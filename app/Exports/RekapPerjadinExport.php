<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapPerjadinExport implements FromCollection, WithMapping, WithHeadings, WithEvents
{
    protected ?int $tahun;
    protected ?int $bulanMulai;
    protected ?int $bulanSelesai;

    /** counter untuk kolom "No" */
    protected int $rowIndex = 0;

    public function __construct(?int $tahun = null, ?int $bulanMulai = null, ?int $bulanSelesai = null)
    {
        $this->tahun       = $tahun;
        $this->bulanMulai  = $bulanMulai;
        $this->bulanSelesai= $bulanSelesai;
    }

    /**
     * Query data – usahakan strukturnya sama seperti di PPKController::tabelRekap()
     */
    public function collection(): Collection
    {
        $tahun       = $this->tahun;
        $bulanMulai  = $this->bulanMulai;
        $bulanSelesai= $this->bulanSelesai;

        // Subquery rekap biaya per (perjadin, pegawai)
        // SESUAIKAN nama tabel & kategori jika di DB berbeda.
        $biayaSub = DB::table('laporan_perjadin as lp')
            ->join('bukti_laporan as bl', 'lp.id', '=', 'bl.id_laporan')
            ->selectRaw('
                lp.id_perjadin,
                lp.id_user,

                SUM(CASE WHEN bl.kategori = "Tiket"            THEN bl.nominal ELSE 0 END) AS biaya_tiket,
                SUM(CASE WHEN bl.kategori = "Uang Harian"      THEN bl.nominal ELSE 0 END) AS biaya_uang_harian,
                SUM(CASE WHEN bl.kategori = "Penginapan"       THEN bl.nominal ELSE 0 END) AS biaya_penginapan,
                SUM(CASE WHEN bl.kategori = "Uang Representasi" THEN bl.nominal ELSE 0 END) AS biaya_uang_representasi,
                SUM(CASE WHEN bl.kategori = "Transport"        THEN bl.nominal ELSE 0 END) AS biaya_transport,
                SUM(CASE WHEN bl.kategori = "Sewa Kendaraan"   THEN bl.nominal ELSE 0 END) AS biaya_sewa_kendaraan,
                SUM(CASE WHEN bl.kategori = "Pengeluaran Riil" THEN bl.nominal ELSE 0 END) AS biaya_pengeluaran_riil,
                SUM(CASE WHEN bl.kategori = "SSPB"             THEN bl.nominal ELSE 0 END) AS biaya_sspb,

                -- Info tambahan yang disimpan di kolom keterangan
                MAX(CASE WHEN bl.kategori = "Nama Penginapan" THEN bl.keterangan ELSE NULL END) AS nama_hotel,
                MAX(CASE WHEN bl.kategori = "Kota" THEN bl.keterangan ELSE NULL END) AS kota_hotel,
                MAX(CASE WHEN bl.kategori = "Kode Tiket"      THEN bl.keterangan ELSE NULL END) AS kode_tiket,
                MAX(CASE WHEN bl.kategori = "Maskapai"        THEN bl.keterangan ELSE NULL END) AS maskapai
            ')
            ->groupBy('lp.id_perjadin', 'lp.id_user');

        $query = DB::table('laporankeuangan as lk')
            ->join('perjalanandinas as pd', 'lk.id_perjadin', '=', 'pd.id')
            ->join('pegawaiperjadin as pp', 'pp.id_perjadin', '=', 'pd.id')
            ->join('users as u', 'pp.id_user', '=', 'u.nip')
            ->leftJoin('pangkatgolongan as pg', 'u.pangkat_gol_id', '=', 'pg.id')
            ->leftJoin('unitkerja as uke2', 'u.id_uke', '=', 'uke2.id')
            ->leftJoin('unitkerja as uke1', 'uke2.id_induk', '=', 'uke1.id')
            ->leftJoin('statuslaporan as sl', 'lk.id_status', '=', 'sl.id')
            ->leftJoinSub($biayaSub, 'b', function ($join) {
                $join->on('b.id_perjadin', '=', 'pd.id')
                     ->on('b.id_user', '=', 'u.nip');
            })
            ->selectRaw('
                pd.tgl_mulai,
                pd.tgl_selesai,
                pd.tujuan,

                u.nama               AS nama_pegawai,
                u.nip                AS nip,
                pg.nama_pangkat      AS pangkat_golongan,

                uke1.nama_uke        AS nama_uke1,
                uke2.nama_uke        AS nama_uke2,

                b.biaya_tiket,
                b.biaya_uang_harian,
                b.biaya_penginapan,
                b.biaya_uang_representasi,
                b.biaya_transport,
                b.biaya_sewa_kendaraan,
                b.biaya_pengeluaran_riil,
                b.biaya_sspb,

                lk.biaya_rampung     AS jumlah_dibayarkan,

                b.nama_hotel,
                b.kota_hotel,
                b.kode_tiket,
                b.maskapai,

                lk.nomor_spm,
                lk.tanggal_spm,
                lk.nomor_sp2d,
                lk.tanggal_sp2d
            ')
            ->where('sl.nama_status', 'Selesai');

        // Filter tahun
        if ($tahun) {
            $query->whereYear('pd.tgl_mulai', $tahun);
        }

        // Filter rentang bulan
        if ($bulanMulai && $bulanSelesai) {
            $query->whereMonth('pd.tgl_mulai', '>=', $bulanMulai)
                  ->whereMonth('pd.tgl_mulai', '<=', $bulanSelesai);
        }

        return $query
            ->orderBy('pd.tgl_mulai')
            ->orderBy('u.nama')
            ->get();
    }

    /**
     * Mapping 1 baris data -> 27 kolom (urutan harus sama dengan header & view)
     */
    public function map($row): array
    {
        $this->rowIndex++;

        $mulai   = $row->tgl_mulai ? \Carbon\Carbon::parse($row->tgl_mulai) : null;
        $selesai = $row->tgl_selesai ? \Carbon\Carbon::parse($row->tgl_selesai) : null;
        $lama    = ($mulai && $selesai) ? $mulai->diffInDays($selesai) + 1 : null;

        return [
            // 1–10: identitas & periode
            $this->rowIndex,
            $row->nama_uke1 ?? '-',
            $row->nama_uke2 ?? '-',
            $row->nama_pegawai,
            $row->nip,
            $row->pangkat_golongan ?? '-',
            $row->tujuan ?? '-',
            $mulai   ? $mulai->format('d-m-Y') : '-',
            $selesai ? $selesai->format('d-m-Y') : '-',
            $lama ?? '-',

            // 11–18: rincian biaya
            $row->biaya_tiket            ?: 0,
            $row->biaya_uang_harian      ?: 0,
            $row->biaya_penginapan       ?: 0,
            $row->biaya_uang_representasi?: 0,
            $row->biaya_transport        ?: 0,
            $row->biaya_sewa_kendaraan   ?: 0,
            $row->biaya_pengeluaran_riil ?: 0,
            $row->biaya_sspb             ?: 0,

            // 19: jumlah dibayarkan
            $row->jumlah_dibayarkan ?: 0,

            // 20–21: penginapan
            $row->nama_hotel ?? '-',
            $row->kota_hotel ?? '-',

            // 22–23: pesawat
            $row->kode_tiket ?? '-',
            $row->maskapai   ?? '-',

            // 24–27: dokumen pembayaran
            $row->nomor_spm    ?? '-',
            $row->tanggal_spm  ? \Carbon\Carbon::parse($row->tanggal_spm)->format('d-m-Y') : '-',
            $row->nomor_sp2d   ?? '-',
            $row->tanggal_sp2d ? \Carbon\Carbon::parse($row->tanggal_sp2d)->format('d-m-Y') : '-',
        ];
    }

    /**
     * Header 2 baris (multi-level).
     * Baris 1: grup besar.
     * Baris 2: nama kolom detail (sama dengan tabel web).
     */
    public function headings(): array
    {
        return [
            // Baris header 1
            [
                'No',
                'Nama UKE-1',
                'Nama UKE-2',
                'Nama Pegawai',
                'NIP',
                'Pangkat / Gol',
                'Tujuan / Dalam Rangka',
                'Tgl Berangkat',
                'Tgl Kembali',
                'Lama (hari)',

                'Rincian Pembayaran / Rincian Biaya (Rp)', // kolom K, nanti di-merge K1:R1
                '', '', '', '', '', '', '',

                'Jumlah Dibayarkan (Rp)',                  // kolom S, di-merge S1:S2

                'Penginapan',                              // T1:U1
                '',
                'Pesawat',                                 // V1:W1
                '',
                'Dokumen Pembayaran',                      // X1:AA1
                '', '', '',
            ],

            // Baris header 2 (nama kolom persis seperti di web)
            [
                'No',
                'Nama UKE-1',
                'Nama UKE-2',
                'Nama Pegawai',
                'NIP',
                'Pangkat / Gol',
                'Tujuan / Dalam Rangka',
                'Tgl Berangkat',
                'Tgl Kembali',
                'Lama (hari)',

                'Tiket',
                'Uang Harian',
                'Penginapan',
                'Uang Representasi',
                'Transport',
                'Sewa Kendaraan',
                'Pengeluaran Riil',
                'SSPB',

                'Jumlah Dibayarkan (Rp)',

                'Nama Hotel',
                'Kota',
                'Kode Tiket',
                'Maskapai',

                'No SPM',
                'Tgl SPM',
                'No SP2D',
                'Tgl SP2D',
            ],
        ];
    }

    /**
     * Styling & merge header (warna, border, dll).
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge header vertical untuk kolom yang tidak punya sub-kolom
                $mergeVertical = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'S'];
                foreach ($mergeVertical as $col) {
                    $sheet->mergeCells("{$col}1:{$col}2");
                }

                // Merge grup besar
                $sheet->mergeCells('K1:R1');   // Rincian Pembayaran / Biaya
                $sheet->mergeCells('T1:U1');   // Penginapan
                $sheet->mergeCells('V1:W1');   // Pesawat
                $sheet->mergeCells('X1:AA1');  // Dokumen Pembayaran

                // Range header keseluruhan
                $headerRange = 'A1:AA2';

                // Warna background + alignment + border tebal
                $sheet->getStyle($headerRange)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4C6F4'], // warna ungu muda seperti template Excel
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Border untuk seluruh data (header + isi)
                $highestRow = $sheet->getHighestRow();
                $fullRange  = "A1:AA{$highestRow}";
                $sheet->getStyle($fullRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Format angka (rupiah) untuk kolom biaya: K–S
                $sheet->getStyle("K3:S{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');
            },
        ];
    }
}
