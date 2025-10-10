<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaporanKeuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama agar tidak duplikat saat seeding ulang
        DB::table('laporan_keuangan')->truncate();

        // Menambahkan data sampel baru dengan kolom tambahan
        DB::table('laporan_keuangan')->insert([
            [
                'nama_pegawai' => 'Budi Santoso',
                'nip' => '199001012015031001',
                'uang_harian' => 1500000.00,
                'biaya_penginapan' => 2500000.00,
                'transport' => 750000.00,
                'nama_hotel' => 'Hotel Grand Hyatt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pegawai' => 'Citra Lestari',
                'nip' => '199205102016022003',
                'uang_harian' => 1200000.00,
                'biaya_penginapan' => 2000000.00,
                'transport' => 600000.00,
                'nama_hotel' => 'Hotel Aston',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pegawai' => 'Agus Wijaya',
                'nip' => '198811202014011005',
                'uang_harian' => 1800000.00,
                'biaya_penginapan' => 3000000.00,
                'transport' => 900000.00,
                'nama_hotel' => 'Hotel Ritz-Carlton',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}