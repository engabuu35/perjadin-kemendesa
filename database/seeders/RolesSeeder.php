<?php

namespace Database\Seeders;

// 1. PASTIKAN ANDA MENAMBAHKAN INI
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 2. Perintah ini akan mengisi tabel 'roles'
        DB::table('roles')->insert([
            [
                'kode' => 'PIMPINAN',
                'nama' => 'Pimpinan'
            ],
            [
                'kode' => 'PEGAWAI',
                'nama' => 'Pegawai'
            ],
            [
                'kode' => 'PPK',
                'nama' => 'PPK'
            ],
            [
                'kode' => 'PIC',
                'nama' => 'PIC'
            ],
        ]);
    }
}