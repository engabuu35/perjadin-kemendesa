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
            ['nama_status' => 'Belum Berlangsung'], //jika tanggal belum mulai
            ['nama_status' => 'Sedang Berlangsung'], //jika tanggal sudah mulai
            ['nama_status' => 'Pembuatan Laporan'],  //jika perjalanan dinas selesai, laporan keungan pic
            ['nama_status' => 'Menunggu Validasi PPK'], //jika laporan sudah dikirim ke ppk, menunggu validasi ppk
            ['nama_status' => 'Selesai'], //jika laporan divalidasi ppk
            ['nama_status' => 'Perlu Tindakan'], //jika ada salah dan ppk minta revisi
            ['nama_status' => 'Diselesaikan Manual'],
            ['nama_status' => 'Dibatalkan'],
        ];

        DB::table('statusperjadin')->insert($statuses);
    }
}