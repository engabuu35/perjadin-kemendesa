<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapPerjadinExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithStyles
{
    protected $tahun;
    protected $bulanMulai;
    protected $bulanSelesai;

    protected $rowNumber = 0; // untuk kolom "No"

    public function __construct($tahun = null, $bulanMulai = null, $bulanSelesai = null)
    {
        $this->tahun        = $tahun;
        $this->bulanMulai   = $bulanMulai;
        $this->bulanSelesai = $bulanSelesai;
    }

    /**
     * QUERY UTAMA – sama logikanya dengan PPKController::tabelRekap
     */
    public function collection()
    {
        // Subquery agregat bukti_laporan
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
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Nama Penginapan' THEN bukti_laporan.keterangan ELSE NULL END) AS nama_hotel"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Kota' THEN bukti_laporan.keterangan ELSE NULL END) AS kota_hotel"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Kode Tiket' THEN bukti_laporan.keterangan ELSE NULL END) AS kode_tiket"),
                DB::raw("MAX(CASE WHEN bukti_laporan.kategori = 'Maskapai' THEN bukti_laporan.keterangan ELSE NULL END) AS maskapai")
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

        if ($this->tahun) {
            $query->whereYear('perjalanandinas.tgl_mulai', $this->tahun);
        }
        if ($this->bulanMulai && $this->bulanSelesai) {
            $query->whereMonth('perjalanandinas.tgl_mulai', '>=', $this->bulanMulai)
                  ->whereMonth('perjalanandinas.tgl_mulai', '<=', $this->bulanSelesai);
        }

        return $query
            ->orderBy('perjalanandinas.tgl_mulai')
            ->orderBy('users.nama')
            ->get();
    }

    /**
     * HEADINGS baris ke-2 (sub-header).
     * Baris ke-1 akan kita buat manual di AfterSheet.
     */
    public function headings(): array
    {
        return [
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
        ];
    }

    /**
     * Mapping setiap row ke 27 kolom (urutan sama dengan headings()).
     */
    public function map($row): array
    {
        $this->rowNumber++;

        $mulai   = $row->tgl_mulai ? \Carbon\Carbon::parse($row->tgl_mulai) : null;
        $selesai = $row->tgl_selesai ? \Carbon\Carbon::parse($row->tgl_selesai) : null;
        $lama    = ($mulai && $selesai) ? $mulai->diffInDays($selesai) + 1 : null;

        return [
            $this->rowNumber,
            $row->nama_uke1 ?? '-',
            $row->nama_uke2 ?? '-',
            $row->nama_pegawai,
            $row->nip,
            $row->pangkat_golongan ?? '-',
            $row->tujuan ?? '-',
            $mulai   ? $mulai->format('d-m-Y')   : '-',
            $selesai ? $selesai->format('d-m-Y') : '-',
            $lama ?? '-',
            $row->biaya_tiket             ?: 0,
            $row->biaya_uang_harian       ?: 0,
            $row->biaya_penginapan        ?: 0,
            $row->biaya_uang_representasi ?: 0,
            $row->biaya_transport         ?: 0,
            $row->biaya_sewa_kendaraan    ?: 0,
            $row->biaya_pengeluaran_riil  ?: 0,
            $row->biaya_sspb              ?: 0,
            $row->jumlah_dibayarkan       ?: 0,
            $row->nama_hotel  ?? '-',
            $row->kota_hotel  ?? '-',
            $row->kode_tiket  ?? '-',
            $row->maskapai    ?? '-',
            $row->nomor_spm   ?? '-',
            $row->tanggal_spm ? \Carbon\Carbon::parse($row->tanggal_spm)->format('d-m-Y') : '-',
            $row->nomor_sp2d  ?? '-',
            $row->tanggal_sp2d ? \Carbon\Carbon::parse($row->tanggal_sp2d)->format('d-m-Y') : '-',
        ];
    }

    /**
     * Style default (misal wrap text).
     */
    public function styles(Worksheet $sheet)
    {
        // Autofit tinggi header, wrap text kolom judul
        $sheet->getStyle('A2:AA2')->getAlignment()->setWrapText(true);

        return [];
    }

    /**
     * Event untuk bikin header bertingkat + warna + border tebal.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Sisipkan baris baru di paling atas:
                // headings() (baris header bawah) geser ke row 2, data ke row 3 dst.
                $sheet->insertNewRowBefore(1, 1);

                // Isi header baris pertama
                $sheet->setCellValue('A1', 'No');
                $sheet->setCellValue('B1', 'Nama UKE-1');
                $sheet->setCellValue('C1', 'Nama UKE-2');
                $sheet->setCellValue('D1', 'Nama Pegawai');
                $sheet->setCellValue('E1', 'NIP');
                $sheet->setCellValue('F1', 'Pangkat / Gol');
                $sheet->setCellValue('G1', 'Tujuan / Dalam Rangka');
                $sheet->setCellValue('H1', 'Tgl Berangkat');
                $sheet->setCellValue('I1', 'Tgl Kembali');
                $sheet->setCellValue('J1', 'Lama (hari)');

                $sheet->setCellValue('K1', 'Rincian Pembayaran / Rincian Biaya (Rp)');
                $sheet->setCellValue('S1', 'Jumlah Dibayarkan (Rp)');
                $sheet->setCellValue('T1', 'Penginapan');
                $sheet->setCellValue('V1', 'Pesawat');
                $sheet->setCellValue('X1', 'Dokumen Pembayaran');

                // Merge kolom yang perlu rowspan dan colspan
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');
                $sheet->mergeCells('D1:D2');
                $sheet->mergeCells('E1:E2');
                $sheet->mergeCells('F1:F2');
                $sheet->mergeCells('G1:G2');
                $sheet->mergeCells('H1:H2');
                $sheet->mergeCells('I1:I2');
                $sheet->mergeCells('J1:J2');

                $sheet->mergeCells('K1:R1'); // Rincian pembayaran (8 kolom)
                $sheet->mergeCells('S1:S2'); // Jumlah dibayarkan
                $sheet->mergeCells('T1:U1'); // Penginapan
                $sheet->mergeCells('V1:W1'); // Pesawat
                $sheet->mergeCells('X1:AA1'); // Dokumen pembayaran (4 kolom)

                // Warna + bold header (baris 1–2)
                $headerRange = 'A1:AA2';

                $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF4C6F4'); // warna ungu muda

                $sheet->getStyle($headerRange)->getFont()->setBold(true);

                // Alignment tengah untuk header
                $sheet->getStyle($headerRange)->getAlignment()->setHorizontal('center');
                $sheet->getStyle($headerRange)->getAlignment()->setVertical('center');

                // Border tebal di header
                $sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(
                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                );

                // Border tipis untuk body data
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A3:AA{$highestRow}")
                    ->getBorders()->getAllBorders()->setBorderStyle(
                        \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    );

                // Auto-size lebar kolom
                foreach (range('A', 'Z') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $sheet->getColumnDimension('AA')->setAutoSize(true);
            },
        ];
    }
}
