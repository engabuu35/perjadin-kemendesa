<?php

namespace Database\Seeders;

// 1. PASTIKAN ANDA MENAMBAHKAN INI
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PangkatGolonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 2. Perintah ini akan mengisi tabel 'pangkat_golongan'
        DB::table('pangkatgolongan')->insert([
            [
                'kode_golongan' => 'III/a',
                'nama_pangkat' => 'Penata Muda'
            ],
            [
                'kode_golongan' => 'III/b',
                'nama_pangkat' => 'Penata Muda Tk. I'
            ],
            [
                'kode_golongan' => 'III/c',
                'nama_pangkat' => 'Penata'
            ],
            [
                'kode_golongan' => 'III/d',
                'nama_pangkat' => 'Penata Tk. I'
            ],
            [
                'kode_golongan' => 'IV/a',
                'nama_pangkat' => 'Pembina'
            ],
            [
                'kode_golongan' => 'IV/b',
                'nama_pangkat' => 'Pembina Tk. I'
            ],
            [
                'kode_golongan' => '-', // Menggunakan strip '-' untuk kode PPNPN
                'nama_pangkat' => 'PPNPN'
            ],
        ]);
    }
}