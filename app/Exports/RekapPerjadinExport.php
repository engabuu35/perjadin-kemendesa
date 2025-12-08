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
        $this->tahun        = $tahun;
        $this->bulanMulai   = $bulanMulai;
        $this->bulanSelesai = $bulanSelesai;
    }

    /**
     * Query data – strukturnya disamakan dengan tabel rekap di PPKController::tabelRekap()
     */
    public function collection(): Collection
    {
        $tahun       = $this->tahun;
        $bulanMulai  = $this->bulanMulai;
        $bulanSelesai= $this->bulanSelesai;

        // SUBQUERY: agregasi bukti_laporan per (id_perjadin, id_user)
        $biayaSub = DB::table('laporan_perjadin as lp')
            ->join('bukti_laporan as bl', 'lp.id', '=', 'bl.id_laporan')
            ->selectRaw('
                lp.id_perjadin,
                lp.id_user,

                SUM(CASE WHEN bl.kategori = "Tiket"             AND bl.nominal > 0 THEN bl.nominal ELSE 0 END) AS biaya_tiket,
                SUM(CASE WHEN bl.kategori = "Uang Harian"       AND bl.nominal > 0 THEN bl.nominal ELSE 0 END) AS biaya_uang_harian,
                SUM(CASE WHEN bl.kategori = "Penginapan"        AND bl.nominal > 0 THEN bl.nominal ELSE 0 END) AS biaya_penginapan,
                SUM(CASE WHEN bl.kategori = "Uang Representasi" AND bl.nominal > 0 THEN bl.nominal ELSE 0 END) AS biaya_uang_representasi,
                SUM(CASE WHEN bl.kategori = "Transport"         AND bl.nominal > 0 THEN bl.nominal ELSE 0 END) AS biaya_transport,
                SUM(CASE WHEN bl.kategori = "Sewa Kendaraan"    AND bl.nominal > 0 THEN bl.nominal ELSE 0 END) AS biaya_sewa_kendaraan,
                SUM(CASE WHEN bl.kategori = "Pengeluaran Riil"  AND bl.nominal > 0 THEN bl.nominal ELSE 0 END) AS biaya_pengeluaran_riil,
                SUM(CASE WHEN bl.kategori = "SSPB"              AND bl.nominal > 0 THEN bl.nominal ELSE 0 END) AS biaya_sspb,

                -- Jumlah dibayarkan per pegawai (semua nominal > 0 kecuali SSPB)
                SUM(CASE WHEN bl.nominal > 0 AND bl.kategori <> "SSPB" THEN bl.nominal ELSE 0 END) AS jumlah_dibayarkan,

                -- Info tambahan yang disimpan di kolom keterangan
                MAX(CASE WHEN bl.kategori = "Nama Penginapan" THEN bl.keterangan ELSE NULL END) AS nama_penginapan,
                MAX(CASE WHEN bl.kategori = "Kota" THEN bl.keterangan ELSE NULL END) AS kota,
                MAX(CASE WHEN bl.kategori = "Jenis Transportasi(Pergi)" THEN bl.keterangan ELSE NULL END) AS jenis_transportasi_pergi,
                MAX(CASE WHEN bl.kategori = "Kode Tiket(Pergi)" THEN bl.keterangan ELSE NULL END) AS kode_tiket_pergi,
                MAX(CASE WHEN bl.kategori = "Nama Transportasi(Pergi)" THEN bl.keterangan ELSE NULL END) AS nama_transportasi_pergi,
                MAX(CASE WHEN bl.kategori = "Jenis Transportasi(Pulang)" THEN bl.keterangan ELSE NULL END) AS jenis_transportasi_pulang,
                MAX(CASE WHEN bl.kategori = "Kode Tiket(Pulang)" THEN bl.keterangan ELSE NULL END) AS kode_tiket_pulang,
                MAX(CASE WHEN bl.kategori = "Nama Transportasi(Pulang)" THEN bl.keterangan ELSE NULL END) AS nama_transportasi_pulang   
            ')
            ->groupBy('lp.id_perjadin', 'lp.id_user');

        // SUBQUERY: mendapatkan hanya ketua/first member per perjadin
        $firstMemberSub = DB::table('pegawaiperjadin as pp')
            ->select('id_perjadin', DB::raw('MIN(id_user) as first_user'))
            ->groupBy('id_perjadin');

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
            ->leftJoinSub($firstMemberSub, 'first', function ($join) {
                $join->on('first.id_perjadin', '=', 'pd.id');
            })
            ->where('sl.nama_status', 'Selesai')
            ->selectRaw('
                pd.id                  AS id_perjadin,
                pd.tgl_mulai,
                pd.tgl_selesai,
                pd.tujuan,
                pd.dalam_rangka,

                u.nama                 AS nama_pegawai,
                u.nip                  AS nip,
                pg.nama_pangkat        AS pangkat_golongan,

                uke1.nama_uke          AS nama_uke1,
                uke2.nama_uke          AS nama_uke2,

                COALESCE(b.biaya_tiket,            0) AS biaya_tiket,
                COALESCE(b.biaya_uang_harian,      0) AS biaya_uang_harian,
                COALESCE(b.biaya_penginapan,       0) AS biaya_penginapan,
                COALESCE(b.biaya_uang_representasi,0) AS biaya_uang_representasi,
                COALESCE(b.biaya_transport,        0) AS biaya_transport,
                COALESCE(b.biaya_sewa_kendaraan,   0) AS biaya_sewa_kendaraan,
                COALESCE(b.biaya_pengeluaran_riil, 0) AS biaya_pengeluaran_riil,
                COALESCE(b.biaya_sspb,             0) AS biaya_sspb,
                COALESCE(b.jumlah_dibayarkan,      0) AS jumlah_dibayarkan,

                b.nama_penginapan,
                b.kota,
                b.jenis_transportasi_pergi,
                b.kode_tiket_pergi,
                b.nama_transportasi_pergi,
                b.jenis_transportasi_pulang,
                b.kode_tiket_pulang,
                b.nama_transportasi_pulang,

                lk.nomor_spm,
                lk.nomor_sp2d,
                lk.biaya_rampung       AS jumlah_sp2d,
                
                -- Added to identify first member of the perjadin
                first.first_user,
                pp.id_user
            ');

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
     * Mapping 1 baris data -> 28 kolom (sesuai template Excel 0–27).
     */
    public function map($row): array
    {
        $this->rowIndex++;

        $mulai   = $row->tgl_mulai ? \Carbon\Carbon::parse($row->tgl_mulai) : null;
        $selesai = $row->tgl_selesai ? \Carbon\Carbon::parse($row->tgl_selesai) : null;
        $lama    = ($mulai && $selesai) ? $mulai->diffInDays($selesai) + 1 : null;

        $jumlahSp2d = ($row->id_user === $row->first_user) ? ($row->jumlah_sp2d ?: 0) : '';
        $nomorSpm = ($row->id_user === $row->first_user) ? ($row->nomor_spm ?? '-') : '-';
        $nomorSp2d = ($row->id_user === $row->first_user) ? ($row->nomor_sp2d ?? '-') : '-';

        return [
            // 1–6: No, UKE, SPM, SP2D, Jumlah SP2D
            $this->rowIndex,                                   // No
            $row->nama_uke1 ?? '-',                            // Nama UKE-1
            $row->nama_uke2 ?? '-',                            // Nama UKE-2
            $nomorSpm,                                         // No SPM (only for first member)
            $nomorSp2d,                                        // No SP2D (only for first member)
            $jumlahSp2d,                                       // Jumlah SP2D (only for first member)

            // 7–15: SPD
            $row->nama_pegawai,                                // Nama Lengkap Tanpa Gelar
            $row->nip,                                         // NIP
            $row->pangkat_golongan ?? '-',                     // Pangkat Golongan
            $row->dalam_rangka ?? '-',                               // Dalam Rangka  (pakai tujuan)
            'Jakarta',                                         // Daerah Asal (statis)
            $row->tujuan ?? '-',                               // Daerah Tujuan (pakai tujuan lagi)
            $mulai   ? $mulai->format('d-m-Y') : '-',          // Tgl SPD Brgkt
            $selesai ? $selesai->format('d-m-Y') : '-',        // Tgl SPD Kmbl
            $lama ?? '-',                                      // Lama Hari

            // 16: Jumlah Dibayarkan per pegawai
            $row->jumlah_dibayarkan ?: 0,

            // 17–24: Rincian Biaya
            $row->biaya_tiket            ?: 0,
            $row->biaya_uang_harian      ?: 0,
            $row->biaya_penginapan       ?: 0,
            $row->biaya_uang_representasi?: 0,
            $row->biaya_transport        ?: 0,
            $row->biaya_sewa_kendaraan   ?: 0,
            $row->biaya_pengeluaran_riil ?: 0,
            $row->biaya_sspb             ?: 0,

            // 25–28: Penginapan & Pesawat
            $row->nama_penginapan ?? '-',
            $row->kota ?? '-',
            $row->jenis_transportasi_pergi ?? '-',
            $row->kode_tiket_pergi ?? '-',
            $row->nama_transportasi_pergi ?? '-', 
            $row->jenis_transportasi_pulang ?? '-',  
            $row->kode_tiket_pulang ?? '-',
            $row->nama_transportasi_pulang ?? '-',
        ];
    }

    /**
     * Header 2 baris.
     * Baris 1: grup besar (SPD, Rincian Pembayaran, Penginapan, Pesawat).
     * Baris 2: nama kolom detail.
     */
    public function headings(): array
    {
        return [
            // Baris header 1
            [
                'No',
                'Nama UKE-1',
                'Nama UKE-2',
                'No SPM',
                'No SP2D',
                'Jumlah SP2D (Rp)',

                'Surat Perjalanan Dinas', '', '', '', '', '', '', '','', // G–O

                'Rincian Pembayaran', '', '', '', '', '', '', '', '', // P–X

                'Penginapan', '',

                'Transportasi', '', '', '', '', '', // AA–AF (6 kolom)
            ],


            // Baris header 2 (nama kolom persis urutan 0–27)
            [
                'No',
                'Nama UKE-1',
                'Nama UKE-2',
                'No SPM',
                'No SP2D',
                'Jumlah SP2D (Rp)',

                'Nama Lengkap Tanpa Gelar',
                'NIP',
                'Pangkat Golongan',
                'Dalam Rangka',
                'Daerah Asal',
                'Daerah Tujuan',
                'Tgl SPD ','',
                'Lama Hari',

                'Jumlah Dibayarkan (Rp)',
                'Rincian Biaya','','','','','','','',

                'Nama Penginapan',
                'Kota',

                'Pergi','','',
                'Pulang','','',
            ],

            [
                'No',
                'Nama UKE-1',
                'Nama UKE-2',
                'No SPM',
                'No SP2D',
                'Jumlah SP2D (Rp)',

                '',
                '',
                '',
                '',
                '',
                '',
                'Brgkt',
                'Kmbl',
                '',

                '',
                'Tiket',
                'Uang Harian',
                'Penginapan',
                'Uang Representasi',
                'Transport',
                'Sewa Kendaraan',
                'Pengeluaran Riil',
                'SSPB',

                '',
                '',

                'Jenis (Pergi)',
                'Kode Tiket (Pergi)',
                'Nama Transport (Pergi)',
                'Jenis (Pulang)',
                'Kode Tiket (Pulang)',
                'Nama Transport (Pulang)',
            ]
        ];
    }

    /**
     * Styling & merge header (warna, border, format angka).
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge header vertical untuk kolom yang tidak punya sub-kolom (baris 1–3)
                $mergeVertical = ['A', 'B', 'C', 'D', 'E', 'F'];
                foreach ($mergeVertical as $col) {
                    $sheet->mergeCells("{$col}1:{$col}3");
                }

                // Merge grup besar
                $sheet->mergeCells('G1:O1');   // Surat Perjalanan Dinas
                $sheet->mergeCells('M2:N2');   //TGL SPD

                $sheet->mergeCells('G2:G3');
                $sheet->mergeCells('H2:H3');
                $sheet->mergeCells('I2:I3');
                $sheet->mergeCells('J2:J3');
                $sheet->mergeCells('K2:K3');
                $sheet->mergeCells('L2:L3');
                $sheet->mergeCells('O2:O3');
                
                $sheet->mergeCells('P1:X1');   // Rincian Pembayaran
                $sheet->mergeCells('P2:P3');   // Jumlah Dibayarkan
                $sheet->mergeCells('Q2:X2');   // Rincian Biaya

                // Penginapan
                $sheet->mergeCells('Y1:Z1');
                $sheet->mergeCells('Y2:Y3');  // Nama Penginapan
                $sheet->mergeCells('Z2:Z3');  // Kota

                // Transportasi (6 kolom)
                $sheet->mergeCells('AA1:AF1');

                // Pergi (di dalam Transportasi)
                $sheet->mergeCells('AA2:AC2');

                // Pulang
                $sheet->mergeCells('AD2:AF2');


                // Range header keseluruhan
                $headerRange = 'A1:AF3';

                // Warna background + alignment + border tebal
                $sheet->getStyle($headerRange)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F4C6F4'], // ungu muda
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
                $fullRange  = "A1:AF{$highestRow}";
                $sheet->getStyle($fullRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Format angka untuk kolom rupiah:
                // F  = Jumlah SP2D
                // P  = Jumlah Dibayarkan
                // Q–X = Rincian Biaya
                $sheet->getStyle("F3:F{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');

                $sheet->getStyle("P3:X{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0');
            },
        ];
    }
}
