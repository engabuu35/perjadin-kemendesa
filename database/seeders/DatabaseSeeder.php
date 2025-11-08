<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. PANGGIL SEMUA TABEL MASTER (INDEPENDEN)
        $this->call([
            PangkatGolonganSeeder::class,
            RolesSeeder::class,
            StatusPerjadinSeeder::class,
            StatusLaporanSeeder::class,
            KategoriBiayaSeeder::class,
            TipeGeotaggingSeeder::class,
        ]);

        // 2. PANGGIL TABEL ORGANISASI (TERGANTUNG DIRINYA SENDIRI)
        $this->call([
            UnitKerjaSeeder::class,
        ]);

        // 3. PANGGIL TABEL PENDUKUNG ORGANISASI
        // (Tergantung UnitKerja)
        $this->call([
            LingkupAuditSeeder::class,
        ]);

        // 4. PANGGIL DATA DUMMY (CONTOH)
        // (Tergantung PangkatGolongan dan UnitKerja)
        $this->call([
            UsersSeeder::class,
        ]);

        // 5. PANGGIL SEEDER JUNCTION
        // (Tergantung Users dan Roles)
        $this->call([
            PenugasanPeranSeeder::class,
        ]);
    }
}