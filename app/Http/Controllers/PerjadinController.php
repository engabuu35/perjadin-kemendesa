<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geotagging; 
use App\Models\PerjalananDinas;
use App\Models\LaporanPerjadin; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PerjadinController extends Controller
{   
    public function index(Request $request)
    {
        $q = $request->query('q');

        $query = PerjalananDinas::query();

        if ($q) {
            $query->where('nomor_surat', 'like', "%$q%")
                ->orWhere('tujuan', 'like', "%$q%");
        }

        $penugasans = $query->orderBy('tgl_mulai', 'desc')->paginate(10);

        return view('pic.penugasan', [
            'penugasans' => $penugasans,
            'q' => $q
        ]);
    }

    public function show($id)
    {
        $userNip = Auth::user()->nip;
        $perjalanan = PerjalananDinas::find($id);

        if (!$perjalanan) abort(404);

        // 1. CEK STATUS SAYA
        $dataSaya = DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->first();

        if (!$dataSaya) abort(403, 'Akses ditolak.');

        $isMyTaskFinished = $dataSaya->is_finished == 1;

        // 2. DATA GEOTAGGING
        $period = CarbonPeriod::create($perjalanan->tgl_mulai, $perjalanan->tgl_selesai);
        $geotagHistory = [];
        $hariKe = 1;
        
        // Cek Absen HARI INI
        $today = Carbon::today();
        $sudahAbsenHariIni = Geotagging::where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->whereDate('created_at', $today)
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
                'waktu'   => $tag ? Carbon::parse($tag->created_at)->format('H:i') : '-',
                'status'  => $tag ? 'Sudah' : 'Belum'
            ];
        }

        // 3. URAIAN
        $laporanSaya = LaporanPerjadin::firstOrNew(['id_perjadin' => $id, 'id_user' => $userNip]);

        // 4. VALIDASI TANGGAL & ABSEN UNTUK TOMBOL SELESAI
        $isTodayInPeriod = $today->between($perjalanan->tgl_mulai, $perjalanan->tgl_selesai);
        
        // [LOGIKA BARU]
        // Cek apakah hari ini adalah HARI TERAKHIR?
        $isLastDay = $today->isSameDay($perjalanan->tgl_selesai);
        
        // Syarat tombol aktif:
        // 1. Hari ini adalah hari terakhir
        // 2. Sudah absen di hari terakhir ini
        $canFinish = $isLastDay && $sudahAbsenHariIni;

        // Pesan Error untuk tombol
        $finishMessage = '';
        if (!$isLastDay) {
            $finishMessage = 'Tombol penyelesaian hanya aktif pada tanggal selesai tugas (' . Carbon::parse($perjalanan->tgl_selesai)->format('d M Y') . ').';
        } elseif (!$sudahAbsenHariIni) {
            $finishMessage = 'Anda belum melakukan Geotagging hari ini. Harap tandai lokasi terlebih dahulu.';
        }

        $statusMessage = '';
        if (!$isTodayInPeriod) {
            if ($today->lt($perjalanan->tgl_mulai)) $statusMessage = 'Belum dimulai.';
            elseif ($today->gt($perjalanan->tgl_selesai)) $statusMessage = 'Masa tugas lewat.';
        }

        return view('pages.detailperjadin', [
            'perjalanan' => $perjalanan,
            'geotagHistory' => $geotagHistory,
            'sudahAbsenHariIni' => $sudahAbsenHariIni,
            'laporanSaya' => $laporanSaya,
            'isTodayInPeriod' => $isTodayInPeriod,
            'statusMessage' => $statusMessage,
            'isMyTaskFinished' => $isMyTaskFinished,
            // Variable Baru
            'canFinish' => $canFinish,
            'finishMessage' => $finishMessage
        ]);
    }

    public function selesaikanTugasSaya(Request $request, $id)
    {
        $userNip = Auth::user()->nip;
        $perjalanan = PerjalananDinas::find($id);
        $today = Carbon::today();

        // VALIDASI BACKEND (Security)
        if (!$today->isSameDay($perjalanan->tgl_selesai)) {
            return back()->with('error', 'Gagal! Anda hanya bisa menyelesaikan tugas pada tanggal selesai jadwal.');
        }
        
        $sudahAbsen = Geotagging::where('id_perjadin', $id)->where('id_user', $userNip)->whereDate('created_at', $today)->exists();
        if (!$sudahAbsen) {
            return back()->with('error', 'Gagal! Anda belum melakukan Geotagging hari ini.');
        }

        // 1. Update Status Individu
        DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->update(['is_finished' => 1]);

        // 2. Cek Semua Selesai?
        $totalPegawai = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->count();
        $totalSelesai = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->where('is_finished', 1)->count();

        if ($totalPegawai == $totalSelesai) {
            $idMenungguVerif = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi Laporan')->value('id');
            PerjalananDinas::where('id', $id)->update(['id_status' => $idMenungguVerif]);
            return back()->with('success', 'Tugas selesai! Status perjalanan kini diteruskan ke PIC.');
        }

        return back()->with('success', 'Tugas Anda berhasil diselesaikan.');
    }

    public function storeUraian(Request $request, $id) {
        $cek = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->where('id_user', Auth::user()->nip)->first();
        if($cek->is_finished) return back()->with('error', 'Sudah selesai, tidak bisa edit.');

        $laporan = LaporanPerjadin::firstOrNew(['id_perjadin' => $id, 'id_user' => Auth::user()->nip]);
        $laporan->uraian = $request->uraian;
        $laporan->save();
        return back()->with('success', 'Uraian disimpan.');
    }
    
    public function tandaiKehadiran(Request $request, $id) {
        $request->validate(['latitude'=>'required','longitude'=>'required','id_tipe'=>'required']);
        
        $perjalanan = PerjalananDinas::find($id);
        $today = Carbon::today();
        
        if (!$today->between($perjalanan->tgl_mulai, $perjalanan->tgl_selesai)) {
            return response()->json(['status'=>'error', 'message'=>'Di luar jadwal.'], 403);
        }
        if (Geotagging::where('id_perjadin', $id)->where('id_user', Auth::user()->nip)->whereDate('created_at', $today)->exists()) {
            return response()->json(['status'=>'error', 'message'=>'Sudah absen.'], 400);
        }

        Geotagging::create([
            'id_perjadin' => $id, 'id_user' => Auth::user()->nip,
            'id_tipe' => $request->id_tipe, 'latitude' => $request->latitude, 'longitude' => $request->longitude
        ]);
        return response()->json(['status'=>'success', 'message'=>'Hadir!']);
    }
}