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

        return view('pages.detailperjadin', compact(
            'perjalanan',
            'geotagHistory',
            'sudahAbsenHariIni',
            'laporanSaya',
            'isTodayInPeriod',
            'isMyTaskFinished',
            'canFinish'
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

        if (!$today->isSameDay($perjalanan->tgl_selesai) && !$today->gt($perjalanan->tgl_selesai)) {
            return back()->with('error', 'Hanya bisa diselesaikan pada atau setelah tanggal selesai.');
        }

        $sudahAbsen = Geotagging::where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->whereDate('created_at', $today)
            ->exists();

        if (!$sudahAbsen && !$today->gt($perjalanan->tgl_selesai)) {
            return back()->with('error', 'Anda belum melakukan geotagging hari ini.');
        }

        DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->update(['is_finished' => 1]);

        $perjalanan->refresh();
        $perjalanan->updateStatus();

        return back()->with('success', 'Tugas Anda selesai.');
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
