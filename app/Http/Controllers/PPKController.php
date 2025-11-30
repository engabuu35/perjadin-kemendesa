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

class PPKController extends Controller
{
    public function index(Request $request)
    {
        $query = PerjalananDinas::query();
        $query->join('statusperjadin', 'perjalanandinas.id_status', '=', 'statusperjadin.id')
              ->select('perjalanandinas.*', 'statusperjadin.nama_status');

        // Hanya status yang perlu divalidasi PPK
        $query->whereIn('statusperjadin.nama_status', [
            'Menunggu Validasi PPK',
            'Menunggu Verifikasi'
        ]);

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
                ->select('bukti_laporan.kategori', 'bukti_laporan.nominal', 'bukti_laporan.keterangan')
                ->get();

            $biaya = [
                'Tiket'            => 0,
                'Uang Harian'      => 0,
                'Penginapan'       => 0,
                'Uang Representasi'=> 0,
                'Transport'        => 0,
                'Sewa Kendaraan'   => 0,
                'Pengeluaran Riil' => 0,
                'SSPB'             => 0,
                'Total'            => 0,
            ];

            $info = [
                'Nama Penginapan'  => '-',
                'Kota'             => '-',
                'Kode Tiket'       => '-',
                'Maskapai'         => '-',
            ];

            foreach ($buktis as $b) {
                if ($b->nominal > 0) {
                    if (isset($biaya[$b->kategori])) {
                        $biaya[$b->kategori] += $b->nominal;
                    }
                    if ($b->kategori != 'SSPB') {
                        $biaya['Total'] += $b->nominal;
                    }
                } else {
                    if (array_key_exists($b->kategori, $info)) {
                        $info[$b->kategori] = $b->keterangan;
                    }
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

        // Status laporan keuangan -> Selesai
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

        // Status perjadin (surat) -> Selesai
        $idSelesai = DB::table('statusperjadin')->where('nama_status', 'Selesai')->value('id');
        $perjalanan->update(['id_status' => $idSelesai]);

        return redirect()
            ->route('ppk.verifikasi.index')
            ->with('success', 'Pembayaran disetujui. Data masuk Riwayat.');
    }

    public function reject(Request $request, $id)
    {
        $perjalanan = PerjalananDinas::findOrFail($id);
        
        // Status Perjadin -> 'Perlu Revisi'

        $idRevisi = DB::table('statusperjadin')->where('nama_status', 'Perlu Revisi')->value('id');
        if (!$idRevisi) {
            $idRevisi = DB::table('statusperjadin')->insertGetId(['nama_status' => 'Perlu Revisi']);
        }

        $perjalanan->update([
            'id_status' => $idRevisi,
            'catatan_penolakan' => $request->alasan_penolakan, // Pastikan kolom ini ada di DB dan $fillable model
            'id_status'         => $idRevisi,
            'catatan_penolakan' => $request->alasan_penolakan,
        ]);

        // Status Laporan Keuangan -> 'Perlu Revisi'
        $idLapRevisi = DB::table('statuslaporan')->where('nama_status', 'Perlu Revisi')->value('id');
        if (!$idLapRevisi) {
            $idLapRevisi = DB::table('statuslaporan')->insertGetId(['nama_status' => 'Perlu Revisi']);
        }
        
        LaporanKeuangan::updateOrCreate(['id_perjadin' => $id], ['id_status' => $idLapRevisi]);

        return redirect()->route('ppk.verifikasi.index')->with('warning', 'Laporan dikembalikan ke PIC untuk perbaikan.');
        LaporanKeuangan::updateOrCreate(
            ['id_perjadin' => $id],
            ['id_status' => $idLapRevisi]
        );

        return redirect()
            ->route('ppk.verifikasi.index')
            ->with('warning', 'Laporan dikembalikan ke PIC.');
    }

    public function tabelRekap(Request $request)
    {
        $bulanMulai   = $request->input('bulan_mulai');
        $bulanSelesai = $request->input('bulan_selesai');
        $tahun        = $request->input('tahun');

        /*
         * SUBQUERY: agregasi bukti_laporan per (id_perjadin, id_user)
         * menghasilkan kolom:
         *   biaya_tiket, biaya_uang_harian, biaya_penginapan, biaya_uang_representasi,
         *   biaya_transport, biaya_sewa_kendaraan, biaya_pengeluaran_riil, biaya_sspb,
         *   jumlah_dibayarkan, nama_hotel, kota_hotel, kode_tiket, maskapai
         */
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
                // Jumlah dibayarkan = semua nominal > 0 kecuali SSPB
                DB::raw("SUM(CASE WHEN bukti_laporan.nominal > 0 AND bukti_laporan.kategori <> 'SSPB' THEN bukti_laporan.nominal ELSE 0 END) AS jumlah_dibayarkan"),
                // Informasi non-nominal diambil dari keterangan
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Nama Penginapan' THEN bukti_laporan.keterangan ELSE NULL END) AS nama_hotel"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Kota' THEN bukti_laporan.keterangan ELSE NULL END) AS kota_hotel"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Kode Tiket' THEN bukti_laporan.keterangan ELSE NULL END) AS kode_tiket"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Maskapai' THEN bukti_laporan.keterangan ELSE NULL END) AS maskapai")
            )
            ->groupBy('laporan_perjadin.id_perjadin', 'laporan_perjadin.id_user');

        /*
         * QUERY UTAMA: join perjalanandinas + laporankeuangan + pegawaiperjadin + users
         * + (unitkerja induk dan anak) + pangkatgolongan + subquery agg di atas.
         */
        $query = DB::table('laporankeuangan')
            ->join('perjalanandinas', 'laporankeuangan.id_perjadin', '=', 'perjalanandinas.id')
            ->join('statuslaporan', 'laporankeuangan.id_status', '=', 'statuslaporan.id')
            ->join('pegawaiperjadin', 'pegawaiperjadin.id_perjadin', '=', 'perjalanandinas.id')
            ->join('users', 'pegawaiperjadin.id_user', '=', 'users.nip')
            ->leftJoin('unitkerja as uke2', 'users.id_uke', '=', 'uke2.id')          // UKE-2 (unit langsung)
            ->leftJoin('unitkerja as uke1', 'uke2.id_induk', '=', 'uke1.id')        // UKE-1 (induk)
            ->leftJoin('pangkatgolongan', 'users.pangkat_gol_id', '=', 'pangkatgolongan.id')
            ->leftJoinSub($agg, 'agg', function ($join) {
                $join->on('agg.id_perjadin', '=', 'perjalanandinas.id');
                $join->on('agg.id_user', '=', 'users.nip');
            })
            ->where('statuslaporan.nama_status', 'Selesai')
            ->select(
                'perjalanandinas.tujuan',
                'perjalanandinas.tgl_mulai',
                'perjalanandinas.tgl_selesai',
                'users.nama as nama_pegawai',
                'users.nip',
                'laporankeuangan.nomor_spm',
                'laporankeuangan.tanggal_spm',
                'laporankeuangan.nomor_sp2d',
                'laporankeuangan.tanggal_sp2d',
                DB::raw('uke1.nama_uke as nama_uke1'),
                DB::raw('uke2.nama_uke as nama_uke2'),
                DB::raw('pangkatgolongan.nama_pangkat as pangkat_golongan'),
                // Rincian biaya dari subquery
                DB::raw('COALESCE(agg.biaya_tiket, 0)              as biaya_tiket'),
                DB::raw('COALESCE(agg.biaya_uang_harian, 0)        as biaya_uang_harian'),
                DB::raw('COALESCE(agg.biaya_penginapan, 0)         as biaya_penginapan'),
                DB::raw('COALESCE(agg.biaya_uang_representasi, 0)  as biaya_uang_representasi'),
                DB::raw('COALESCE(agg.biaya_transport, 0)          as biaya_transport'),
                DB::raw('COALESCE(agg.biaya_sewa_kendaraan, 0)     as biaya_sewa_kendaraan'),
                DB::raw('COALESCE(agg.biaya_pengeluaran_riil, 0)   as biaya_pengeluaran_riil'),
                DB::raw('COALESCE(agg.biaya_sspb, 0)               as biaya_sspb'),
                DB::raw('COALESCE(agg.jumlah_dibayarkan, 0)        as jumlah_dibayarkan'),
                'agg.nama_hotel',
                'agg.kota_hotel',
                'agg.kode_tiket',
                'agg.maskapai'
            );

        // Filter tahun
        if ($tahun) {
            $query->whereYear('perjalanandinas.tgl_mulai', $tahun);
        }

        // Filter range bulan
        if ($bulanMulai && $bulanSelesai) {
            $query->whereMonth('perjalanandinas.tgl_mulai', '>=', $bulanMulai)
                  ->whereMonth('perjalanandinas.tgl_mulai', '<=', $bulanSelesai);
        }

        $rekap = $query
            ->orderBy('perjalanandinas.tgl_mulai')
            ->orderBy('users.nama')
            ->get();

        // Total jumlah dibayarkan (per pegawai) dari kolom hasil agregasi
        $totalDibayarkan = $rekap->sum('jumlah_dibayarkan');

        return view('ppk.tabelRekap', compact(
            'rekap',
            'totalDibayarkan',
            'bulanMulai',
            'bulanSelesai',
            'tahun'
        ));
    }

    public function exportRekap(Request $request) {
        return Excel::download(
            new RekapPerjadinExport($request->tahun, $request->bulan_mulai, $request->bulan_selesai),
            'rekap.xlsx'
        );
    }
}