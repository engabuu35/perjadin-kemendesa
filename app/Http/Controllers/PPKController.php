<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerjalananDinas;
use App\Models\LaporanKeuangan;
use App\Models\BuktiLaporan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\RekapPerjadinExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\NotificationController;
use App\Models\User;

class PPKController extends Controller
{
    public function index(Request $request)
    {
        $query = PerjalananDinas::query();
        $query->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
              ->leftJoin('laporankeuangan', 'perjalanandinas.id', '=', 'laporankeuangan.id_perjadin')
              ->leftJoin('statuslaporan', 'laporankeuangan.id_status', '=', 'statuslaporan.id')
              ->select('perjalanandinas.*', 'statusperjadin.nama_status');

        // LOGIKA FILTER BARU: Sertakan status Manual JIKA sudah ada laporan keuangan (Menunggu Verifikasi)
        $query->where(function($q) {
            $q->whereIn('statusperjadin.nama_status', ['Menunggu Validasi PPK', 'Menunggu Verifikasi'])
              ->orWhere(function($sub) {
                  $sub->where('statusperjadin.nama_status', 'Diselesaikan Manual')
                      ->where('statuslaporan.nama_status', 'Menunggu Verifikasi');
              });
        });

        if ($request->has('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nomor_surat', 'like', "%$q%")
                    ->orWhere('tujuan', 'like', "%$q%");
            });
        }

        $listVerifikasi = $query->orderBy('updated_at', 'desc')->paginate(12);

        foreach ($listVerifikasi as $item) {
            $item->custom_status = 'Butuh Validasi';
            $item->status_color  = 'blue';
        }

        return view('ppk.verifikasi.index', compact('listVerifikasi'));
    }

    public function show($id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        $statusText = DB::table('statusperjadin')->where('id', $perjalanan->id_status)->value('nama_status');
        $isSelesai  = ($statusText === 'Selesai');
        $laporanKeuangan = LaporanKeuangan::where('id_perjadin', $id)->first();

        $pesertaList = DB::table('pegawaiperjadin')
            ->join('users', 'users.nip', '=', 'pegawaiperjadin.id_user')
            ->where('pegawaiperjadin.id_perjadin', $id)
            ->select('users.nip', 'users.nama', 'pegawaiperjadin.role_perjadin')
            ->get();

        $rekapData = [];
        $totalSeluruhnya = 0;

        foreach ($pesertaList as $p) {
            $buktis = DB::table('laporan_perjadin')
                ->join('bukti_laporan', 'laporan_perjadin.id', '=', 'bukti_laporan.id_laporan')
                ->where('laporan_perjadin.id_perjadin', $id)
                ->where('laporan_perjadin.id_user', $p->nip)
                ->select('bukti_laporan.kategori', 'bukti_laporan.nominal', 'bukti_laporan.keterangan', 'bukti_laporan.path_file', 'bukti_laporan.nama_file')
                ->get();

            // UPDATE: Struktur array biaya sekarang menyimpan nominal DAN file
            $biaya = [
            'Tiket'            => ['nominal' => 0, 'path_file' => null, 'nama_file' => null],
            'Uang Harian'      => ['nominal' => 0, 'path_file' => null, 'nama_file' => null],
            'Penginapan'       => ['nominal' => 0, 'path_file' => null, 'nama_file' => null],
            'Uang Representasi'=> ['nominal' => 0, 'path_file' => null, 'nama_file' => null],
            'Transport'        => ['nominal' => 0, 'path_file' => null, 'nama_file' => null],
            'Sewa Kendaraan'   => ['nominal' => 0, 'path_file' => null, 'nama_file' => null],
            'Pengeluaran Riil' => ['nominal' => 0, 'path_file' => null, 'nama_file' => null],
            'SSPB'             => ['nominal' => 0, 'path_file' => null, 'nama_file' => null],
            'Total'            => 0
            ];

            $info = [
                'Nama Penginapan'        => '-', 
                'Kota'                   => '-', 
                
                // Transportasi Pergi
                'Jenis Transportasi(Pergi)'  => '-', 
                'Kode Tiket(Pergi)'       => '-',
                'Nama Transportasi(Pergi)'   => '-', 
                
                // Transportasi Pulang
                'Jenis Transportasi(Pulang)' => '-', 
                'Kode Tiket(Pulang)'      => '-',
                'Nama Transportasi(Pulang)'  => '-'
            ];

            foreach($buktis as $b) {
                // Cek apakah kategori ini ada di daftar biaya (kecuali Total)
                if (array_key_exists($b->kategori, $biaya) && $b->kategori !== 'Total') {
                    // Tambah nominal
                    $biaya[$b->kategori]['nominal'] += $b->nominal;
                    
                    // Simpan path file jika ada
                    if (!empty($b->path_file)) {
                    $biaya[$b->kategori]['path_file'] = $b->path_file;
                    $biaya[$b->kategori]['nama_file'] = $b->nama_file ?? basename($b->path_file);
                    }

                    // Hitung Total (Kecuali SSPB)
                    if ($b->kategori != 'SSPB') {
                        $biaya['Total'] += $b->nominal;
                    }
                } else {
                    if (array_key_exists($b->kategori, $info)) $info[$b->kategori] = $b->keterangan;
                }
            }

            $totalSeluruhnya += $biaya['Total'];

            $rekapData[] = [
                'nip'   => $p->nip,
                'nama'  => $p->nama,
                'biaya' => $biaya,
                'info'  => $info,
            ];
        }

        return view(
            'ppk.verifikasi.detail',
            compact('perjalanan', 'rekapData', 'statusText', 'isSelesai', 'totalSeluruhnya', 'laporanKeuangan')
        );
    }

    public function approve(Request $request, $id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        $idLapSelesai = DB::table('statuslaporan')->where('nama_status', 'Selesai')->value('id') ?? 5;

        LaporanKeuangan::updateOrCreate(
            ['id_perjadin' => $id],
            [
                'id_status'     => $idLapSelesai,
                'verified_by'   => Auth::user()->nip,
                'verified_at'   => now(),
                'nomor_spm'     => $request->nomor_spm,
                'tanggal_spm'   => $request->tanggal_spm,
                'nomor_sp2d'    => $request->nomor_sp2d,
                'tanggal_sp2d'  => $request->tanggal_sp2d,
                'biaya_rampung' => $request->total_biaya_rampung,
            ]
        );

        $idSelesai = DB::table('statusperjadin')->where('nama_status', 'Selesai')->value('id');
        $perjalanan->update(['id_status' => $idSelesai]);

        $pesertaNips = DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->pluck('id_user')
            ->toArray();
        
        if (!empty($pesertaNips)) {
            app(NotificationController::class)->sendFromTemplate(
                'laporan_selesai',  // ganti template ke 'laporan_selesai'
                $pesertaNips,
                [
                    'tujuan' => $perjalanan->tujuan,
                    'tanggal' => $perjalanan->tgl_mulai ? date('d M Y', strtotime($perjalanan->tgl_mulai)) : '-',
                ],
                ['action_url' => '/perjalanan/' . $id]
            );
        }

        $picUsers = User::whereHas('roles', function($q) {
            $q->where('kode', 'PIC');
        })->pluck('nip')->toArray();
        
        if (!empty($picUsers)) {
            app(NotificationController::class)->send(
                $picUsers,
                'verifikasi',
                'Laporan Disetujui PPK',
                'Laporan perjalanan ke ' . $perjalanan->tujuan . ' telah disetujui oleh PPK.',
                ['id_perjadin' => $id, 'tujuan' => $perjalanan->tujuan],
                [
                    'icon' => '✅',
                    'color' => 'green',
                    // tidak ada action_url - notifikasi ini hanya informasi saja
                ]
            );
        }

        return redirect()->route('ppk.verifikasi.index')->with('success', 'Pembayaran disetujui. Data masuk Riwayat.');
    }

    public function reject(Request $request, $id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);

        $idLapRevisi = DB::table('statuslaporan')->where('nama_status', 'Perlu Revisi')->value('id');
        if (!$idLapRevisi) {
            $idLapRevisi = DB::table('statuslaporan')->insertGetId(['nama_status' => 'Perlu Revisi']);
        }
        
        LaporanKeuangan::updateOrCreate(['id_perjadin' => $id], ['id_status' => $idLapRevisi]);

        $pesertaNips = DB::table('pegawaiperjadin')
            ->where('id_perjadin', $id)
            ->pluck('id_user')
            ->toArray();
        
        if (!empty($pesertaNips)) {
            app(NotificationController::class)->sendFromTemplate(
                'laporan_dikembalikan',
                $pesertaNips,
                [
                    'tujuan' => $perjalanan->tujuan,
                    'alasan' => $request->alasan_penolakan ?? 'Perlu perbaikan',
                ],
                ['action_url' => '/perjalanan/' . $id]
            );
        }

        $picUsers = User::whereHas('roles', function($q) {
            $q->where('kode', 'PIC');
        })->pluck('nip')->toArray();
        
        if (!empty($picUsers)) {
            app(NotificationController::class)->send(
                $picUsers,
                'verifikasi',
                'Laporan Dikembalikan PPK',
                'Laporan perjalanan ke ' . $perjalanan->tujuan . ' dikembalikan oleh PPK. Alasan: ' . ($request->alasan_penolakan ?? 'Perlu perbaikan'),
                ['id_perjadin' => $id, 'tujuan' => $perjalanan->tujuan, 'alasan' => $request->alasan_penolakan],
                [
                    'icon' => '🔄',
                    'color' => 'orange',
                    'action_url' => '/pic/pelaporan-keuangan/' . $id,
                ]
            );
        }

        return redirect()->route('ppk.verifikasi.index')->with('warning', 'Laporan dikembalikan ke PIC untuk perbaikan.');
    }

    public function tabelRekap(Request $request)
    {
        $bulanMulai   = $request->input('bulan_mulai');
        $bulanSelesai = $request->input('bulan_selesai');
        $tahun        = $request->input('tahun');

        $agg = DB::table('laporan_perjadin')
            ->join('bukti_laporan', 'laporan_perjadin.id', '=', 'bukti_laporan.id_laporan')
            ->select(
                'laporan_perjadin.id_perjadin',
                'laporan_perjadin.id_user',
                DB::raw("SUM(CASE WHEN bukti_laporan.nominal > 0 AND bukti_laporan.kategori = 'Tiket' THEN bukti_laporan.nominal ELSE 0 END) AS biaya_tiket"),
                DB::raw("SUM(CASE WHEN bukti_laporan.nominal > 0 AND bukti_laporan.kategori = 'Uang Harian' THEN bukti_laporan.nominal ELSE 0 END) AS biaya_uang_harian"),
                DB::raw("SUM(CASE WHEN bukti_laporan.nominal > 0 AND bukti_laporan.kategori = 'Penginapan' THEN bukti_laporan.nominal ELSE 0 END) AS biaya_penginapan"),
                DB::raw("SUM(CASE WHEN bukti_laporan.nominal > 0 AND bukti_laporan.kategori = 'Uang Representasi' THEN bukti_laporan.nominal ELSE 0 END) AS biaya_uang_representasi"),
                DB::raw("SUM(CASE WHEN bukti_laporan.nominal > 0 AND bukti_laporan.kategori = 'Transport' THEN bukti_laporan.nominal ELSE 0 END) AS biaya_transport"),
                DB::raw("SUM(CASE WHEN bukti_laporan.nominal > 0 AND bukti_laporan.kategori = 'Sewa Kendaraan' THEN bukti_laporan.nominal ELSE 0 END) AS biaya_sewa_kendaraan"),
                DB::raw("SUM(CASE WHEN bukti_laporan.nominal > 0 AND bukti_laporan.kategori = 'Pengeluaran Riil' THEN bukti_laporan.nominal ELSE 0 END) AS biaya_pengeluaran_riil"),
                DB::raw("SUM(CASE WHEN bukti_laporan.nominal > 0 AND bukti_laporan.kategori = 'SSPB' THEN bukti_laporan.nominal ELSE 0 END) AS biaya_sspb"),
                DB::raw("SUM(CASE WHEN bukti_laporan.nominal > 0 AND bukti_laporan.kategori <> 'SSPB' THEN bukti_laporan.nominal ELSE 0 END) AS jumlah_dibayarkan"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Nama Penginapan' THEN bukti_laporan.keterangan ELSE NULL END) AS nama_penginapan"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Kota' THEN bukti_laporan.keterangan ELSE NULL END) AS kota"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Jenis Transportasi(Pergi)' THEN bukti_laporan.keterangan ELSE NULL END) AS jenis_transportasi_pergi"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Kode Tiket(Pergi)' THEN bukti_laporan.keterangan ELSE NULL END) AS kode_tiket_pergi"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Nama Transportasi(Pergi)' THEN bukti_laporan.keterangan ELSE NULL END) AS nama_transportasi_pergi"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Jenis Transportasi(Pulang)' THEN bukti_laporan.keterangan ELSE NULL END) AS jenis_transportasi_pulang"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Kode Tiket(Pulang)' THEN bukti_laporan.keterangan ELSE NULL END) AS kode_tiket_pulang"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Nama Transportasi(Pulang)' THEN bukti_laporan.keterangan ELSE NULL END) AS nama_transportasi_pulang")
            )
            ->groupBy('laporan_perjadin.id_perjadin', 'laporan_perjadin.id_user');

        $query = DB::table('laporankeuangan')
            ->join('perjalanandinas', 'laporankeuangan.id_perjadin', '=', 'perjalanandinas.id')
            ->join('statuslaporan', 'laporankeuangan.id_status', '=', 'statuslaporan.id')
            ->join('pegawaiperjadin', 'pegawaiperjadin.id_perjadin', '=', 'perjalanandinas.id')
            ->join('users', 'pegawaiperjadin.id_user', '=', 'users.nip')
            ->leftJoin('unitkerja as uke2', 'users.id_uke', '=', 'uke2.id')
            ->leftJoin('unitkerja as uke1', 'uke2.id_induk', '=', 'uke1.id')
            ->leftJoin('pangkatgolongan', 'users.pangkat_gol_id', '=', 'pangkatgolongan.id')
            ->leftJoinSub($agg, 'agg', function ($join) {
                $join->on('agg.id_perjadin', '=', 'perjalanandinas.id');
                $join->on('agg.id_user', '=', 'users.nip');
            })
            ->where('statuslaporan.nama_status', 'Selesai')
            ->select(
                'perjalanandinas.tujuan',
                'perjalanandinas.dalam_rangka',
                'perjalanandinas.tgl_mulai',
                'perjalanandinas.tgl_selesai',
                'users.nama as nama_pegawai',
                'users.nip',
                'laporankeuangan.nomor_spm',
                'laporankeuangan.tanggal_spm',
                'laporankeuangan.nomor_sp2d',
                'laporankeuangan.tanggal_sp2d',
                'laporankeuangan.biaya_rampung as jumlah_sp2d', 
                DB::raw('uke1.nama_uke as nama_uke1'),
                DB::raw('uke2.nama_uke as nama_uke2'),
                DB::raw('pangkatgolongan.nama_pangkat as pangkat_golongan'),
                DB::raw('COALESCE(agg.biaya_tiket, 0)              as biaya_tiket'),
                DB::raw('COALESCE(agg.biaya_uang_harian, 0)        as biaya_uang_harian'),
                DB::raw('COALESCE(agg.biaya_penginapan, 0)         as biaya_penginapan'),
                DB::raw('COALESCE(agg.biaya_uang_representasi, 0)  as biaya_uang_representasi'),
                DB::raw('COALESCE(agg.biaya_transport, 0)          as biaya_transport'),
                DB::raw('COALESCE(agg.biaya_sewa_kendaraan, 0)     as biaya_sewa_kendaraan'),
                DB::raw('COALESCE(agg.biaya_pengeluaran_riil, 0)   as biaya_pengeluaran_riil'),
                DB::raw('COALESCE(agg.biaya_sspb, 0)               as biaya_sspb'),
                DB::raw('COALESCE(agg.jumlah_dibayarkan, 0)        as jumlah_dibayarkan'),
                'agg.nama_penginapan', 'agg.kota',
                'agg.jenis_transportasi_pergi', 'agg.kode_tiket_pergi', 'agg.nama_transportasi_pergi',
                'agg.jenis_transportasi_pulang', 'agg.kode_tiket_pulang', 'agg.nama_transportasi_pulang'
            );

        if ($tahun) $query->whereYear('perjalanandinas.tgl_mulai', $tahun);
        if ($bulanMulai && $bulanSelesai) {
            $query->whereMonth('perjalanandinas.tgl_mulai', '>=', $bulanMulai)
                  ->whereMonth('perjalanandinas.tgl_mulai', '<=', $bulanSelesai);
        }

        $rekap = $query->orderBy('perjalanandinas.tgl_mulai')->orderBy('users.nama')->get();
        $totalDibayarkan = $rekap->sum('jumlah_dibayarkan');

        return view('ppk.tabelRekap', compact('rekap', 'totalDibayarkan', 'bulanMulai', 'bulanSelesai', 'tahun'));
    }

    public function exportRekap(Request $request) {
        return Excel::download(
            new RekapPerjadinExport($request->tahun, $request->bulan_mulai, $request->bulan_selesai),
            'rekap.xlsx'
        );
    }
}