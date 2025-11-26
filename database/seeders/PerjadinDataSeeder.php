<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerjadinDataSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan status perjadin tersedia
        $this->call(StatusPerjadinSeeder::class);

        // Reset data transaksi/referensi (sesuaikan table list dengan DB Anda)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('perjalanandinas')->truncate();
        DB::table('pegawaiperjadin')->truncate();
        DB::table('laporan_perjadin')->truncate();
        DB::table('bukti_laporan')->truncate();
        DB::table('geotagging')->truncate();
        DB::table('laporankeuangan')->truncate(); // juga bersihkan agregat laporan keuangan
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil ID status perjadin
        $idDraft       = DB::table('statusperjadin')->where('nama_status', 'Draft / Menunggu Persetujuan')->value('id');
        $idBelum       = DB::table('statusperjadin')->where('nama_status', 'Belum Berlangsung')->value('id');
        $idSedang      = DB::table('statusperjadin')->where('nama_status', 'Sedang Berlangsung')->value('id');
        $idMenungguLap = DB::table('statusperjadin')->where('nama_status', 'Menunggu Laporan')->value('id');
        $idMenungguVer = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi Laporan')->value('id');
        $idSelesai     = DB::table('statusperjadin')->where('nama_status', 'Selesai')->value('id');
        $idDitolak     = DB::table('statusperjadin')->where('nama_status', 'Ditolak')->value('id');
        $idManual      = DB::table('statusperjadin')->where('nama_status', 'Diselesaikan Manual')->value('id');

        // NIP contoh user (sesuaikan dengan user exist/seeder user Anda)
        $nipKetua    = '199103032021031003';
        $nipAnggota  = '199204042022042004';
        $nipPimpinan = '198001012010011001';

        // ============================
        // 1) DRAFT / Menunggu Persetujuan
        // ============================
        $id1 = DB::table('perjalanandinas')->insertGetId([
            'id_pembuat' => $nipKetua,
            'id_status' => $idDraft,
            'id_atasan' => $nipPimpinan,
            'approved_by' => null,
            'approved_at' => null,
            'nomor_surat' => 'ST/DRAFT/001/2025',
            'tanggal_surat' => now()->subDays(10),
            'tujuan' => 'Survei Awal Kabupaten X',
            'tgl_mulai' => now()->addDays(7), // mulai di masa depan
            'tgl_selesai' => now()->addDays(9),
            'uraian' => null,
            'created_at' => now()->subDays(10),
        ]);
        $this->insertPeserta($id1, $nipKetua, $nipAnggota);

        // ============================
        // 2) Belum Berlangsung (approved, tapi tgl_mulai > hari ini)
        // ============================
        $id2 = DB::table('perjalanandinas')->insertGetId([
            'id_pembuat' => $nipKetua,
            'id_status' => $idBelum,
            'id_atasan' => $nipPimpinan,
            'approved_by' => $nipPimpinan,
            'approved_at' => now()->subDays(5),
            'nomor_surat' => 'ST/NOTSTART/002/2025',
            'tanggal_surat' => now()->subDays(6),
            'tujuan' => 'Rapat Koordinasi Provinsi Y',
            'tgl_mulai' => now()->addDays(3),
            'tgl_selesai' => now()->addDays(4),
            'uraian' => null,
            'created_at' => now()->subDays(6),
        ]);
        $this->insertPeserta($id2, $nipKetua, $nipAnggota);

        // ============================
        // 3) Sedang Berlangsung (tgl_mulai <= hari ini <= tgl_selesai)
        // ============================
        $id3 = DB::table('perjalanandinas')->insertGetId([
            'id_pembuat' => $nipKetua,
            'id_status' => $idSedang,
            'id_atasan' => $nipPimpinan,
            'approved_by' => $nipPimpinan,
            'approved_at' => now()->subDays(2),
            'nomor_surat' => 'ST/ONGOING/003/2025',
            'tanggal_surat' => now()->subDays(3),
            'tujuan' => 'Kunjungan Lapangan Bandung',
            'tgl_mulai' => now()->subDays(1),
            'tgl_selesai' => now()->addDays(1),
            'uraian' => 'Tim sedang mengumpulkan data lapangan terkait pengelolaan dana desa wisata dan keluhan masyarakat.',
            'created_at' => now()->subDays(3),
        ]);
        $this->insertPeserta($id3, $nipKetua, $nipAnggota);

        // ============================
        // 4) Menunggu Verifikasi Laporan (sudah selesai & PIC submit laporan individu)
        // ============================
        $id4 = DB::table('perjalanandinas')->insertGetId([
            'id_pembuat' => $nipKetua,
            'id_status' => $idMenungguVer,
            'id_atasan' => $nipPimpinan,
            'approved_by' => $nipPimpinan,
            'approved_at' => now()->subDays(10),
            'nomor_surat' => 'ST/VERIFY/004/2025',
            'tanggal_surat' => now()->subDays(15),
            'tujuan' => 'Rapat Nasional Surabaya',
            'tgl_mulai' => now()->subDays(8),
            'tgl_selesai' => now()->subDays(6),
            'uraian' => 'Seluruh peserta telah mengumpulkan laporan—termasuk temuan bahwa sekitar 50% dana promosi desa wisata diduga diselewengkan oleh kepala desa.',
            'created_at' => now()->subDays(15),
        ]);
        $this->insertPeserta($id4, $nipKetua, $nipAnggota);

        // buat laporan_perjadin dan bukti untuk Ketua & Anggota (agar PIC bisa lihat)
        $lapKetua = DB::table('laporan_perjadin')->insertGetId([
            'id_perjadin' => $id4,
            'id_user' => $nipKetua,
            'uraian' => 'Laporan lengkap - ketua',
            'is_final' => 1,
            'created_at' => now()
        ]);
        $this->insertBukti($lapKetua, 'Tiket Pesawat', 1500000);
        $this->insertBukti($lapKetua, 'Penginapan', 2000000);

        $lapAnggota = DB::table('laporan_perjadin')->insertGetId([
            'id_perjadin' => $id4,
            'id_user' => $nipAnggota,
            'uraian' => 'Laporan lengkap - anggota',
            'is_final' => 1,
            'created_at' => now()
        ]);
        $this->insertBukti($lapAnggota, 'Tiket Kereta', 500000);
        $this->insertBukti($lapAnggota, 'Uang Harian', 400000);

        // ============================
        // 5) Selesai (diverifikasi/selesai oleh PIC/PPK)
        // ============================
        $id5 = DB::table('perjalanandinas')->insertGetId([
            'id_pembuat' => $nipKetua,
            'id_status' => $idSelesai,
            'id_atasan' => $nipPimpinan,
            'approved_by' => $nipPimpinan,
            'approved_at' => now()->subDays(20),
            'nomor_surat' => 'ST/FIN/005/2025',
            'tanggal_surat' => now()->subDays(25),
            'tujuan' => 'Workshop Jakarta',
            'tgl_mulai' => now()->subDays(22),
            'tgl_selesai' => now()->subDays(20),
            'uraian' => 'Verifikasi final: Status kegiatan selesai. Laporan menguatkan indikasi penyelewengan dana desa wisata untuk kepentingan pribadi kepala desa.',
            'created_at' => now()->subDays(25),
        ]);
        $this->insertPeserta($id5, $nipKetua, $nipAnggota);

        // buat laporan_perjadin + bukti untuk id5 (sebagai histori)
        $lapKetua2 = DB::table('laporan_perjadin')->insertGetId([
            'id_perjadin' => $id5,
            'id_user' => $nipKetua,
            'uraian' => 'Laporan final - ketua',
            'is_final' => 1,
            'created_at' => now()->subDays(20)
        ]);
        $this->insertBukti($lapKetua2, 'Transport', 800000);
        $this->insertBukti($lapKetua2, 'Penginapan', 1200000);

        // (opsional) Anda bisa tambahkan entri laporankeuangan agregat di LaporanKeuanganSeeder
    }

    private function insertPeserta($idPerjadin, $ketua, $anggota) {
        DB::table('pegawaiperjadin')->insert([
            ['id_perjadin' => $idPerjadin, 'id_user' => $ketua, 'role_perjadin' => 'Ketua', 'is_lead' => 1],
            ['id_perjadin' => $idPerjadin, 'id_user' => $anggota, 'role_perjadin' => 'Anggota', 'is_lead' => 0],
        ]);
    }

    private function insertBukti($idLaporan, $kategori, $nominal) {
        DB::table('bukti_laporan')->insert([
            'id_laporan' => $idLaporan,
            'kategori' => $kategori,
            'nominal' => $nominal,
            'nama_file' => null,
            'path_file' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
