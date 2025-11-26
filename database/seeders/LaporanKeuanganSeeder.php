<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaporanKeuanganSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan StatusLaporan sudah di-seed (StatusLaporanSeeder)
        // Ambil id statuslaporan yang relevan
        $statusMenunggu = DB::table('statuslaporan')->where('nama_status', 'Menunggu Verifikasi')->value('id');
        $statusDisetujui = DB::table('statuslaporan')->where('nama_status', 'Disetujui')->value('id');

        // Ambil perjadin yang dibuat di PerjadinDataSeeder
        $perjadinMenunggu = DB::table('perjalanandinas')->where('nomor_surat', 'ST/VERIFY/004/2025')->value('id');
        $perjadinSelesai   = DB::table('perjalanandinas')->where('nomor_surat', 'ST/FIN/005/2025')->value('id');

        if ($perjadinMenunggu) {
            DB::table('laporankeuangan')->insert([
                'id_perjadin' => $perjadinMenunggu,
                'id_status' => $statusMenunggu ?: null,
                'verified_by' => null,
                'verified_at' => null,
                'nomor_spm' => null,
                'tanggal_spm' => null,
                'nomor_sp2d' => null,
                'tanggal_sp2d' => null,
                'biaya_rampung' => 4400000, // contoh total dari bukti
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($perjadinSelesai) {
            DB::table('laporankeuangan')->insert([
                'id_perjadin' => $perjadinSelesai,
                'id_status' => $statusDisetujui ?: null,
                'verified_by' => '198001012010011001', // contoh NIP verifier
                'verified_at' => now()->subDays(10),
                'nomor_spm' => 'SPM/2025/001',
                'tanggal_spm' => now()->subDays(9),
                'nomor_sp2d' => 'SP2D/2025/001',
                'tanggal_sp2d' => now()->subDays(5),
                'biaya_rampung' => 2000000,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(5),
            ]);
        }
    }
}
