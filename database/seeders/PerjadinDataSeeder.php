<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerjadinDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // === AMBIL DATA MASTER ===
        $statusSelesai = DB::table('statusperjadin')->where('nama_status', 'Selesai')->value('id');
        $statusProses = DB::table('statusperjadin')->where('nama_status', 'Sedang Berlangsung')->value('id');
        $statusTunggu = DB::table('statusperjadin')->where('nama_status', 'Menunggu Laporan')->value('id');
        
        $tipeGeotagging = DB::table('tipegeotagging')->where('nama_tipe', 'Laporan Harian')->value('id');
        
        // === DATA USER ===
        $nipPic1 = '199103032021031003';
        $nipPimpinan = '198001012010011001';
        
        // User Grup 1 (Untuk data selesai)
        $usersGrup1 = ['199103032021031003', '199204042022042004', '198001012010011001', '199909092029092009', 'PPNPN-001'];
        
        // User Grup 3 (Untuk data on-progress)
        $usersGrup3 = [];
        for ($i = 1; $i <= 10; $i++) {
            $usersGrup3[] = '2001111120311110' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        // ===============================================
        // GRUP 1: 5 DATA SELESAI (FILE GABUNGAN ADA)
        // ===============================================
        for ($i = 0; $i < 5; $i++) {
            $tglMulai = now()->subMonths(2)->addDays($i * 5);
            $tglSelesai = $tglMulai->copy()->addDays(3);

            // Insert perjalanandinas
            $perjadinId = DB::table('perjalanandinas')->insertGetId([
                'id_pembuat' => $usersGrup1[0],
                'id_status' => $statusSelesai,
                'id_atasan' => $nipPimpinan,
                'approved_by' => $nipPimpinan,
                'approved_at' => $tglMulai->copy()->subDay(),
                'nomor_surat' => 'ST/' . (100 + $i) . '/ITJEN/2025',
                'tanggal_surat' => $tglMulai->copy()->subDays(2),
                'tujuan' => 'Audit Gabungan Kota ' . ($i+1),
                'tgl_mulai' => $tglMulai,
                'tgl_selesai' => $tglSelesai,
                'uraian' => 'Audit selesai dan dokumen lengkap.',
                'pdf_keuangan' => 'tiket_tim_gabungan.pdf',
                'surat_tugas' => 'surat_tugas_tim_' . ($i+1) . '.pdf',
                'tgl_acc' => $tglMulai->copy()->subDay(),
                'created_at' => $tglMulai,
                'updated_at' => $tglSelesai,
            ]);

            // Insert pegawai
            DB::table('pegawaiperjadin')->insert([
                ['id_perjadin' => $perjadinId, 'id_user' => $usersGrup1[($i) % 5], 'role_perjadin' => 'Ketua', 'is_lead' => 1],
                ['id_perjadin' => $perjadinId, 'id_user' => $usersGrup1[($i + 1) % 5], 'role_perjadin' => 'Anggota', 'is_lead' => 0],
            ]);
        }

        // ===============================================
        // GRUP 3: 5 DATA ON PROGRESS (FILE KOSONG)
        // ===============================================
        $counter = 0;
        for ($j = 0; $j < 5; $j++) {
            $userA = $usersGrup3[$counter];
            $userB = $usersGrup3[$counter + 1];
            $counter += 2;

            $status = $statusProses;
            $tglMulai = now();
            $tglSelesai = now()->addDays(2);

            if ($j >= 3) { // skenario telat
                $status = $statusTunggu;
                $tglMulai = now()->subDays(5);
                $tglSelesai = now()->subDays(2);
            }

            $perjadinId = DB::table('perjalanandinas')->insertGetId([
                'id_pembuat' => $nipPic1,
                'id_status' => $status,
                'id_atasan' => $nipPimpinan,
                'approved_by' => $nipPimpinan,
                'nomor_surat' => 'ST/TIM-GAB/' . (200 + $j),
                'tanggal_surat' => $tglMulai->copy()->subDay(),
                'tujuan' => 'Tugas Tim ' . ($j+1),
                'tgl_mulai' => $tglMulai,
                'tgl_selesai' => $tglSelesai,
                'uraian' => null,
                'pdf_keuangan' => null,
                'surat_tugas' => null,
                'tgl_acc' => null,
            ]);

            // Insert pegawai
            DB::table('pegawaiperjadin')->insert([
                ['id_perjadin' => $perjadinId, 'id_user' => $userA, 'role_perjadin' => 'Ketua', 'is_lead' => 1],
                ['id_perjadin' => $perjadinId, 'id_user' => $userB, 'role_perjadin' => 'Anggota', 'is_lead' => 0],
            ]);

            // Geotagging
            if ($status == $statusProses) {
                DB::table('geotagging')->insert([
                    'id_perjadin' => $perjadinId,
                    'id_user' => $userA,
                    'id_tipe' => $tipeGeotagging,
                    'latitude' => -6.200000,
                    'longitude' => 106.816666,
                    'created_at' => now(),
                ]);
            }
        }
    }
}
