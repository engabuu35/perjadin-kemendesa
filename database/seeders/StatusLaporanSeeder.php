<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusLaporanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('statuslaporan')->insert([
            [
                'nama_status' => 'Belum Dibuat' // Laporan sama sekali belum ada
            ],
            [
                'nama_status' => 'Draft' // PIC sedang mengisi, belum dikirim
            ],
            [
                'nama_status' => 'Menunggu Verifikasi' // PIC sudah kirim, PPK belum periksa
            ],
            [
                'nama_status' => 'Perlu Revisi' // PPK tolak, PIC harus perbaiki
            ],
            [
                'nama_status' => 'Disetujui' // PPK sudah setuju, siap bayar
            ],
            [
                'nama_status' => 'Selesai Dibayar' // Proses selesai
            ],
        ]);
    }
}