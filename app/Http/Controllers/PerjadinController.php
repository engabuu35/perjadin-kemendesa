<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geotagging; 
use App\Models\PerjalananDinas;
use App\Models\LaporanPerjadin; 
use App\Models\BuktiLaporan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PerjadinController extends Controller
{
    public function show($id)
    {
        $userNip = Auth::user()->nip;
        $perjalanan = PerjalananDinas::find($id);

        if (!$perjalanan) {
            abort(404, 'Perjalanan dinas tidak ditemukan');
        }

        // --- LOGIKA 1: SIAPKAN DATA GEOTAGGING HARIAN ---
        $period = CarbonPeriod::create($perjalanan->tgl_mulai, $perjalanan->tgl_selesai);
        $geotagHistory = [];
        $hariKe = 1;
        
        // Cek apakah hari ini user SUDAH absen?
        $sudahAbsenHariIni = Geotagging::where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->whereDate('created_at', Carbon::today())
            ->exists();

        foreach ($period as $date) {
            $tag = Geotagging::where('id_perjadin', $id)
                ->where('id_user', $userNip)
                ->whereDate('created_at', $date)
                ->first();

            $geotagHistory[] = [
                'hari_ke' => $hariKe++,
                'tanggal' => $date->format('d M Y'),
                'raw_date' => $date->format('Y-m-d'), 
                'lokasi'  => $tag ? "Lat: {$tag->latitude}, Long: {$tag->longitude}" : '-',
                'lat_raw' => $tag ? $tag->latitude : null,
                'long_raw' => $tag ? $tag->longitude : null,
                'waktu'   => $tag ? Carbon::parse($tag->created_at)->format('H:i') : '-',
                'status'  => $tag ? 'Sudah' : 'Belum'
            ];
        }

        $laporan = LaporanPerjadin::with('bukti')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->first();

        $isLastDayPassed = Carbon::now()->gte(Carbon::parse($perjalanan->tgl_selesai));

        // [BARU] VALIDASI TANGGAL UNTUK TOMBOL
        $today = Carbon::today();
        $startDate = Carbon::parse($perjalanan->tgl_mulai);
        $endDate = Carbon::parse($perjalanan->tgl_selesai);
        
        // Cek apakah hari ini ada di dalam rentang jadwal?
        $isTodayInPeriod = $today->between($startDate, $endDate);
        
        // Pesan status untuk UI
        $statusMessage = '';
        if (!$isTodayInPeriod) {
            if ($today->lt($startDate)) {
                $statusMessage = 'Perjalanan dinas belum dimulai.';
            } elseif ($today->gt($endDate)) {
                $statusMessage = 'Perjalanan dinas sudah selesai.';
            }
        }

        return view('pages.detailperjadin', [
            'perjalanan' => $perjalanan,
            'geotagHistory' => $geotagHistory,
            'sudahAbsenHariIni' => $sudahAbsenHariIni,
            'laporan' => $laporan,
            'isLastDayPassed' => $isLastDayPassed,
            // Kirim variabel baru ke View
            'isTodayInPeriod' => $isTodayInPeriod,
            'statusMessage' => $statusMessage
        ]);
    }

    public function tandaiKehadiran(Request $request, $id)
    {
        // 1. Validasi Dasar
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'id_tipe' => 'required|integer',
        ]);

        if ($validator->fails()) return response()->json(['status' => 'error', 'message' => 'Data tidak valid'], 422);

        // 2. [BARU] Validasi Rentang Tanggal (Security Check)
        $perjalanan = PerjalananDinas::find($id);
        if (!$perjalanan) return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);

        $today = Carbon::today();
        $startDate = Carbon::parse($perjalanan->tgl_mulai);
        $endDate = Carbon::parse($perjalanan->tgl_selesai);

        if (!$today->between($startDate, $endDate)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal! Tanggal hari ini berada di luar jadwal perjalanan dinas.'
            ], 403); // 403 Forbidden
        }

        $userNip = Auth::user()->nip;

        // 3. Cek Double Absen
        $cekHarian = Geotagging::where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->whereDate('created_at', Carbon::today())
            ->exists();

        if ($cekHarian) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan tagging lokasi hari ini.'
            ], 400);
        }

        try {
            $geotag = Geotagging::create([
                'id_perjadin' => $id,
                'id_user'     => $userNip,
                'id_tipe'     => $request->id_tipe,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
            ]);
            
            return response()->json([
                'status' => 'success', 
                'message' => "Kehadiran hari ini berhasil dicatat!",
                'data' => $geotag
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal simpan database'], 500);
        }
    }

    // ... (Method storeLaporan & deleteBukti tetap sama) ...
    public function storeLaporan(Request $request, $id)
    {
        $userNip = Auth::user()->nip;
        $laporan = LaporanPerjadin::firstOrNew(['id_perjadin' => $id, 'id_user' => $userNip]);
        $laporan->uraian = $request->uraian;
        
        if ($request->action_type == 'finish') {
            if (empty($request->uraian) || strlen($request->uraian) < 20) {
                return back()->with('error', 'Uraian harus diisi lengkap.');
            }
            $laporan->is_final = true;
        } else {
            $laporan->is_final = false;
        }
        $laporan->save();

        if ($request->hasFile('bukti')) {
            $files = $request->file('bukti');
            $kategoris = $request->kategori ?? [];
            foreach ($files as $index => $file) {
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('bukti_perjadin', $filename, 'public');
                    BuktiLaporan::create([
                        'id_laporan' => $laporan->id,
                        'nama_file' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'kategori' => $kategoris[$index] ?? 'Umum',
                        // [TAMBAHAN] Simpan nominal ke database
                        // Jika user lupa isi, otomatis set ke 0
                        'nominal' => $nominals[$index] ?? 0 
                    ]);
                }
            }
        }
        return back()->with('success', 'Laporan berhasil disimpan.');
    }
    
    public function deleteBukti($idBukti) {
        $bukti = BuktiLaporan::find($idBukti);
        if($bukti) {
            Storage::disk('public')->delete($bukti->path_file);
            $bukti->delete();
            return back()->with('success', 'Bukti dihapus');
        }
        return back()->with('error', 'Bukti tidak ditemukan');
    }

    /**
     * Tampilkan daftar penugasan perjalanan dinas (index).
     */
    public function index(Request $request)
    {
        // optional: search sederhana berdasarkan nomor_surat atau tujuan
        $q = $request->input('q');

        $query = PerjalananDinas::query();

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('nomor_surat', 'like', "%{$q}%")
                    ->orWhere('tujuan', 'like', "%{$q}%");
            });
        }

        // ambil data dengan pagination
        $penugasans = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // kita kirim ke view; view akan melakukan mapping warna/status presentation
        return view('pic.penugasan', compact('penugasans', 'q'));
    }
}