<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class RekapPerjadinExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected ?int $tahun;
    protected ?int $bulan;
    protected int $rowNumber = 0;

    public function __construct(?int $tahun = null, ?int $bulan = null)
    {
        $this->tahun = $tahun;
        $this->bulan = $bulan;
    }

    public function query()
    {
        $query = DB::table('laporankeuangan')
            ->join('perjalanandinas', 'laporankeuangan.id_perjadin', '=', 'perjalanandinas.id')
            ->join('pegawaiperjadin', 'pegawaiperjadin.id_perjadin', '=', 'perjalanandinas.id')
            ->join('users', 'pegawaiperjadin.id_user', '=', 'users.nip')
            ->leftJoin('pangkatgolongan', 'users.pangkat_gol_id', '=', 'pangkatgolongan.id')
            ->leftJoin('unitkerja', 'users.id_uke', '=', 'unitkerja.id')
            ->leftJoin('statuslaporan', 'laporankeuangan.id_status', '=', 'statuslaporan.id')
            ->select(
                'laporankeuangan.nomor_spm',
                'laporankeuangan.tanggal_spm',
                'laporankeuangan.nomor_sp2d',
                'laporankeuangan.tanggal_sp2d',
                'laporankeuangan.biaya_rampung',

                'perjalanandinas.tujuan',
                'perjalanandinas.tgl_mulai',
                'perjalanandinas.tgl_selesai',

                'users.nama as nama_pegawai',
                'users.nip',
                'pangkatgolongan.nama_pangkat as pangkat_golongan',
                'unitkerja.nama_uke as unit_kerja',
                'statuslaporan.nama_status as status_laporan'
            )
            ->whereNotNull('laporankeuangan.biaya_rampung');

        if ($this->tahun) {
            $query->whereYear('perjalanandinas.tgl_mulai', $this->tahun);
        }

        if ($this->bulan) {
            $query->whereMonth('perjalanandinas.tgl_mulai', $this->bulan);
        }

        return $query->orderBy('perjalanandinas.tgl_mulai')
                     ->orderBy('users.nama');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pegawai',
            'NIP',
            'Pangkat/Gol',
            'Unit Kerja',
            'Dalam Rangka / Tujuan',
            'Tgl Berangkat',
            'Tgl Kembali',
            'Lama (hari)',
            'Jumlah Dibayarkan (Rp)',
            'No SPM',
            'Tgl SPM',
            'No SP2D',
            'Tgl SP2D',
            'Status Laporan',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        $mulai   = $row->tgl_mulai ? Carbon::parse($row->tgl_mulai) : null;
        $selesai = $row->tgl_selesai ? Carbon::parse($row->tgl_selesai) : null;
        $lama    = ($mulai && $selesai) ? $mulai->diffInDays($selesai) + 1 : null;

        return [
            $this->rowNumber,
            $row->nama_pegawai,
            $row->nip,
            $row->pangkat_golongan ?? '-',
            $row->unit_kerja ?? '-',
            $row->tujuan,
            $mulai ? $mulai->format('d-m-Y') : '-',
            $selesai ? $selesai->format('d-m-Y') : '-',
            $lama ?? '-',
            $row->biaya_rampung ?? 0,
            $row->nomor_spm ?? '-',
            $row->tanggal_spm ? Carbon::parse($row->tanggal_spm)->format('d-m-Y') : '-',
            $row->nomor_sp2d ?? '-',
            $row->tanggal_sp2d ? Carbon::parse($row->tanggal_sp2d)->format('d-m-Y') : '-',
            $row->status_laporan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
