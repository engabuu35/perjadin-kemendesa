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
    /**
     * LIST PENUGASAN
     * Tidak ada auto-update status di sini.
     */
    public function index(Request $request)
    {
        $q = $request->query('q');

        $penugasans = PerjalananDinas::when($q, function ($qr) use ($q) {
                $qr->where('nomor_surat', 'like', "%$q%")
                   ->orWhere('tujuan', 'like', "%$q%");
            })
            ->orderBy('tgl_mulai', 'desc')
            ->paginate(10);

        $missingUraian = [];
        $suratMissing  = [];
        $conflicts     = [];

        $allPerjadin = PerjalananDinas::select('id', 'tgl_mulai', 'tgl_selesai')->get();

        foreach ($penugasans as $p) {

            $pegs = DB::table('pegawaiperjadin')
                ->where('id_perjadin', $p->id)
                ->pluck('id_user')
                ->toArray();

            $missingUraian[$p->id] = DB::table('pegawaiperjadin')
                ->where('id_perjadin', $p->id)
                ->where(function ($q) {
                    $q->whereNull('laporan_individu')
                      ->orWhereRaw("TRIM(IFNULL(laporan_individu,'')) = ''");
                })
                ->pluck('id_user')
                ->toArray();

            $suratMissing[$p->id] = empty($p->surat_tugas);

            $conflictNips = [];
            foreach ($pegs as $nip) {
                $exists = DB::table('pegawaiperjadin as pp')
                    ->join('perjalanandinas as pd', 'pp.id_perjadin', '=', 'pd.id')
                    ->where('pp.id_user', $nip)
                    ->where('pp.id_perjadin', '<>', $p->id)
                    ->where('pd.tgl_mulai', '<=', $p->tgl_selesai)
                    ->where('pd.tgl_selesai', '>=', $p->tgl_mulai)
                    ->exists();

                if ($exists) $conflictNips[] = $nip;
            }

            $conflicts[$p->id] = array_values(array_unique($conflictNips));
        }

        return view('pic.penugasan', compact(
            'penugasans', 'q', 'missingUraian', 'suratMissing', 'conflicts'
        ));
    }

    /**
     * DETAIL PERJALANAN
     * Update status DIIZINKAN sekali saat halaman ini dibuka.
     */
    public function show($id)
    {
        $userNip = Auth::user()->nip;
        $perjalanan = PerjalananDinas::findOrFail($id);

        if (method_exists($perjalanan, 'updateStatus')) {
            $perjalanan->updateStatus();
            $perjalanan->refresh();
        }

        $dataSaya = DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->first();

        if (!$dataSaya) abort(403);

        $isMyTaskFinished = $dataSaya->is_finished == 1;

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

        $laporanSaya = LaporanPerjadin::firstOrNew([
            'id_perjadin' => $id,
            'id_user'     => $userNip
        ]);

        $isTodayInPeriod = $today->between($perjalanan->tgl_mulai, $perjalanan->tgl_selesai);
        $isLastDay = $today->isSameDay($perjalanan->tgl_selesai);
        $isPastEnd = $today->gt($perjalanan->tgl_selesai);

        $canFinish = ($isLastDay && $sudahAbsenHariIni) || $isPastEnd;

        // ---------- finishMessage: alasan tombol belum aktif ----------
        $finishMessage = null;
        if (!$canFinish) {
            if ($today->lt($perjalanan->tgl_mulai)) {
                $finishMessage = 'Belum bisa diselesaikan: tugas belum dimulai (mulai ' . Carbon::parse($perjalanan->tgl_mulai)->format('d M Y') . ').';
            } elseif ($today->gt($perjalanan->tgl_selesai)) {
                // meskipun lewat tanggal selesai, canFinish akan true karena isPastEnd => jarang sampai sini,
                // tetap sediakan fallback
                $finishMessage = 'Periode tugas telah berakhir pada ' . Carbon::parse($perjalanan->tgl_selesai)->format('d M Y') . '.';
            } elseif (!$isLastDay && !$isPastEnd) {
                $finishMessage = 'Tombol hanya aktif pada hari terakhir tugas atau setelah tugas selesai.';
            } elseif ($isLastDay && !$sudahAbsenHariIni) {
                $finishMessage = 'Hari ini hari terakhir tugas, tetapi Anda belum melakukan geotagging hari ini.';
            } else {
                $finishMessage = 'Belum memenuhi syarat untuk menyelesaikan tugas.';
            }
        }
        // ---------------------------------------------------------------

        // ----- tambahan: status message + badge -----
        if (!$isTodayInPeriod) {
            if ($today->lt($perjalanan->tgl_mulai)) {
                $statusMessage = 'Tugas belum dimulai. Periode: ' . Carbon::parse($perjalanan->tgl_mulai)->format('d M Y') . ' — ' . Carbon::parse($perjalanan->tgl_selesai)->format('d M Y') . '.';
            } else {
                $statusMessage = 'Periode tugas telah berakhir pada ' . Carbon::parse($perjalanan->tgl_selesai)->format('d M Y') . '.';
            }
        } else {
            $statusMessage = null;
        }

        if ($isMyTaskFinished) {
            $statusPegawai = 'Selesai';
            $statusBadgeClass = 'bg-gray-100 text-gray-700 border border-gray-200';
        } elseif ($sudahAbsenHariIni) {
            $statusPegawai = 'Hadir Hari Ini';
            $statusBadgeClass = 'bg-green-100 text-green-700 border border-green-200';
        } elseif ($isTodayInPeriod) {
            $statusPegawai = 'Dalam Perjalanan';
            $statusBadgeClass = 'bg-blue-50 text-blue-700 border border-blue-100';
        } else {
            $statusPegawai = 'Belum Hadir';
            $statusBadgeClass = 'bg-yellow-50 text-yellow-700 border border-yellow-100';
        }
        // --------------------------------------------

        return view('pages.detailperjadin', compact(
            'perjalanan',
            'geotagHistory',
            'sudahAbsenHariIni',
            'laporanSaya',
            'isTodayInPeriod',
            'isMyTaskFinished',
            'canFinish',
            'statusMessage',
            'statusPegawai',
            'statusBadgeClass',
            'finishMessage'  // <-- kirim finishMessage ke view
        ));
    }


    /**
     * FINISH TUGAS SAYA
     * Tidak update status langsung — model yang menangani.
     */
    public function selesaikanTugasSaya(Request $request, $id)
    {
        $userNip = Auth::user()->nip;
        $perjalanan = PerjalananDinas::findOrFail($id);
        $today = Carbon::today();

        // 1. Validasi Waktu
        if (!$today->isSameDay($perjalanan->tgl_selesai) && !$today->gt($perjalanan->tgl_selesai)) {
            return back()->with('error', 'Hanya bisa diselesaikan pada atau setelah tanggal selesai.');
        }

        // 2. Validasi Uraian Kegiatan
        $laporan = LaporanPerjadin::where('id_perjadin', $id)->where('id_user', $userNip)->first();
        
        if (!$laporan || empty($laporan->uraian)) {
            return back()->with('error', 'Anda belum mengisi uraian kegiatan. Silakan isi terlebih dahulu.');
        }

        $jumlahKata = str_word_count(strip_tags($laporan->uraian));
        if ($jumlahKata < 100) {
            $kurang = 100 - $jumlahKata;
            return back()->with('error', "Uraian kegiatan belum cukup detail. Minimal 100 kata. Anda menulis {$jumlahKata} kata (kurang {$kurang} kata lagi).");
        }

        // 3. Update status pegawai ini jadi finished
        DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->update(['is_finished' => 1]);

        // 4. Cek apakah SEMUA pegawai dalam tim sudah selesai?
        $totalPegawai = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->count();
        $totalSelesai = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->where('is_finished', 1)->count();

        if ($totalPegawai == $totalSelesai) {
            // JIKA SEMUA SELESAI -> Masuk ke Dashboard PIC
            
            // PERBAIKAN DI SINI:
            // Prioritas 1: Cari 'Pembuatan Laporan' (Agar statusnya jadi ID 3)
            $statusNext = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi Laporan')->value('id');
            
            // Prioritas 2: Fallback ke 'Menunggu Verifikasi Laporan' jika yang atas tidak ada
            // if (!$statusNext) {
            //     $statusNext = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi Laporan')->value('id');
            // }

            if ($statusNext) {
                $perjalanan->update(['id_status' => $statusNext]);
                return back()->with('success', 'Tugas Anda selesai! Laporan diteruskan ke PIC (Status: Pembuatan Laporan).');
            }
        }

        return back()->with('success', 'Tugas Anda selesai. Menunggu rekan tim lain.');
    }

    /**
     * SIMPAN URAIAN PER USER
     */
    public function storeUraian(Request $request, $id)
    {
        $request->validate(['uraian' => 'nullable|string']);

        $nip = Auth::user()->nip;

        $cek = DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $nip)
            ->first();

        if (!$cek) {
            return back()->with('error', 'Anda tidak terdaftar pada perjalanan ini.');
        }

        if ($cek->is_finished) {
            return back()->with('error', 'Sudah selesai, tidak bisa edit.');
        }

        $laporan = LaporanPerjadin::firstOrNew([
            'id_perjadin' => $id,
            'id_user'     => $nip
        ]);

        $laporan->uraian = $request->uraian;
        $laporan->save();

        $perjalanan = PerjalananDinas::find($id);
        $perjalanan?->updateStatus();

        return back()->with('success', 'Uraian tersimpan.');
    }

    /**
     * ABSENSI / GEOTAGGING
     */
    public function tandaiKehadiran(Request $request, $id)
    {
        $request->validate([
            'latitude'  => 'required',
            'longitude' => 'required',
            'id_tipe'   => 'required'
        ]);

        $perjalanan = PerjalananDinas::findOrFail($id);
        $today = Carbon::today();

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
            'id_user'     => Auth::user()->nip,
            'id_tipe'     => $request->id_tipe,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude
        ]);

        return response()->json(['status' => 'success', 'message' => 'Hadir!']);
    }
}
