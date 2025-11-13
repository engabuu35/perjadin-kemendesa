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
        $statusPerjadinSelesai = DB::table('statusperjadin')->where('nama_status', 'Selesai')->value('id');
        $statusPerjadinOnProgress = DB::table('statusperjadin')->where('nama_status', 'Sedang Berlangsung')->value('id');
        $statusPerjadinTungguLaporan = DB::table('statusperjadin')->where('nama_status', 'Menunggu Laporan')->value('id');
        
        $statusLaporanSelesai = DB::table('statuslaporan')->where('nama_status', 'Selesai Dibayar')->value('id');
        $statusLaporanDraft = DB::table('statuslaporan')->where('nama_status', 'Draft')->value('id');
        $statusLaporanBelum = DB::table('statuslaporan')->where('nama_status', 'Belum Dibuat')->value('id');

        $tipeGeotagging = DB::table('tipegeotagging')->where('nama_tipe', 'Laporan Harian')->value('id');
        
        $kategoriTiket = DB::table('kategoribiaya')->where('nama_kategori', 'Tiket')->value('id');
        $kategoriHotel = DB::table('kategoribiaya')->where('nama_kategori', 'Penginapan')->value('id');
        $kategoriUangHarian = DB::table('kategoribiaya')->where('nama_kategori', 'Uang Harian')->value('id');

        // === AMBIL DATA USER (LANGSUNG DARI NIP, BUKAN EMAIL) ===
        $nipPic1 = '199103032021031003';
        $nipPic2 = '199204042022042004';
        $nipPpk = '199002022020022002';
        $nipPimpinan = '198001012010011001';
        $nipPegawaiMurni = '199909092029092009';
        $nipPpn = 'PPNPN-001';

        // User Grup 1 (PICs, Pimpinan, dll)
        $usersGrup1 = [$nipPic1, $nipPic2, $nipPimpinan, $nipPegawaiMurni, $nipPpn];
        // User Grup 3 (On Progress)
        $usersGrup3 = [];
        for ($i = 1; $i <= 10; $i++) {
            // NIP ini harus sama persis dengan yang dibuat di UsersSeeder
            $usersGrup3[] = '2001111120311110' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }


        // ===============================================
        // === GRUP 1: 5 DATA PERJADIN LENGKAP & SELESAI ===
        // ===============================================
        // Kita buat 5 contoh perjadin yang sudah selesai untuk user grup 1
        for ($i = 0; $i < 5; $i++) {
            $pembuatNip = $usersGrup1[$i % count($usersGrup1)]; // PIC atau pembuat
            $pelaksanaNip = $usersGrup1[($i + 1) % count($usersGrup1)]; // Pegawai pelaksana
            $tglMulai = now()->subMonths(2)->addDays($i * 5);
            $tglSelesai = $tglMulai->copy()->addDays(3);

            // 1. Buat Perjalanan Dinas
            $perjadinId = DB::table('perjalanandinas')->insertGetId([
                'id_pembuat' => $pembuatNip,
                'id_status' => $statusPerjadinSelesai,
                'approved_by' => $nipPimpinan,
                'approved_at' => $tglMulai->copy()->subDay(),
                'nomor_surat' => 'ST/' . (100 + $i) . '/ITJEN/2025',
                'tanggal_surat' => $tglMulai->copy()->subDays(2),
                'tujuan' => 'Audit Kinerja di ' . ['Medan', 'Surabaya', 'Makassar', 'Bandung', 'Denpasar'][$i],
                'tgl_mulai' => $tglMulai,
                'tgl_selesai' => $tglSelesai,
                'hasil_perjadin' => 'Telah dilaksanakan audit kinerja dengan hasil baik.',
                'created_at' => $tglMulai->copy()->subDays(3),
                'updated_at' => $tglSelesai->copy()->addDays(2),
            ]);

            // 2. Tugaskan Pegawai
            DB::table('pegawaiperjadin')->insert([
                'id_perjadin' => $perjadinId,
                'id_user' => $pelaksanaNip,
                'role_perjadin' => 'Anggota Tim',
                'is_lead' => true,
                'laporan_individu' => 'Laporan individu untuk ' . $pelaksanaNip . ' sudah selesai.',
            ]);

            // 3. Buat Laporan Keuangan Selesai
            $laporanId = DB::table('laporankeuangan')->insertGetId([
                'id_perjadin' => $perjadinId,
                'id_status' => $statusLaporanSelesai,
                'verified_by' => $nipPpk,
                'verified_at' => $tglSelesai->copy()->addDays(5),
                'nomor_spm' => 'SPM/00' . $i . '/2025',
                'tanggal_spm' => $tglSelesai->copy()->addDays(6),
                'nomor_sp2d' => 'SP2D/00' . $i . '/2025',
                'tanggal_sp2d' => $tglSelesai->copy()->addDays(7),
                'biaya_rampung' => 4500000,
                'created_at' => $tglSelesai->copy()->addDay(),
                'updated_at' => $tglSelesai->copy()->addDays(7),
            ]);

            // 4. Buat Rincian Anggaran
            DB::table('rinciananggaran')->insert([
                [
                    'id_laporan' => $laporanId,
                    'id_kategori' => $kategoriTiket,
                    'tanggal_biaya' => $tglMulai,
                    'deskripsi_biaya' => 'Tiket Pesawat PP',
                    'jumlah_biaya' => 2000000,
                    'path_bukti' => null,
                ],
                [
                    'id_laporan' => $laporanId,
                    'id_kategori' => $kategoriHotel,
                    'tanggal_biaya' => $tglMulai,
                    'deskripsi_biaya' => 'Hotel 3 Malam',
                    'jumlah_biaya' => 1500000,
                    'path_bukti' => null,
                ],
                [
                    'id_laporan' => $laporanId,
                    'id_kategori' => $kategoriUangHarian,
                    'tanggal_biaya' => $tglMulai,
                    'deskripsi_biaya' => 'Uang Harian 4 Hari',
                    'jumlah_biaya' => 1000000,
                    'path_bukti' => null,
                ],
            ]);

            // 5. Buat Geotagging
            DB::table('geotagging')->insert([
                [
                    'id_perjadin' => $perjadinId,
                    'id_user' => $pelaksanaNip,
                    'id_tipe' => $tipeGeotagging,
                    'latitude' => -6.175392,
                    'longitude' => 106.827153, // Monas
                    'created_at' => $tglMulai->copy()->addHours(8),
                ],
                [
                    'id_perjadin' => $perjadinId,
                    'id_user' => $pelaksanaNip,
                    'id_tipe' => $tipeGeotagging,
                    'latitude' => -6.175392,
                    'longitude' => 106.827153, // Monas
                    'created_at' => $tglSelesai->copy()->addHours(17),
                ],
            ]);
        }


        // ====================================================
        // === GRUP 3: 10 DATA PERJADIN ON-PROGRESS / AKTIF ===
        // ====================================================
        // Kita buat 10 perjadin untuk 10 user di grup 3
        foreach ($usersGrup3 as $index => $pelaksanaNip) {
            
            // 5 data "Sedang Berlangsung", 5 data "Menunggu Laporan"
            if ($index < 5) {
                // "Sedang Berlangsung" (Asumsi hari ini 13 Nov 2025)
                $tglMulai = now()->subDays(1); // Mulai kemarin
                $tglSelesai = now()->addDays(3); // Selesai 3 hari lagi
                $statusPerjadin = $statusPerjadinOnProgress;
                $statusLaporan = $statusLaporanBelum;
                $hasilPerjadin = null;
                $laporanIndividu = null;
            } else {
                // "Menunggu Laporan"
                $tglMulai = now()->subDays(10); // Mulai 10 hari lalu
                $tglSelesai = now()->subDays(5); // Selesai 5 hari lalu
                $statusPerjadin = $statusPerjadinTungguLaporan;
                $statusLaporan = $statusLaporanDraft;
                $hasilPerjadin = null; // Belum ada laporan
                $laporanIndividu = null; // Pegawai belum submit
            }

            // 1. Buat Perjalanan Dinas
            $perjadinId = DB::table('perjalanandinas')->insertGetId([
                'id_pembuat' => $nipPic1, // Dibuat oleh PIC 1
                'id_status' => $statusPerjadin,
                'approved_by' => $nipPimpinan,
                'approved_at' => $tglMulai->copy()->subDay(),
                'nomor_surat' => 'ST/' . (200 + $index) . '/ITJEN/2025',
                'tanggal_surat' => $tglMulai->copy()->subDays(2),
                'tujuan' => 'Rapat Koordinasi di ' . ['Semarang', 'Yogyakarta', 'Manado', 'Palembang', 'Pontianak'][$index % 5],
                'tgl_mulai' => $tglMulai,
                'tgl_selesai' => $tglSelesai,
                'hasil_perjadin' => $hasilPerjadin,
                'created_at' => $tglMulai->copy()->subDays(3),
                'updated_at' => $tglMulai,
            ]);

            // 2. Tugaskan Pegawai
            DB::table('pegawaiperjadin')->insert([
                'id_perjadin' => $perjadinId,
                'id_user' => $pelaksanaNip,
                'role_perjadin' => 'Anggota Tim',
                'is_lead' => true,
                'laporan_individu' => $laporanIndividu,
            ]);

            // 3. Buat Laporan Keuangan (Draft / Belum Dibuat)
            DB::table('laporankeuangan')->insert([
                'id_perjadin' => $perjadinId,
                'id_status' => $statusLaporan,
                'verified_by' => null,
                'verified_at' => null,
                'nomor_spm' => null,
                'tanggal_spm' => null,
                'nomor_sp2d' => null,
                'tanggal_sp2d' => null,
                'biaya_rampung' => null,
                'created_at' => $tglSelesai->copy()->addDay(),
                'updated_at' => $tglSelesai->copy()->addDay(),
            ]);

            // 4. Geotagging (Hanya untuk yang sedang berlangsung)
            if ($statusPerjadin == $statusPerjadinOnProgress) {
                 DB::table('geotagging')->insert([
                    'id_perjadin' => $perjadinId,
                    'id_user' => $pelaksanaNip,
                    'id_tipe' => $tipeGeotagging,
                    'latitude' => -7.257472, // Surabaya
                    'longitude' => 112.752090, 
                    'created_at' => $tglMulai->copy()->addHours(9), // Baru berangkat
                ]);
            }
        }

    }
}