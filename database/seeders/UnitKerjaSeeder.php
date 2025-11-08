<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitKerjaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Induk Tertinggi (UKE-1)
        DB::table('unitkerja')->insert([
            [
                'id_induk' => null, // Paling atas
                'kode_uke' => 'ITJEN',
                'nama_uke' => 'INSPEKTORAT JENDERAL'
            ]
        ]);

        // 2. Ambil ID Induk (UKE-1)
        $idItjen = DB::table('unitkerja')->where('kode_uke', 'ITJEN')->first()->id;

        // 3. Buat 6 Anak (UKE-2) di bawah ITJEN
        DB::table('unitkerja')->insert([
            [
                'id_induk' => $idItjen,
                'kode_uke' => 'SETITJEN',
                'nama_uke' => 'SEKRETARIAT INSPEKTORAT JENDERAL'
            ],
            [
                'id_induk' => $idItjen,
                'kode_uke' => 'IRWIL1',
                'nama_uke' => 'INSPEKTORAT I'
            ],
            [
                'id_induk' => $idItjen,
                'kode_uke' => 'IRWIL2',
                'nama_uke' => 'INSPEKTORAT II'
            ],
            [
                'id_induk' => $idItjen,
                'kode_uke' => 'IRWIL3',
                'nama_uke' => 'INSPEKTORAT III'
            ],
            [
                'id_induk' => $idItjen,
                'kode_uke' => 'IRWIL4',
                'nama_uke' => 'INSPEKTORAT IV'
            ],
            [
                'id_induk' => $idItjen,
                'kode_uke' => 'IRWIL5',
                'nama_uke' => 'INSPEKTORAT V'
            ],
        ]);
        
        // CATATAN: Kita tidak membuat Subbag TU atau Kelompok Auditor
        // karena PIC ditugaskan di level UKE-2 (Inspektorat I, II, dll)
    }
}