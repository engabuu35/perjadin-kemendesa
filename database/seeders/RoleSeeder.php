<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <-- TAMBAHKAN BARIS INI

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel dulu untuk menghindari duplikasi
        // DB::table('roles')->truncate(); // Truncate tidak mereset auto-increment, jadi kita pakai delete saja
        DB::table('roles')->delete();


        DB::table('roles')->insert([
            ['name' => 'Admin IT', 'slug' => 'admin-it', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Staf PPK', 'slug' => 'ppk', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pimpinan', 'slug' => 'pimpinan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pegawai', 'slug' => 'pegawai', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}