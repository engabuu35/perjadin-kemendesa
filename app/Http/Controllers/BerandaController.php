<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\PerjalananDinas;
use App\Models\Geotagging; // pastikan model ini ada

class BerandaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $daftar = PerjalananDinas::with(['status', 'laporanKeuangan', 'pegawai_laporan'])
            ->whereHas('pegawai', function ($q) use ($user) {
                $q->where('nip', $user->nip);
            })
            ->whereHas('status', function ($q) {
                $q->whereNotIn('nama_status', ['Selesai', 'Dibatalkan']);
            })
            ->orderBy('tgl_mulai', 'asc')
            ->get();

        // --- OPTIMISASI: ambil semua geotag hari ini untuk user sekali saja ---
        $todayDate = now()->toDateString();
        $geotaggedPerjadinIds = Geotagging::where('id_user', $user->nip)
            ->whereDate('created_at', $todayDate)
            ->pluck('id_perjadin')
            ->toArray();

        $perjalanan_list = $daftar->map(function ($p) use ($user, $geotaggedPerjadinIds) {
            $today   = now()->startOfDay();
            $mulai   = $p->tgl_mulai ? $p->tgl_mulai->startOfDay() : null;
            $selesai = $p->tgl_selesai ? $p->tgl_selesai->endOfDay() : null;

            // Sumber kebenaran: MODEL
            $status_text  = $p->status_name ?? ($p->status->nama_status ?? '—');
            $status_class = $p->status_class ?? 'bg-gray-500';

            $tglString = ($p->tgl_mulai ? $p->tgl_mulai->translatedFormat('d M') : '-') .
                        ' - ' .
                        ($p->tgl_selesai ? $p->tgl_selesai->translatedFormat('d M Y') : '-');

            // ===== PER-USER CHECKS (BERANDA) =====
            // 1) Uraian untuk user ini kosong pada pivot? (nama pivot: 'laporan_individu' atau 'uraian')
            $uraianMissing = false;
            $lapPivotUser = $p->pegawai_laporan->firstWhere('nip', $user->nip);
            if ($lapPivotUser) {
                $laporan = $lapPivotUser->pivot->uraian ?? null;
                $uraianMissing = (! $laporan || trim($laporan) === '');
            } else {
                // seharusnya tidak terjadi karena whereHas, tapi safe fallback:
                $uraianMissing = true;
            }

            // 2) Geotag hari ini sudah ada untuk perjadin ini?
            $geotagExistsToday = in_array($p->id, $geotaggedPerjadinIds, true);
            $geotagMissingToday = ! $geotagExistsToday;

            return (object) [
                'id'                    => $p->id,
                'nomor_surat'           => $p->nomor_surat,
                'lokasi'                => $p->tujuan,
                'tanggal'               => $tglString,
                'status'                => $status_text,
                'status_class'          => $status_class,
                'uraian_missing'        => $uraianMissing,
                'geotag_missing_today'  => $geotagMissingToday,
            ];
        });

        return view('pages.beranda', compact('perjalanan_list'));
    }
}
