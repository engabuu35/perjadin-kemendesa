<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipeGeotaggingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tipegeotagging')->insert([
            [
                'nama_tipe' => 'Laporan Harian' 
                // Tipe default untuk tagging harian
            ],
            [
                'nama_tipe' => 'Laporan Khusus' 
                // Opsional, jika ada kejadian mendadak yang perlu dilaporkan
            ],
        ]);
    }
}