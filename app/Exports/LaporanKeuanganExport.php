<?php

namespace App\Exports;

use App\Models\LaporanKeuangan;
use Maatwebsite\Excel\Concerns\FromQuery; // Mengganti FromCollection menjadi FromQuery
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanKeuanganExport implements FromQuery, WithHeadings, WithMapping // Mengganti di sini
{
    /**
    * @return \Illuminate\Database\Query\Builder
    */
    public function query() // Mengganti nama method dari collection() menjadi query()
    {
        // Hanya memberikan query builder, bukan mengambil semua data sekaligus
        return LaporanKeuangan::query();
    }

    /**
     * Mendefinisikan header untuk kolom-kolom Excel.
     */
    public function headings(): array
    {
        return [
            'Nama Pegawai',
            'NIP',
            'Uang Harian (Rp)',
            'Biaya Penginapan (Rp)',
            'Transport (Rp)',
            'Nama Hotel',
        ];
    }

    /**
     * Memetakan data untuk setiap baris di Excel.
     * Urutannya harus sama dengan headings().
     */
    public function map($laporan): array
    {
        return [
            $laporan->nama_pegawai,
            $laporan->nip,
            $laporan->uang_harian,
            $laporan->biaya_penginapan,
            $laporan->transport,
            $laporan->nama_hotel,
        ];
    }
}

