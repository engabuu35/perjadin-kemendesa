<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusPerjadinSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('statusperjadin')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $statuses = [
            // 1. Awal
            ['nama_status' => 'Draft'],
            ['nama_status' => 'Belum Berlangsung'],
            ['nama_status' => 'Sedang Berlangsung'],

            // 2. Pegawai Selesai -> Masuk ke PIC
            ['nama_status' => 'Menunggu Verifikasi Laporan'], 

            // 3. PIC Kirim -> Masuk ke PPK
            ['nama_status' => 'Menunggu Verifikasi'], 

            // 4. Balikan dari PPK -> Masuk ke PIC lagi
            ['nama_status' => 'Perlu Revisi'],

            // 5. Final
            ['nama_status' => 'Selesai'],
            ['nama_status' => 'Ditolak'], 
        ];

        DB::table('statusperjadin')->insert($statuses);
    }
}