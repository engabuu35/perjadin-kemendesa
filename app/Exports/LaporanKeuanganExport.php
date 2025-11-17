<?php

namespace App\Exports;

use App\Models\LaporanKeuangan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanKeuanganExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    /**
    * @param array $filters
    */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
    * @return \Illuminate\Database\Query\Builder
    */
    public function query()
    {
        $query = LaporanKeuangan::query()->with([
            'perjalananDinas:id,nomor_surat,tujuan,tgl_mulai,tgl_selesai',
            'status:id,nama_status',
            'verifier:nip,nama',
            'rincianAnggaran' // To calculate total
        ]);

        // Apply filters passed from controller
        if (!empty($this->filters['status'])) {
            $query->whereHas('status', function ($q) {
                $q->where('nama_status', $this->filters['status']);
            });
        }
        
        if (!empty($this->filters['search'])) {
            $query->whereHas('perjalananDinas', function ($q) {
                $q->where('nomor_surat', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('tujuan', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        return $query;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'ID Laporan',
            'Nomor Surat Perjadin',
            'Tujuan Perjadin',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Status Laporan',
            'Diverifikasi Oleh',
            'Tanggal Verifikasi',
            'Nomor SPM',
            'Tanggal SPM',
            'Nomor SP2D',
            'Tanggal SP2D',
            'Biaya Rampung (Final)',
        ];
    }

    /**
    * @param LaporanKeuangan $laporan
    *
    * @return array
    */
    public function map($laporan): array
    {
        return [
            $laporan->id,
            $laporan->perjalananDinas->nomor_surat ?? 'N/A',
            $laporan->perjalananDinas->tujuan ?? 'N/A',
            $laporan->perjalananDinas ? $laporan->perjalananDinas->tgl_mulai->format('d-m-Y') : '',
            $laporan->perjalananDinas ? $laporan->perjalananDinas->tgl_selesai->format('d-m-Y') : '',
            $laporan->status->nama_status ?? 'N/A',
            $laporan->verifier->nama ?? 'Belum Diverifikasi',
            $laporan->verified_at ? $laporan->verified_at->format('d-m-Y H:i') : '',
            $laporan->nomor_spm,
            $laporan->tanggal_spm ? $laporan->tanggal_spm->format('d-m-Y') : '',
            $laporan->nomor_sp2d,
            $laporan->tanggal_sp2d ? $laporan->tanggal_sp2d->format('d-m-Y') : '',
            $laporan->biaya_rampung,
            // Jika biaya rampung null, kita bisa hitung manual dari rincian
            // $laporan->biaya_rampung ?? $laporan->rincianAnggaran->sum('jumlah_biaya'),
        ];
    }

    /**
    * @param Worksheet $sheet
    */
    public function styles(Worksheet $sheet)
    {
        // Style the first row (headings)
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}