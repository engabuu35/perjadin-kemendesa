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
    // ... (Fungsi index dan show tidak berubah drastis, fokus ke selesaikanTugasSaya)
    // Saya sertakan full class agar aman
    
    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = PerjalananDinas::query();

        if ($q) {
            $query->where('nomor_surat', 'like', "%$q%")
                ->orWhere('tujuan', 'like', "%$q%");
        }
        $penugasans = $query->orderBy('tgl_mulai', 'desc')->paginate(10);
        return view('pic.penugasan', ['penugasans' => $penugasans, 'q' => $q]);
    }

    public function show($id)
    {
        $userNip = Auth::user()->nip;
        $perjalanan = PerjalananDinas::find($id);

        if (!$perjalanan) abort(404);

        // 1. CEK STATUS PEGAWAI (INDIVIDU)
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
        
        $today = Carbon::today();
        // Cek absen hari ini
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
        $isLastDay = $today->isSameDay($perjalanan->tgl_selesai);
        
        // Logika Tombol Selesai:
        // Harus hari terakhir DAN sudah absen hari ini
        // ATAU tanggal hari ini sudah MELEWATI tanggal selesai (kasus lupa klik)
        $isPastEnd = $today->gt($perjalanan->tgl_selesai);
        
        $canFinish = ($isLastDay && $sudahAbsenHariIni) || $isPastEnd;

        $finishMessage = '';
        if ($isMyTaskFinished) {
            $finishMessage = 'Anda sudah menyelesaikan tugas ini.';
        } elseif (!$isLastDay && !$isPastEnd) {
            $finishMessage = 'Tombol penyelesaian hanya aktif pada tanggal terakhir tugas.';
        } elseif ($isLastDay && !$sudahAbsenHariIni) {
            $finishMessage = 'Anda belum melakukan Geotagging hari ini.';
        }

        $statusMessage = '';
        if (!$isTodayInPeriod && !$isPastEnd) {
             $statusMessage = 'Belum dimulai.';
        }

        return view('pages.detailperjadin', [
            'perjalanan' => $perjalanan,
            'geotagHistory' => $geotagHistory,
            'sudahAbsenHariIni' => $sudahAbsenHariIni,
            'laporanSaya' => $laporanSaya,
            'isTodayInPeriod' => $isTodayInPeriod,
            'statusMessage' => $statusMessage,
            'isMyTaskFinished' => $isMyTaskFinished,
            'canFinish' => $canFinish,
            'finishMessage' => $finishMessage
        ]);
    }

    public function selesaikanTugasSaya(Request $request, $id)
    {
        $userNip = Auth::user()->nip;
        
        // 1. Update status pegawai ini jadi finished
        DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->update(['is_finished' => 1]);

        // 2. Cek apakah SEMUA pegawai dalam tim sudah selesai?
        $totalPegawai = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->count();
        $totalSelesai = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->where('is_finished', 1)->count();

        if ($totalPegawai == $totalSelesai) {
            // JIKA SEMUA SUDAH SELESAI -> Update Status Perjadin jadi "Menunggu Verifikasi Laporan"
            // Ini akan mentrigger item ini muncul di Dashboard PIC
            
            $statusNext = DB::table('statusperjadin')
                ->where('nama_status', 'Menunggu Verifikasi Laporan')
                ->value('id');

            if ($statusNext) {
                PerjalananDinas::where('id', $id)->update(['id_status' => $statusNext]);
                return back()->with('success', 'Tugas Anda selesai! Karena semua tim sudah selesai, laporan kini diteruskan ke PIC.');
            }
        }

        return back()->with('success', 'Tugas Anda berhasil diselesaikan. Menunggu rekan tim lain selesai.');
    }

    public function storeUraian(Request $request, $id) {
        $cek = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->where('id_user', Auth::user()->nip)->first();
        if($cek->is_finished) return back()->with('error', 'Tugas sudah selesai, tidak bisa edit uraian.');

        $laporan = LaporanPerjadin::firstOrNew(['id_perjadin' => $id, 'id_user' => Auth::user()->nip]);
        $laporan->uraian = $request->uraian;
        $laporan->save();
        return back()->with('success', 'Uraian disimpan.');
    }
    
    public function tandaiKehadiran(Request $request, $id) {
        $request->validate(['latitude'=>'required','longitude'=>'required','id_tipe'=>'required']);
        $perjalanan = PerjalananDinas::find($id);
        $today = Carbon::today();
        
        // Izinkan absen jika hari ini <= tanggal selesai (menghindari error jika telat absen di hari terakhir)
        if ($today->gt($perjalanan->tgl_selesai)) {
             return response()->json(['status'=>'error', 'message'=>'Masa tugas sudah lewat.'], 403);
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