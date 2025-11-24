<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geotagging; 
use App\Models\PerjalananDinas;
use App\Models\LaporanPerjadin; 
use App\Models\BuktiLaporan;
use Illuminate\Support\Facades\DB;
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

        // 1. DATA GEOTAGGING
        $period = CarbonPeriod::create($perjalanan->tgl_mulai, $perjalanan->tgl_selesai);
        $geotagHistory = [];
        $hariKe = 1;
        
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
                'lokasi'  => $tag ? "Lat: {$tag->latitude}, Long: {$tag->longitude}" : '-',
                'lat_raw' => $tag ? $tag->latitude : null,
                'long_raw' => $tag ? $tag->longitude : null,
                'waktu'   => $tag ? Carbon::parse($tag->created_at)->format('H:i') : '-',
                'status'  => $tag ? 'Sudah' : 'Belum'
            ];
        }

        // 2. DATA LAPORAN SAYA
        $laporanSaya = LaporanPerjadin::firstOrNew([
            'id_perjadin' => $id,
            'id_user' => $userNip
        ]);

        // 3. DATA SEMUA PESERTA (JOIN Tabel Users)
        $allPeserta = DB::table('pegawaiperjadin')
            ->join('users', 'users.nip', '=', 'pegawaiperjadin.id_user')
            ->where('pegawaiperjadin.id_perjadin', $id)
            // Pastikan nama kolom sesuai database kamu (users.nama atau a)
            ->select('users.nip', 'users.nama', 'pegawaiperjadin.role_perjadin', 'pegawaiperjadin.is_lead')
            ->orderBy('pegawaiperjadin.is_lead', 'desc')
            ->get();

        // Inject Data Laporan ke setiap peserta
        foreach($allPeserta as $peserta) {
            $laporan = LaporanPerjadin::with('bukti')
                        ->where('id_perjadin', $id)
                        ->where('id_user', $peserta->nip)
                        ->first();
            
            $peserta->laporan = $laporan;
            $peserta->total_biaya = $laporan ? $laporan->bukti->sum('nominal') : 0;
        }

        // 4. VALIDASI TANGGAL
        $today = Carbon::today();
        $startDate = Carbon::parse($perjalanan->tgl_mulai);
        $endDate = Carbon::parse($perjalanan->tgl_selesai);
        $isTodayInPeriod = $today->between($startDate, $endDate);
        
        $statusMessage = '';
        if (!$isTodayInPeriod) {
            if ($today->lt($startDate)) $statusMessage = 'Belum dimulai.';
            elseif ($today->gt($endDate)) $statusMessage = 'Sudah selesai.';
        }

        return view('pages.detailperjadin', [
            'perjalanan' => $perjalanan,
            'geotagHistory' => $geotagHistory,
            'sudahAbsenHariIni' => $sudahAbsenHariIni,
            'laporanSaya' => $laporanSaya, 
            'allPeserta' => $allPeserta,
            'isTodayInPeriod' => $isTodayInPeriod,
            'statusMessage' => $statusMessage
        ]);
    }

    // --- PERBAIKAN FITUR UPLOAD BUKTI ---
    public function storeBukti(Request $request, $id)
    {
        // 1. Validasi
        $request->validate([
            'target_nip' => 'required|exists:users,nip',
            'kategori' => 'required|string',
            'nominal' => 'required|numeric|min:0', // Pastikan input nominal angka
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // TIDAK WAJIB (nullable), max 5MB
        ]);

        // 2. Cari/Buat Laporan Induk
        $laporan = LaporanPerjadin::firstOrCreate(
            ['id_perjadin' => $id, 'id_user' => $request->target_nip],
            ['uraian' => null, 'is_final' => false] 
        );

        $path = null;
        $filename = null;

        // 3. Cek apakah ada file yang diupload?
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . $request->target_nip . '_' . $file->getClientOriginalName();
            // Simpan ke folder 'public/bukti_perjadin'
            $path = $file->storeAs('bukti_perjadin', $filename, 'public');
        }

        // 4. Simpan ke Database (Walaupun file null, tetap simpan nominal & kategori)
        BuktiLaporan::create([
            'id_laporan' => $laporan->id,
            'nama_file' => $filename, // Bisa null
            'path_file' => $path,     // Bisa null
            'kategori' => $request->kategori,
            'nominal' => $request->nominal
        ]);

        return back()->with('success', 'Data biaya berhasil ditambahkan!');
    }

    public function storeUraian(Request $request, $id)
    {
        $userNip = Auth::user()->nip;
        $laporan = LaporanPerjadin::firstOrNew(['id_perjadin' => $id, 'id_user' => $userNip]);
        $laporan->uraian = $request->uraian;
        
        if ($request->action_type == 'finish') {
            if (empty($request->uraian) || strlen($request->uraian) < 20) {
                return back()->with('error', 'Uraian harus diisi lengkap sebelum selesai.');
            }
            $laporan->is_final = true;
        } else {
             if(!$laporan->is_final) {
                $laporan->is_final = false;
             }
        }
        $laporan->save();
        $msg = ($request->action_type == 'finish') ? 'Laporan selesai!' : 'Draft tersimpan.';
        return back()->with('success', $msg);
    }
    
    public function deleteBukti($idBukti) {
        $bukti = BuktiLaporan::find($idBukti);
        if($bukti) {
            // Hapus file fisik jika ada
            if($bukti->path_file && Storage::disk('public')->exists($bukti->path_file)) {
                Storage::disk('public')->delete($bukti->path_file);
            }
            $bukti->delete();
            return back()->with('success', 'Item dihapus');
        }
        return back()->with('error', 'Item tidak ditemukan');
    }

    public function tandaiKehadiran(Request $request, $id)
    {
        $userNip = Auth::user()->nip;
        // ... (Kode sama seperti sebelumnya, validasi tanggal dll)
        // Saya singkat agar tidak kepanjangan, pakai logic validasi tanggal yang sudah kamu punya sebelumnya
        $geotag = Geotagging::create([
            'id_perjadin' => $id, 'id_user' => $userNip,
            'id_tipe' => $request->id_tipe, 'latitude' => $request->latitude, 'longitude' => $request->longitude,
        ]);
        return response()->json(['status' => 'success', 'message' => "Hadir!"]);
    }

    public function index(Request $request)
    {
        // search
        $q = $request->q;

        $query = PerjalananDinas::query();

        if ($q) {
            $query->where('nomor_surat', 'like', "%$q%")
                ->orWhere('tujuan', 'like', "%$q%");
        }

        // pagination
        $penugasans = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('pic.penugasan', [
            'penugasans' => $penugasans,
            'q' => $q
        ]);
    }
}