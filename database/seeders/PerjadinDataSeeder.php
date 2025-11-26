<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerjadinDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset & Init Status
        $this->call(StatusPerjadinSeeder::class);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('perjalanandinas')->truncate();
        DB::table('pegawaiperjadin')->truncate();
        DB::table('laporan_perjadin')->truncate();
        DB::table('bukti_laporan')->truncate();
        DB::table('geotagging')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $stMenungguVerif = DB::table('statusperjadin')->where('nama_status', 'Menunggu Verifikasi Laporan')->value('id');
        
        $nipPic = '199103032021031003'; 
        $nipPimpinan = '198001012010011001';

        $availableUsers = DB::table('users')
            ->whereNotIn('nip', [$nipPic, $nipPimpinan])
            ->pluck('nip')
            ->toArray();

        // Buat 10 Data Dummy
        for ($i = 1; $i <= 10; $i++) {
            
            $perjadinId = DB::table('perjalanandinas')->insertGetId([
                'id_pembuat' => $nipPic,
                'id_status' => $stMenungguVerif, 
                'id_atasan' => $nipPimpinan,
                'approved_by' => $nipPimpinan,
                'approved_at' => now()->subDays(10),
                'nomor_surat' => 'ST/LENGKAP/' . str_pad($i, 3, '0', STR_PAD_LEFT) . '/2025',
                'tanggal_surat' => now()->subDays(12),
                'tujuan' => 'Audit Lapangan Desa ' . $this->getNamaKota($i),
                'tgl_mulai' => now()->subDays(8),
                'tgl_selesai' => now()->subDays(5),
                'uraian' => 'Kegiatan audit telah dilaksanakan sesuai prosedur.',
                'created_at' => now()->subDays(12),
            ]);

            shuffle($availableUsers);
            $tim = array_slice($availableUsers, 0, 2);

            foreach ($tim as $index => $nipPegawai) {
                DB::table('pegawaiperjadin')->insert([
                    'id_perjadin' => $perjadinId,
                    'id_user' => $nipPegawai,
                    'role_perjadin' => ($index == 0) ? 'Ketua' : 'Anggota',
                    'is_lead' => ($index == 0) ? 1 : 0,
                    'is_finished' => 1
                ]);

                $laporanId = DB::table('laporan_perjadin')->insertGetId([
                    'id_perjadin' => $perjadinId,
                    'id_user' => $nipPegawai,
                    'uraian' => 'Laporan kegiatan individu pegawai.',
                    'is_final' => 1,
                    'created_at' => now()
                ]);

                // --- INSERT DATA SESUAI STRUKTUR BARU (KOLOM KETERANGAN) ---
                
                // 1. TIKET (Nominal)
                $this->insertBiaya($laporanId, 'Tiket', 1500000);
                // 1b. TIKET (Info: Maskapai & Kode) -> Masuk ke kolom 'keterangan'
                $this->insertInfoTeks($laporanId, 'Maskapai', 'Garuda Indonesia');
                $this->insertInfoTeks($laporanId, 'Kode Tiket', 'GA-' . rand(100, 999));

                // 2. PENGINAPAN (Nominal)
                $this->insertBiaya($laporanId, 'Penginapan', 2000000);
                // 2b. PENGINAPAN (Info: Hotel & Kota) -> Masuk ke kolom 'keterangan'
                $this->insertInfoTeks($laporanId, 'Nama Penginapan', 'Hotel Bintang ' . rand(3,5));
                $this->insertInfoTeks($laporanId, 'Kota', $this->getNamaKota($i));

                // 3. UANG HARIAN (Hanya Nominal)
                $this->insertBiaya($laporanId, 'Uang Harian', 450000 * 3);
            }
        }
    }

    // Helper: Simpan Biaya (Nominal)
    private function insertBiaya($idLaporan, $kategori, $nominal) {
        DB::table('bukti_laporan')->insert([
            'id_laporan' => $idLaporan, 
            'kategori' => $kategori, 
            'nominal' => $nominal,
            'keterangan' => null, // Keterangan kosong untuk data biaya
            'nama_file' => null, 'path_file' => null, 
            'created_at' => now(), 'updated_at' => now()
        ]);
    }

    // Helper: Simpan Info Teks (Satu fungsi untuk semua)
    private function insertInfoTeks($idLaporan, $kategori, $isiTeks) {
        DB::table('bukti_laporan')->insert([
            'id_laporan' => $idLaporan, 
            'kategori' => $kategori, // Misal: "Maskapai", "Kota"
            'nominal' => 0,
            'keterangan' => $isiTeks, // Misal: "Garuda", "Bandung"
            'nama_file' => null, 'path_file' => null, 
            'created_at' => now(), 'updated_at' => now()
        ]);
    }

    private function getNamaKota($i) {
        $kota = ['Bandung', 'Surabaya', 'Yogyakarta', 'Medan', 'Makassar', 'Bali', 'Semarang', 'Malang', 'Bogor', 'Solo'];
        return $kota[($i - 1) % count($kota)];
    }
}