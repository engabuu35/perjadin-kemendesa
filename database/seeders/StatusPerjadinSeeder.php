<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            ['nama_status' => 'Draft / Menunggu Persetujuan'],
            ['nama_status' => 'Belum Berlangsung'],
            ['nama_status' => 'Sedang Berlangsung'],
            ['nama_status' => 'Menunggu Laporan'],
            
            // Status saat pegawai sudah selesai, sedang diisi PIC
            ['nama_status' => 'Menunggu Verifikasi Laporan'], 
            
            // [BARU] Status setelah PIC klik "Kirim ke PPK"
            ['nama_status' => 'Menunggu Validasi PPK'], 
            
            ['nama_status' => 'Selesai'], // Disetujui PPK
            ['nama_status' => 'Ditolak'],
            ['nama_status' => 'Diselesaikan Manual'],
            ['nama_status' => 'Dibatalkan'],
        ];

        DB::table('statusperjadin')->insert($statuses);
    }
}