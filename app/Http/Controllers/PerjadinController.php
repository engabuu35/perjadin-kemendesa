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

        // --- Update status otomatis: ambil id status dulu dengan aman ---
        $idBelum = DB::table('statusperjadin')->where('nama_status', 'Belum Berlangsung')->value('id');
        $idSedang = DB::table('statusperjadin')->where('nama_status', 'Sedang Berlangsung')->value('id');
        $statusIds = array_values(array_filter([$idBelum, $idSedang]));

        if (!empty($statusIds)) {
            PerjalananDinas::whereIn('id_status', $statusIds)
                ->where('tgl_mulai', '<=', now())
                ->get()
                ->each->updateStatus();
        }

        // --- Hitung peringatan untuk setiap kartu (uraian, surat, konflik) ---
        $missingUraian = [];   // [id_perjadin => [nip,...]]
        $suratMissing   = [];  // [id_perjadin => bool]
        $conflicts      = [];  // [id_perjadin => [nip,...]]

        // Preload all perjadin (untuk cek konflik lebih efisien daripada meng-query per loop)
        // Hanya mengambil id, tgl_mulai, tgl_selesai untuk perjadin lain
        $allPerjadin = PerjalananDinas::select('id', 'tgl_mulai', 'tgl_selesai')->get();

        foreach ($penugasans as $p) {
            // 1) pegawai pada perjalanan ini (nip list)
            $pegs = DB::table('pegawaiperjadin')->where('id_perjadin', $p->id)->pluck('id_user')->toArray();

            // 2) uraian belum lengkap (pivot.laporan_individu null atau kosong)
            $missing = DB::table('pegawaiperjadin')
                ->where('id_perjadin', $p->id)
                ->where(function ($q) {
                    $q->whereNull('laporan_individu')
                      ->orWhereRaw("TRIM(IFNULL(laporan_individu,'')) = ''");
                })
                ->pluck('id_user')
                ->toArray();

            $missingUraian[$p->id] = $missing;

            // 3) cek file surat tugas pada perjalanandinas (sesuaikan nama kolom jika beda)
            $suratMissing[$p->id] = empty($p->surat_tugas);

            // 4) cek konflik: apakah salah satu pegawai punya perjalanan lain yg tanggalnya overlap
            $conflictNips = [];
            if (!empty($pegs)) {
                foreach ($pegs as $nip) {
                    $exists = DB::table('pegawaiperjadin as pp')
                        ->join('perjalanandinas as pd', 'pp.id_perjadin', '=', 'pd.id')
                        ->where('pp.id_user', $nip)
                        ->where('pp.id_perjadin', '<>', $p->id)
                        // overlap condition: other.tgl_mulai <= this.tgl_selesai AND other.tgl_selesai >= this.tgl_mulai
                        ->where('pd.tgl_mulai', '<=', $p->tgl_selesai)
                        ->where('pd.tgl_selesai', '>=', $p->tgl_mulai)
                        ->exists();

                    if ($exists) $conflictNips[] = $nip;
                }
            }
            // uniq & indeks ulang
            $conflicts[$p->id] = array_values(array_unique($conflictNips));
        }

        return view('pic.penugasan', [
            'penugasans'     => $penugasans,
            'q'              => $q,
            'missingUraian'  => $missingUraian,
            'suratMissing'   => $suratMissing,
            'conflicts'      => $conflicts,
        ]);
    }

    public function show($id)
    {
        $userNip = Auth::user()->nip;

        $perjalanan = PerjalananDinas::find($id);
        if (!$perjalanan) abort(404);

        // Update status model jika ada mekanisme transisi otomatis
        if (method_exists($perjalanan, 'updateStatus')) {
            $perjalanan->updateStatus();
            $perjalanan->refresh();
        }

        // 1. CEK STATUS PEGAWAI (INDIVIDU)
        $dataSaya = DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->first();

        if (!$dataSaya) abort(403, 'Akses ditolak.');

        $isMyTaskFinished = isset($dataSaya->is_finished) && $dataSaya->is_finished == 1;

        // 2. DATA GEOTAGGING (riwayat per hari)
        $period = CarbonPeriod::create($perjalanan->tgl_mulai, $perjalanan->tgl_selesai);
        $geotagHistory = [];
        $hariKe = 1;

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

        // 3. URAIAN per-user (gunakan tabel laporan jika tersedia)
        $laporanSaya = LaporanPerjadin::firstOrNew(['id_perjadin' => $id, 'id_user' => $userNip]);

        // 4. VALIDASI TANGGAL & ABSEN UNTUK TOMBOL SELESAI
        $isTodayInPeriod = $today->between($perjalanan->tgl_mulai, $perjalanan->tgl_selesai);
        $isLastDay = $today->isSameDay($perjalanan->tgl_selesai);
        // kasuistik: jika lewat hari selesai (lupa klik), izinkan finish
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
        } elseif ($isPastEnd && !$isTodayInPeriod) {
            $statusMessage = 'Masa tugas sudah lewat.';
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
        $perjalanan = PerjalananDinas::find($id);

        if (!$perjalanan) {
            return back()->with('error', 'Perjalanan tidak ditemukan.');
        }

        $today = Carbon::today();

        // Jika kebijakan: hanya izinkan finish di hari terakhir atau setelah lewat hari selesai
        if (!$today->isSameDay($perjalanan->tgl_selesai) && !$today->gt($perjalanan->tgl_selesai)) {
            return back()->with('error', 'Gagal! Anda hanya bisa menyelesaikan tugas pada tanggal selesai jadwal atau setelahnya.');
        }

        $sudahAbsen = Geotagging::where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->whereDate('created_at', $today)
            ->exists();

        if (!$sudahAbsen && !$today->gt($perjalanan->tgl_selesai)) {
            // jika belum absen dan belum lewat tgl_selesai -> gagal
            return back()->with('error', 'Gagal! Anda belum melakukan Geotagging hari ini.');
        }

        // gunakan transaction untuk konsistensi
        DB::transaction(function () use ($id, $userNip, $perjalanan, &$allCompleted) {
            DB::table('pegawaiperjadin')
                ->where('id_perjadin', $id)
                ->where('id_user', $userNip)
                ->update(['is_finished' => 1]);

            // cek semua selesai?
            $totalPegawai = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->count();
            $totalSelesai = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->where('is_finished', 1)->count();

            $allCompleted = ($totalPegawai > 0 && $totalPegawai == $totalSelesai);

            if ($allCompleted) {
                $idPembuatan = DB::table('statusperjadin')->where('nama_status', 'Pembuatan Laporan')->value('id');
                if ($idPembuatan) {
                    PerjalananDinas::where('id', $id)->update(['id_status' => $idPembuatan]);
                }
            }
        });

        // update status model jika perlu
        $perjalanan->refresh();
        if (method_exists($perjalanan, 'updateStatus')) {
            $perjalanan->updateStatus();
        }

        if (!empty($allCompleted)) {
            return back()->with('success', 'Tugas selesai! Status perjalanan sekarang: Pembuatan Laporan (PIC diharapkan membuat laporan).');
        }

        return back()->with('success', 'Tugas Anda berhasil diselesaikan. Menunggu rekan tim lain selesai.');
    }

    public function storeUraian(Request $request, $id)
    {
        // Validasi input singkat (opsional tambahkan rules lebih ketat)
        $request->validate(['uraian' => 'nullable|string']);

        // Cek hak user di pegawaiperjadin
        $cek = DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', Auth::user()->nip)
            ->first();

        if (!$cek) {
            return back()->with('error', 'Anda tidak terdaftar pada perjalanan ini.');
        }

        if (!empty($cek->is_finished) && $cek->is_finished) {
            return back()->with('error', 'Sudah selesai, tidak bisa edit.');
        }

        // Simpan uraian ke tabel laporan per pegawai (lebih fleksibel)
        $laporan = LaporanPerjadin::firstOrNew(['id_perjadin' => $id, 'id_user' => Auth::user()->nip]);
        $laporan->uraian = $request->uraian;
        $laporan->save();

        // optional: update status perjalanan jika model menangani transisi berdasarkan isi uraian
        $perjalanan = PerjalananDinas::find($id);
        if ($perjalanan && method_exists($perjalanan, 'updateStatus')) {
            $perjalanan->updateStatus();
        }

        return back()->with('success', 'Uraian disimpan.');
    }

    public function tandaiKehadiran(Request $request, $id)
    {
        $request->validate(['latitude' => 'required', 'longitude' => 'required', 'id_tipe' => 'required']);

        $perjalanan = PerjalananDinas::find($id);
        if (!$perjalanan) {
            return response()->json(['status' => 'error', 'message' => 'Perjalanan tidak ditemukan.'], 404);
        }

        $today = Carbon::today();

        // Izinkan absen selama periode tugas (termasuk hari terakhir). Jika kebijakan berbeda, sesuaikan.
        if (!$today->between($perjalanan->tgl_mulai, $perjalanan->tgl_selesai)) {
            return response()->json(['status' => 'error', 'message' => 'Di luar jadwal.'], 403);
        }

        if (Geotagging::where('id_perjadin', $id)
            ->where('id_user', Auth::user()->nip)
            ->whereDate('created_at', $today)
            ->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Sudah absen.'], 400);
        }

        Geotagging::create([
            'id_perjadin' => $id,
            'id_user' => Auth::user()->nip,
            'id_tipe' => $request->id_tipe,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        return response()->json(['status' => 'success', 'message' => 'Hadir!']);
    }
}
