<?php

namespace Database\Seeders;

// 1. PASTIKAN ANDA MENAMBAHKAN INI
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusPerjadinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 2. Perintah ini akan mengisi tabel 'status_perjadin'
        // Status ini melacak progres SETIAP PEGAWAI
        DB::table('statusperjadin')->insert([
            [
                'nama_status' => 'Belum Berlangsung' 
                // Status default saat PIC baru menugaskan pegawai.
            ],
            [
                'nama_status' => 'Sedang Berlangsung' 
                // Otomatis diubah oleh sistem jika tgl_mulai == hari ini.
            ],
            [
                'nama_status' => 'Menunggu Laporan' 
                // Otomatis diubah jika tgl_selesai < hari ini TAPI laporan_individu masih KOSONG.
            ],
            [
                'nama_status' => 'Selesai' 
                // Selesai normal (otomatis diubah saat pegawai men-submit laporan_individu).
            ],
            [
                'nama_status' => 'Diselesaikan Manual' 
                // Selesai paksa (digunakan saat PIC menyudahi tugas pegawai secara manual).
            ],
            [
                'nama_status' => 'Dibatalkan' 
                // Jika penugasan pegawai dibatalkan.
            ],
        ]);
    }
}