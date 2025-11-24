<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerjadinDataSeeder extends Seeder
{
    public function run(): void
    {
        // Kita panggil seeder status dulu biar ID-nya fresh dan urut
        $this->call(StatusPerjadinSeeder::class);

        // Reset Data Transaksi Saja
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('perjalanandinas')->truncate();
        DB::table('pegawaiperjadin')->truncate();
        DB::table('laporan_perjadin')->truncate();
        DB::table('bukti_laporan')->truncate();
        DB::table('geotagging')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil ID Status dari Database (hasil seeder status di atas)
        $idProses = DB::table('statusperjadin')->where('nama_status', 'Sedang Berlangsung')->value('id');
        $idVerif  = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi Laporan')->value('id');
        
        $tipeHarian = DB::table('tipegeotagging')->where('nama_tipe', 'Laporan Harian')->value('id');
        
        // User NIP
        $nipKetua = '199103032021031003'; 
        $nipAnggota = '199204042022042004';
        $nipPimpinan = '198001012010011001';

        // ==============================================================
        // DATA 1: SEDANG BERLANGSUNG (Untuk Pegawai Test Input)
        // ==============================================================
        $id1 = DB::table('perjalanandinas')->insertGetId([
            'id_pembuat' => $nipKetua,
            'id_status' => $idProses,
            'id_atasan' => $nipPimpinan,
            'approved_by' => $nipPimpinan,
            'approved_at' => now()->subDays(1),
            'nomor_surat' => 'ST/PROSES/001/2025',
            'tanggal_surat' => now()->subDays(1),
            'tujuan' => 'Kunjungan Lapangan Bandung',
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addDays(2),
            'uraian' => 'Progres 50%',
            'created_at' => now(),
        ]);
        $this->insertPeserta($id1, $nipKetua, $nipAnggota);

        // ==============================================================
        // DATA 2: MENUNGGU VERIFIKASI (Untuk PIC Test Pelaporan)
        // ==============================================================
        $id2 = DB::table('perjalanandinas')->insertGetId([
            'id_pembuat' => $nipKetua,
            'id_status' => $idVerif,
            'id_atasan' => $nipPimpinan,
            'approved_by' => $nipPimpinan,
            'approved_at' => now()->subDays(6),
            'nomor_surat' => 'ST/VERIF/002/2025',
            'tanggal_surat' => now()->subDays(5),
            'tujuan' => 'Rapat Koordinasi Surabaya',
            'tgl_mulai' => now()->subDays(4),
            'tgl_selesai' => now()->subDays(2),
            'uraian' => 'Kegiatan berjalan lancar.',
            'created_at' => now()->subDays(5),
        ]);
        $this->insertPeserta($id2, $nipKetua, $nipAnggota);

        // Data Dummy Laporan Keuangan (Agar tabel PIC tidak kosong)
        $laporanKetua = DB::table('laporan_perjadin')->insertGetId([
            'id_perjadin' => $id2, 'id_user' => $nipKetua, 'uraian' => 'Lengkap', 'is_final' => 1, 'created_at' => now()
        ]);
        $this->insertBukti($laporanKetua, 'Tiket', 1500000);
        $this->insertBukti($laporanKetua, 'Penginapan', 2000000);

        $laporanAnggota = DB::table('laporan_perjadin')->insertGetId([
            'id_perjadin' => $id2, 'id_user' => $nipAnggota, 'uraian' => 'Lengkap', 'is_final' => 1, 'created_at' => now()
        ]);
        $this->insertBukti($laporanAnggota, 'Tiket', 1500000);
        $this->insertBukti($laporanAnggota, 'Uang Harian', 500000);
    }

    private function insertPeserta($idPerjadin, $ketua, $anggota) {
        DB::table('pegawaiperjadin')->insert([
            ['id_perjadin' => $idPerjadin, 'id_user' => $ketua, 'role_perjadin' => 'Ketua', 'is_lead' => 1],
            ['id_perjadin' => $idPerjadin, 'id_user' => $anggota, 'role_perjadin' => 'Anggota', 'is_lead' => 0],
        ]);
    }

    private function insertBukti($idLaporan, $kategori, $nominal) {
        DB::table('bukti_laporan')->insert([
            'id_laporan' => $idLaporan, 'kategori' => $kategori, 'nominal' => $nominal, 'nama_file' => null, 'path_file' => null, 'created_at' => now(), 'updated_at' => now()
        ]);
    }
}