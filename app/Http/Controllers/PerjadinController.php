<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Geotagging;
use App\Models\PerjalananDinas;
use App\Models\LaporanPerjadin;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Notifikasi;
use App\Notifications\PerjalananAssignedNotification;

class PerjadinController extends Controller
{
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

        foreach ($penugasans as $p) {
            $pegs = DB::table('pegawaiperjadin')
                ->where('id_perjadin', $p->id)
                ->pluck('id_user')
                ->toArray();

            // LOGIKA BARU: Jika status manual (ID 7), anggap uraian lengkap
            if ($p->id_status == 7) {
                $missingUraian[$p->id] = [];
            } else {
                $missingUraian[$p->id] = DB::table('laporan_perjadin')
                    ->where('id_perjadin', $p->id)
                    ->where(function ($q) {
                        $q->whereNull('uraian')
                          ->orWhereRaw("TRIM(IFNULL(uraian,'')) = ''");
                    })
                    ->pluck('id_user')
                    ->toArray();
            }

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

    public function show($id)
    {
        $user    = Auth::user();
        $userNip = $user->nip;

        $perjalanan = PerjalananDinas::findOrFail($id);

        if (method_exists($perjalanan, 'updateStatus')) {
            $perjalanan->updateStatus();
            $perjalanan->refresh();
        }

        $roleKode = DB::table('penugasanperan')
            ->join('roles', 'penugasanperan.role_id', '=', 'roles.id')
            ->where('penugasanperan.user_id', $userNip)
            ->value('roles.kode');

        $roleKode = $roleKode ? strtoupper($roleKode) : null;

        $dataSaya = DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->first();

        $isAnggota  = (bool) $dataSaya;
        $isPembuat  = $perjalanan->id_pembuat === $userNip;
        $isPrivRole = in_array($roleKode, ['PIC', 'PPK', 'PIMPINAN']);

        if (!$isAnggota && !$isPembuat && !$isPrivRole) {
            abort(403);
        }

        $isMyTaskFinished = $dataSaya ? ($dataSaya->is_finished == 1) : false;

        $idStatusSelesai = DB::table('statusperjadin')->where('nama_status', 'Selesai')->value('id');
        $isRiwayatPerjadin = $perjalanan->id_status == $idStatusSelesai;

        $canEditUraian   = !$isRiwayatPerjadin && !$isMyTaskFinished;
        $canFinishTask   = !$isRiwayatPerjadin && !$isMyTaskFinished;

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
                'lat_raw' => $tag ? $tag->latitude : null,
                'long_raw'=> $tag ? $tag->longitude : null,
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

        $finishMessage = null;
        if (!$canFinish) {
            if ($today->lt($perjalanan->tgl_mulai)) {
                $finishMessage = 'Belum bisa diselesaikan: tugas belum dimulai.';
            } elseif ($today->gt($perjalanan->tgl_selesai)) {
                $finishMessage = 'Periode tugas telah berakhir.';
            } elseif (!$isLastDay && !$isPastEnd) {
                $finishMessage = 'Tombol hanya aktif pada hari terakhir tugas.';
            } elseif ($isLastDay && !$sudahAbsenHariIni) {
                $finishMessage = 'Anda belum melakukan geotagging hari ini.';
            }
        }

        $statusMessage = null;
        if (!$isTodayInPeriod) {
            if ($today->lt($perjalanan->tgl_mulai)) {
                $statusMessage = 'Tugas belum dimulai.';
            } else {
                $statusMessage = 'Periode tugas telah berakhir.';
            }
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

        return view('pages.detailperjadin', compact(
            'perjalanan',
            'dataSaya',
            'isRiwayatPerjadin',
            'canEditUraian',
            'canFinishTask',
            'geotagHistory',
            'sudahAbsenHariIni',
            'laporanSaya',
            'isTodayInPeriod',
            'isMyTaskFinished',
            'canFinish',
            'statusMessage',
            'statusPegawai',
            'statusBadgeClass',
            'finishMessage'
        ));
    }

    public function selesaikanTugasSaya(Request $request, $id)
    {
        $userNip = Auth::user()->nip;
        $perjalanan = PerjalananDinas::findOrFail($id);
        $today = Carbon::today();

        if (!$today->isSameDay($perjalanan->tgl_selesai) && !$today->gt($perjalanan->tgl_selesai)) {
            return back()->with('error', 'Hanya bisa diselesaikan pada atau setelah tanggal selesai.');
        }

        $laporan = LaporanPerjadin::where('id_perjadin', $id)->where('id_user', $userNip)->first();
        
        if (!$laporan || empty($laporan->uraian)) {
            return back()->with('error', 'Anda belum mengisi uraian kegiatan.');
        }

        $jumlahKata = str_word_count(strip_tags($laporan->uraian));
        if ($jumlahKata < 100) {
            return back()->with('error', "Uraian kegiatan minimal 100 kata.");
        }

        DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->where('id_user', $userNip)
            ->update(['is_finished' => 1]);

        $laporan->update(['is_final' => 1]);

        $totalPegawai = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->count();
        $totalSelesai = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->where('is_finished', 1)->count();

        if ($totalPegawai == $totalSelesai) {
            $statusNext = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi Laporan')->value('id');
            if ($statusNext) {
                $perjalanan->update(['id_status' => $statusNext]);
                return back()->with('success', 'Tugas Anda selesai! Laporan diteruskan ke PIC.');
            }
        }

        return back()->with('success', 'Tugas Anda selesai.');
    }

    public function storeUraian(Request $request, $id)
    {
        $request->validate(['uraian' => 'nullable|string']);
        $nip = Auth::user()->nip;

        $cek = DB::table('pegawaiperjadin')->where('id_perjadin', $id)->where('id_user', $nip)->first();
        if (!$cek) return back()->with('error', 'Anda tidak terdaftar.');
        if ($cek->is_finished) return back()->with('error', 'Sudah selesai, tidak bisa edit.');

        $laporan = LaporanPerjadin::firstOrNew(['id_perjadin' => $id, 'id_user' => $nip]);
        $laporan->uraian = $request->uraian;
        $laporan->save();

        return back()->with('success', 'Uraian tersimpan.');
    }

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

        if (Geotagging::where('id_perjadin', $id)->where('id_user', Auth::user()->nip)->whereDate('created_at', $today)->exists()) {
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

    public function assign(Request $request, PerjalananDinas $perjalanan)
    {
        $user = User::find($request->input('user_id'));
        if ($user) {
            $user->notify(new PerjalananAssignedNotification($perjalanan));
        }
        return back()->with('success', 'Perjalanan berhasil ditugaskan.');
    }
}