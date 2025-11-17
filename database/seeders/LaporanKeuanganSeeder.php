<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class LaporanKeuanganSeeder extends Seeder
{
    public function run(): void
    {
        $perjadin = DB::table('perjalanandinas')->first()->id;
        $status = DB::table('statuslaporan')->where('nama_status', 'Menunggu Verifikasi')->value('id');

        DB::table('laporankeuangan')->insert([
            [
                'id_perjadin' => $perjadin,
                'id_status' => $status,
                'biaya_rampung' => 3950000,
                'nomor_spm' => null,
                'tanggal_spm' => null,
                'nomor_sp2d' => null,
                'tanggal_sp2d' => null,
                'created_at' => now()
            ]
        ]);
    }
}
