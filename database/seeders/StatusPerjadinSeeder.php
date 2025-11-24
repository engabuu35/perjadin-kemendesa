<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusPerjadinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset table agar tidak duplicate saat seeding ulang
        // Note: Disable foreign key check biar bisa truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('statusperjadin')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Daftar Status Gabungan (File Anda + Kebutuhan Sistem)
        $statuses = [
            [
                'nama_status' => 'Draft / Menunggu Persetujuan' 
                // Status awal saat surat baru dibuat tapi belum di-acc pimpinan.
            ],
            [
                'nama_status' => 'Belum Berlangsung' 
                // Status default saat sudah di-acc tapi tanggal mulai > hari ini.
            ],
            [
                'nama_status' => 'Sedang Berlangsung' 
                // Otomatis diubah oleh sistem jika tgl_mulai <= hari ini.
            ],
            [
                'nama_status' => 'Menunggu Laporan' 
                // (Warning) Otomatis diubah jika tgl_selesai < hari ini TAPI pegawai belum klik selesai.
            ],
            [
                'nama_status' => 'Menunggu Verifikasi Laporan' 
                // [PENTING] Status setelah pegawai klik "Selesaikan & Kirim". Masuk ke dashboard PIC.
            ],
            [
                'nama_status' => 'Selesai' 
                // Status akhir setelah diverifikasi oleh PIC/PPK.
            ],
            [
                'nama_status' => 'Ditolak' 
                // Jika laporan dikembalikan untuk revisi.
            ],
            [
                'nama_status' => 'Diselesaikan Manual' 
                // Selesai paksa (digunakan saat PIC menyudahi tugas pegawai secara manual).
            ],
            [
                'nama_status' => 'Dibatalkan' 
                // Jika penugasan pegawai dibatalkan.
            ],
        ];

        DB::table('statusperjadin')->insert($statuses);
    }
}