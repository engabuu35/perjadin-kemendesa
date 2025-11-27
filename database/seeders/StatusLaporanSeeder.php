<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusLaporanSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('statuslaporan')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $statuses = [
            ['nama_status' => 'Belum Dibuat'],
            ['nama_status' => 'Perlu Tindakan'],    // Saat di meja PIC
            ['nama_status' => 'Menunggu Verifikasi'], // Saat di meja PPK
            ['nama_status' => 'Perlu Revisi'],      // Ditolak
            ['nama_status' => 'Selesai'],           // Disetujui
        ];

        DB::table('statuslaporan')->insert($statuses);
    }
}