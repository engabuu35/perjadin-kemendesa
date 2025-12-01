<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PimpinanController extends Controller
{
    /**
     * Halaman dashboard / monitoring pimpinan:
     * - Peta geotagging perjadin aktif
     * - Grafik jumlah perjadin per bulan
     * - Grafik anggaran per bulan
     * - Daftar perjadin yang sedang berlangsung
     */
    public function index()
    {
        // Ambil ID status "Sedang Berlangsung"
        $statusOnProgress = DB::table('statusperjadin')
            ->where('nama_status', 'Sedang Berlangsung')
            ->value('id');

        // Hitung pegawai yang sedang dalam perjalanan dinas
        $pegawaiOnProgress = DB::table('pegawaiperjadin AS pp')
            ->join('perjalanandinas AS pd', 'pd.id', '=', 'pp.id_perjadin')
            ->join('users AS u', 'u.nip', '=', 'pp.id_user')   // opsional, kalau mau filter is_aktif
            ->where('pd.id_status', $statusOnProgress)        // status "Sedang Berlangsung"
            ->where('u.is_aktif', 1)                          // opsional: hanya pegawai aktif
            ->distinct('pp.id_user')                          // hitung pegawai unik
            ->count('pp.id_user');


        // Ambil data perjalanan dinas yang sedang berlangsung dengan detail
        $perjalanandinas = DB::table('perjalanandinas')
            ->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
            ->leftJoin('users as pembuat', 'perjalanandinas.id_pembuat', '=', 'pembuat.nip')
            ->where('perjalanandinas.id_status', $statusOnProgress)
            ->select(
                'perjalanandinas.*',
                'statusperjadin.nama_status',
                'pembuat.nama as nama_pembuat',
                'pembuat.email as email_pembuat'
            )
            ->orderBy('perjalanandinas.tgl_mulai', 'desc')
            ->get();

        // ==========================
        //   BAR CHART: JUMLAH PERJADIN PER BULAN
        // ==========================
        $barChartData   = [];
        $tahunSekarang  = Carbon::now()->year;

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $count = DB::table('perjalanandinas')
                ->whereYear('tgl_mulai', $tahunSekarang)
                ->whereMonth('tgl_mulai', $bulan)
                ->count();

            $barChartData[] = $count;
        }

        // Total perjadin 30 hari terakhir
        $totalSebulanTerakhir = DB::table('perjalanandinas')
            ->where('tgl_mulai', '>=', Carbon::now()->subDays(30))
            ->where('tgl_mulai', '<=', Carbon::now())
            ->count();

        // ==========================
        //   LINE CHART: ANGGARAN PER BULAN
        // ==========================
        $lineChartData = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $totalAnggaran = (int) DB::table('laporankeuangan')
                ->join('perjalanandinas', 'laporankeuangan.id_perjadin', '=', 'perjalanandinas.id')
                ->whereYear('perjalanandinas.tgl_mulai', $tahunSekarang)
                ->whereMonth('perjalanandinas.tgl_mulai', $bulan)
                ->whereNotNull('laporankeuangan.biaya_rampung')
                ->sum('laporankeuangan.biaya_rampung');

            $lineChartData[] = $totalAnggaran;
        }

        // Total anggaran 30 hari terakhir
        $anggaranSebulanTerakhir = (int) DB::table('laporankeuangan')
            ->join('perjalanandinas', 'laporankeuangan.id_perjadin', '=', 'perjalanandinas.id')
            ->where('perjalanandinas.tgl_mulai', '>=', Carbon::now()->subDays(30))
            ->where('perjalanandinas.tgl_mulai', '<=', Carbon::now())
            ->whereNotNull('laporankeuangan.biaya_rampung')
            ->sum('laporankeuangan.biaya_rampung');

        // ==========================
        //   DATA PETA GEOTAGGING (ON PROGRESS)
        // ==========================
        $geotagMapData = DB::table('geotagging as g')
            ->join('perjalanandinas as pd', 'g.id_perjadin', '=', 'pd.id')
            ->join('users as u', 'g.id_user', '=', 'u.nip')
            ->leftJoin('tipegeotagging as t', 'g.id_tipe', '=', 't.id')
            ->where('pd.id_status', $statusOnProgress)
            ->orderBy('g.created_at', 'desc')
            ->select(
                'g.latitude',
                'g.longitude',
                'g.created_at',
                'pd.id as id_perjadin',
                'pd.nomor_surat',
                'pd.tujuan',
                'u.nama',
                'u.nip',
                't.nama_tipe'
            )
            ->get()
            ->map(function ($row) {
                return [
                    'lat'     => (float) $row->latitude,
                    'lng'     => (float) $row->longitude,
                    'nama'    => $row->nama,
                    'nip'     => $row->nip,
                    'nomor'   => $row->nomor_surat,
                    'tujuan'  => $row->tujuan,
                    'waktu'   => Carbon::parse($row->created_at)->format('Y-m-d H:i'),
                    'tipe'    => $row->nama_tipe,
                ];
            });

        return view('pimpinan.monitoringPegawai', compact(
            'pegawaiOnProgress',
            'perjalanandinas',
            'barChartData',
            'lineChartData',
            'totalSebulanTerakhir',
            'anggaranSebulanTerakhir',
            'geotagMapData'
        ));
    }

    /**
     * Halaman detail perjalanan dinas (dipakai baik dari Monitoring maupun Riwayat).
     * Menampilkan ringkasan yang ringkas namun lengkap:
     * - Info umum perjadin
     * - Tim & kontak
     * - Ringkasan progres
     * - Uraian pelaksanaan (PIC & individu)
     * - Rekap keuangan
     * - Ringkasan geotagging + peta harian
     */
    public function detail($id)
    {
        // ==========================
        // 1. Detail utama perjadin
        // ==========================
        $perjadin = DB::table('perjalanandinas as pd')
            ->leftJoin('statusperjadin as sp', 'pd.id_status', '=', 'sp.id')
            ->leftJoin('laporankeuangan as lk', 'lk.id_perjadin', '=', 'pd.id')
            ->leftJoin('statuslaporan as sl', 'lk.id_status', '=', 'sl.id')
            ->where('pd.id', $id)
            ->select(
                'pd.*',
                'sp.nama_status', // nama status perjadin
                'lk.id as id_laporan_keu',
                'lk.biaya_rampung',
                'lk.created_at as laporan_keu_created_at',
                'sl.nama_status as nama_status_laporan'
            )
            ->first();

        if (!$perjadin) {
            abort(404, 'Data perjalanan dinas tidak ditemukan');
        }

        // ==========================
        // 2. Pembuat & Approver
        // ==========================
        $pembuat = DB::table('users')
            ->where('nip', $perjadin->id_pembuat)
            ->first();

        $approver = null;
        if ($perjadin->approved_by) {
            $approver = DB::table('users')
                ->where('nip', $perjadin->approved_by)
                ->first();
        }

        // ==========================
        // 3. Tim Pegawai
        // ==========================
        $pegawai = DB::table('pegawaiperjadin as pp')
            ->join('users as u', 'pp.id_user', '=', 'u.nip')
            ->where('pp.id_perjadin', $id)
            ->select(
                'pp.*',
                'u.nama',
                'u.nip',
                'u.email',
                'u.no_telp'
            )
            ->get();

        $totalPegawai   = $pegawai->count();
        $pegawaiSelesai = $pegawai->where('is_finished', 1)->count();

        // ==========================
        // 4. Hitung fase / progres umum
        // ==========================
        $mulai   = $perjadin->tgl_mulai ? Carbon::parse($perjadin->tgl_mulai) : null;
        $selesai = $perjadin->tgl_selesai ? Carbon::parse($perjadin->tgl_selesai) : null;
        $today   = Carbon::today();

        if ($mulai && $selesai) {
            if ($today->lt($mulai)) {
                $fase = 'Belum Berlangsung';
            } elseif ($today->between($mulai, $selesai)) {
                $fase = 'Sedang Berlangsung';
            } else {
                $fase = 'Selesai';
            }
        } else {
            $fase = $perjadin->nama_status ?? '-';
        }

        $persenPegawaiSelesai = $totalPegawai > 0
            ? round($pegawaiSelesai / $totalPegawai * 100)
            : 0;

        // ==========================
        // 5. Uraian Pelaksanaan (PIC & Individu)
        // ==========================
        $uraianPerjadin = $perjadin->uraian ?? '';

        $uraianIndividu = DB::table('laporan_perjadin as lp')
            ->join('users as u', 'lp.id_user', '=', 'u.nip')
            ->where('lp.id_perjadin', $id)
            ->select(
                'lp.*',
                'u.nama',
                'u.nip'
            )
            ->orderBy('lp.created_at', 'asc')
            ->get();

        // ==========================
        // 6. Rekap Keuangan
        // ==========================
        $rincian = collect();
        if ($perjadin->id_laporan_keu) {
            $rincian = DB::table('rinciananggaran as ra')
                ->join('kategoribiaya as kb', 'ra.id_kategori', '=', 'kb.id')
                ->where('ra.id_laporan', $perjadin->id_laporan_keu)
                ->select(
                    'ra.*',
                    'kb.nama_kategori'
                )
                ->get();
        }

        $keuangan = [
            'ada_laporan'           => (bool) $perjadin->id_laporan_keu,
            'status_laporan'        => $perjadin->nama_status_laporan ?? 'Belum Dibuat',
            'total_biaya_rampung'   => (int) ($perjadin->biaya_rampung ?? 0),
            'rincian'               => $rincian,
        ];

        // ==========================
        // 7. Ringkasan Geotagging
        // ==========================
        $totalHari = ($mulai && $selesai)
            ? ($mulai->diffInDays($selesai) + 1)
            : 0;

        $hariTerisi = DB::table('geotagging')
            ->where('id_perjadin', $id)
            ->select(DB::raw('DATE(created_at) as tgl'))
            ->distinct()
            ->count();

        $totalRecordGeotag = DB::table('geotagging')
            ->where('id_perjadin', $id)
            ->count();

        $geotagSummary = [
            'total_hari'   => $totalHari,
            'hari_terisi'  => $hariTerisi,
            'total_record' => $totalRecordGeotag,
        ];

        // ==========================
        // 8. Data untuk Peta Geotagging (Detail Perjadin)
        // ==========================
        $geotagMapData = DB::table('geotagging as g')
            ->join('users as u', 'g.id_user', '=', 'u.nip')
            ->leftJoin('tipegeotagging as t', 'g.id_tipe', '=', 't.id')
            ->where('g.id_perjadin', $id)
            ->orderBy('g.created_at', 'asc')
            ->select(
                'g.*',
                'u.nama',
                'u.nip',
                't.nama_tipe'
            )
            ->get()
            ->map(function ($row) {
                return [
                    'lat'     => (float) $row->latitude,
                    'lng'     => (float) $row->longitude,
                    'nama'    => $row->nama,
                    'nip'     => $row->nip,
                    'tipe'    => $row->nama_tipe,
                    'waktu'   => Carbon::parse($row->created_at)->format('Y-m-d H:i:s'),
                    'tanggal' => Carbon::parse($row->created_at)->toDateString(),
                ];
            });

        // ==========================
        // 9. Paketkan data progres untuk view
        // ==========================
        $progress = [
            'fase'                     => $fase,
            'status_perjadin'          => $perjadin->nama_status ?? '-',
            'mulai'                    => $mulai,
            'selesai'                  => $selesai,
            'total_pegawai'            => $totalPegawai,
            'pegawai_selesai'          => $pegawaiSelesai,
            'persen_pegawai_selesai'   => $persenPegawaiSelesai,
            'ada_uraian_perjadin'      => trim($uraianPerjadin) !== '',
            'jumlah_uraian_individu'   => $uraianIndividu->count(),
            'ada_laporan_keuangan'     => $keuangan['ada_laporan'],
            'status_laporan_keuangan'  => $keuangan['status_laporan'],
        ];

        return view('pimpinan.detail', compact(
            'perjadin',
            'pembuat',
            'approver',
            'pegawai',
            'progress',
            'uraianPerjadin',
            'uraianIndividu',
            'keuangan',
            'geotagSummary',
            'geotagMapData'
        ));
    }
}
