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
        // DIUBAH: .call menjadi ->call
        $this->call([
            PangkatGolonganSeeder::class,
            RolesSeeder::class,
            StatusPerjadinSeeder::class,
            StatusLaporanSeeder::class,
            KategoriBiayaSeeder::class,
            TipeGeotaggingSeeder::class,
        ]);

        // 2. PANGGIL TABEL ORGANISASI
        // DIUBAH: .call menjadi ->call
        $this->call([
            UnitKerjaSeeder::class,
        ]);

        // 3. PANGGIL TABEL PENDUKUNG ORGANISASI
        // DIUBAH: .call menjadi ->call
        $this->call([
            LingkupAuditSeeder::class,
        ]);

        // 4. PANGGIL DATA USER (TOTAL 30 USERS)
        // Seeder ini sekarang juga mengisi tabel 'penugasanperan'
        // DIUBAH: .call menjadi ->call
        $this->call([
            UsersSeeder::class,
        ]);

        // 5. PANGGIL SEEDER DATA TRANSAKSI PERJADIN
        // PENTING: Ini harus dijalankan setelah Users dan Master
        // DIUBAH: .call menjadi ->call
        $this->call([
            PerjadinDataSeeder::class,
        ]);
        
        // 6. Panggilan ke PenugasanPeranSeeder::class DIHAPUS
        // $this->call([
        //     PenugasanPeranSeeder::class,
        // ]);
        $this->call([
            LaporanKeuanganSeeder::class,
        ]);

        $this->call([
        DummyPegawaiWithPerjadinSeeder::class,
        ]);

    }
}