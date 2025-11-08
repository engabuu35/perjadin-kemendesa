<?php

namespace Database\Seeders;

// Pastikan Anda menambahkan ini
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriBiayaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Perintah ini akan mengisi tabel 'kategoribiaya'
        DB::table('kategoribiaya')->insert([
            [ 'nama_kategori' => 'Tiket' ],
            [ 'nama_kategori' => 'Uang Harian' ],
            [ 'nama_kategori' => 'Penginapan' ],
            [ 'nama_kategori' => 'Uang Representasi' ],
            [ 'nama_kategori' => 'Transport' ],
            [ 'nama_kategori' => 'Sewa Kendaraan' ],
            [ 'nama_kategori' => 'Pengeluaran Riil' ],
            [ 'nama_kategori' => 'SSPB' ],
        ]);
    }
}