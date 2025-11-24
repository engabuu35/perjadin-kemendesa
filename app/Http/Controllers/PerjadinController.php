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
    // --- HALAMAN UTAMA (INDEX) ---
    public function index(Request $request)
    {
        $q = $request->input('q');
        $query = PerjalananDinas::query();

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('nomor_surat', 'like', "%{$q}%")
                    ->orWhere('tujuan', 'like', "%{$q}%");
            });
        }

        $penugasans = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        return view('pic.penugasan', compact('penugasans', 'q'));
    }

    // --- HALAMAN DETAIL (SHOW) ---
    public function show($id)
    {
        $userNip = Auth::user()->nip;
        $perjalanan = PerjalananDinas::find($id);

        if (!$perjalanan) abort(404);

        // 1. CEK STATUS KUNCI (READ ONLY)
        // Cari ID status "Sedang Berlangsung". Selain status ini, anggap terkunci.
        $statusBerlangsung = DB::table('statusperjadin')->where('nama_status', 'Sedang Berlangsung')->value('id');
        // Jika status bukan "Sedang Berlangsung", maka locked = true
        $isLocked = ($perjalanan->id_status != $statusBerlangsung);

        // Pesan Status Text
        $statusText = DB::table('statusperjadin')->where('id', $perjalanan->id_status)->value('nama_status');

        // 2. LOGIKA GEOTAGGING
        $period = CarbonPeriod::create($perjalanan->tgl_mulai, $perjalanan->tgl_selesai);
        $geotagHistory = [];
        $hariKe = 1;
        $sudahAbsenHariIni = Geotagging::where('id_perjadin', $id)->where('id_user', $userNip)->whereDate('created_at', Carbon::today())->exists();

        foreach ($period as $date) {
            $tag = Geotagging::where('id_perjadin', $id)->where('id_user', $userNip)->whereDate('created_at', $date)->first();
            $geotagHistory[] = [
                'hari_ke' => $hariKe++,
                'tanggal' => $date->format('d M Y'),
                'lokasi'  => $tag ? "Lat: {$tag->latitude}, Long: {$tag->longitude}" : '-',
                'waktu'   => $tag ? Carbon::parse($tag->created_at)->format('H:i') : '-',
                'status'  => $tag ? 'Sudah' : 'Belum'
            ];
        }

        // 3. DATA LAPORAN SAYA
        $laporanSaya = LaporanPerjadin::firstOrNew(['id_perjadin' => $id, 'id_user' => $userNip]);

        // 4. DATA SEMUA PESERTA
        $allPeserta = DB::table('pegawaiperjadin')
            ->join('users', 'users.nip', '=', 'pegawaiperjadin.id_user')
            ->where('pegawaiperjadin.id_perjadin', $id)
            ->select('users.nip', 'users.nama as name', 'pegawaiperjadin.role_perjadin', 'pegawaiperjadin.is_lead')
            ->orderBy('pegawaiperjadin.is_lead', 'desc')
            ->get();

        foreach($allPeserta as $peserta) {
            $laporan = LaporanPerjadin::with('bukti')->where('id_perjadin', $id)->where('id_user', $peserta->nip)->first();
            $peserta->laporan = $laporan;
            $peserta->total_biaya = $laporan ? $laporan->bukti->sum('nominal') : 0;
        }

        // 5. Validasi Tanggal
        $today = Carbon::today();
        $isTodayInPeriod = $today->between($perjalanan->tgl_mulai, $perjalanan->tgl_selesai);
        
        // Status message buat UI
        $statusMessage = '';
        if (!$isTodayInPeriod) {
            if ($today->lt($perjalanan->tgl_mulai)) $statusMessage = 'Belum dimulai.';
            elseif ($today->gt($perjalanan->tgl_selesai)) $statusMessage = 'Sudah selesai.';
        }

        return view('pages.detailperjadin', [
            'perjalanan' => $perjalanan,
            'geotagHistory' => $geotagHistory,
            'sudahAbsenHariIni' => $sudahAbsenHariIni,
            'laporanSaya' => $laporanSaya, 
            'allPeserta' => $allPeserta,
            'isTodayInPeriod' => $isTodayInPeriod,
            'isLocked' => $isLocked, 
            'statusText' => $statusText,
            'statusMessage' => $statusMessage
        ]);
    }

    // --- FITUR BARU: FINALISASI PERJALANAN ---
    public function selesaikanPerjadin(Request $request, $id)
    {
        $idMenungguVerif = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi Laporan')->value('id');

        $update = PerjalananDinas::where('id', $id)->update([
            'id_status' => $idMenungguVerif
        ]);

        if($update) {
            return back()->with('success', 'Laporan berhasil dikirim! Status perjalanan kini Menunggu Verifikasi.');
        }
        return back()->with('error', 'Gagal mengupdate status.');
    }

    // --- SIMPAN BUKTI ---
    public function storeBukti(Request $request, $id) {
        // Cek Kunci
        $perjalanan = PerjalananDinas::find($id);
        $statusBerlangsung = DB::table('statusperjadin')->where('nama_status', 'Sedang Berlangsung')->value('id');
        if($perjalanan->id_status != $statusBerlangsung) return back()->with('error', 'Perjalanan sudah dikunci/selesai.');
        
        $request->validate([
            'target_nip' => 'required', 'kategori' => 'required', 'nominal' => 'required|min:0', 'bukti' => 'nullable|max:5120'
        ]);

        $laporan = LaporanPerjadin::firstOrCreate(['id_perjadin' => $id, 'id_user' => $request->target_nip]);
        
        $path = null;
        $filename = null;
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . $request->target_nip . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_perjadin', $filename, 'public');
        }

        BuktiLaporan::create([
            'id_laporan' => $laporan->id, 'nama_file' => $filename, 'path_file' => $path, 
            'kategori' => $request->kategori, 'nominal' => $request->nominal
        ]);
        return back()->with('success', 'Data biaya berhasil ditambahkan.');
    }

    // --- SIMPAN URAIAN ---
    public function storeUraian(Request $request, $id) {
        // Cek Kunci
        $perjalanan = PerjalananDinas::find($id);
        $statusBerlangsung = DB::table('statusperjadin')->where('nama_status', 'Sedang Berlangsung')->value('id');
        if($perjalanan->id_status != $statusBerlangsung) return back()->with('error', 'Perjalanan sudah dikunci/selesai.');

        $laporan = LaporanPerjadin::firstOrNew(['id_perjadin' => $id, 'id_user' => Auth::user()->nip]);
        $laporan->uraian = $request->uraian;
        
        // Logika draft/final individu (opsional)
        if ($request->action_type == 'finish') {
             if (empty($request->uraian) || strlen($request->uraian) < 20) return back()->with('error', 'Uraian terlalu pendek.');
             $laporan->is_final = true;
        }
        $laporan->save();
        return back()->with('success', 'Uraian berhasil disimpan.');
    }
    
    // --- HAPUS BUKTI ---
    public function deleteBukti($idBukti) {
       $bukti = BuktiLaporan::find($idBukti);
       if(!$bukti) return back();

       // Cek Kunci sebelum hapus
       $laporan = LaporanPerjadin::find($bukti->id_laporan);
       $perjalanan = PerjalananDinas::find($laporan->id_perjadin);
       $statusBerlangsung = DB::table('statusperjadin')->where('nama_status', 'Sedang Berlangsung')->value('id');
       if($perjalanan->id_status != $statusBerlangsung) return back()->with('error', 'Tidak bisa menghapus data yang sudah dikunci.');

       if($bukti->path_file && Storage::disk('public')->exists($bukti->path_file)) {
            Storage::disk('public')->delete($bukti->path_file);
       }
       $bukti->delete();
       return back()->with('success', 'Bukti dihapus.');
    }
    
    // --- GEOTAGGING ---
    public function tandaiKehadiran(Request $request, $id) {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'latitude' => 'required', 'longitude' => 'required', 'id_tipe' => 'required'
        ]);
        if ($validator->fails()) return response()->json(['status' => 'error', 'message' => 'Data invalid'], 422);

        // Cek Tanggal
        $perjalanan = PerjalananDinas::find($id);
        $today = Carbon::today();
        if (!$today->between($perjalanan->tgl_mulai, $perjalanan->tgl_selesai)) {
            return response()->json(['status' => 'error', 'message' => 'Di luar jadwal dinas.'], 403);
        }

        // Cek Duplikat Harian
        if (Geotagging::where('id_perjadin', $id)->where('id_user', Auth::user()->nip)->whereDate('created_at', $today)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Sudah absen hari ini.'], 400);
        }

        Geotagging::create([
            'id_perjadin' => $id, 'id_user' => Auth::user()->nip,
            'id_tipe' => $request->id_tipe, 'latitude' => $request->latitude, 'longitude' => $request->longitude,
        ]);
        return response()->json(['status'=>'success', 'message' => 'Hadir!']);
    }

}