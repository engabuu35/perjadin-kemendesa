<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PerjadinDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset & Init Status
        $this->call(StatusPerjadinSeeder::class);
        $this->call(StatusLaporanSeeder::class); 

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('perjalanandinas')->truncate();
        DB::table('pegawaiperjadin')->truncate();
        DB::table('laporan_perjadin')->truncate();
        DB::table('bukti_laporan')->truncate();
        DB::table('geotagging')->truncate();
        DB::table('laporankeuangan')->truncate(); 
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. AMBIL ID STATUS (Safety Check)
        $idPic = $this->getStatusId('Menunggu Verifikasi Laporan');
        $idPpk = $this->getStatusId('Menunggu Verifikasi');
        $idSelesai = $this->getStatusId('Selesai');
        
        // STATUS LAPORAN KEUANGAN
        // PERBAIKAN: Gunakan 'Selesai' saja (ID 5), jangan 'Selesai Dibayar' (ID 6) 
        // agar tidak error foreign key jika ID 6 belum terbuat.
        $idLapPerluTindakan = $this->getStatusLaporanId('Perlu Tindakan');
        $idLapMenunggu = $this->getStatusLaporanId('Menunggu Verifikasi');
        $idLapSelesai = $this->getStatusLaporanId('Selesai'); 

        $nipPic = '199103032021031003'; 
        $nipPimpinan = '198001012010011001';
        $nipPpk = '199002022020022002';

        $availableUsers = DB::table('users')
            ->whereNotIn('nip', [$nipPic, $nipPimpinan, $nipPpk])
            ->pluck('nip')
            ->toArray();

        if (empty($availableUsers)) {
            $this->command->error('Tabel users kosong. Jalankan UsersSeeder dulu.');
            return;
        }

        // ==========================================
        // SKENARIO 1: DATA UNTUK PIC (3 Data)
        // Status: Menunggu Verifikasi Laporan
        // ==========================================
        for ($i = 1; $i <= 3; $i++) {
            $this->createPerjadin($i, $nipPic, $nipPimpinan, $idPic, $availableUsers, 'ST/PIC/'.rand(100,999));
        }

        // ==========================================
        // SKENARIO 2: DATA UNTUK PPK (3 Data)
        // Status: Menunggu Verifikasi
        // ==========================================
        for ($i = 4; $i <= 6; $i++) {
            $idPerjadin = $this->createPerjadin($i, $nipPic, $nipPimpinan, $idPpk, $availableUsers, 'ST/PPK/'.rand(100,999));
            
            DB::table('laporankeuangan')->insert([
                'id_perjadin' => $idPerjadin,
                'id_status' => $idLapMenunggu,
                'biaya_rampung' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // ==========================================
        // SKENARIO 3: DATA SELESAI / RIWAYAT (4 Data)
        // Status: Selesai
        // ==========================================
        for ($i = 7; $i <= 10; $i++) {
            $idPerjadin = $this->createPerjadin($i, $nipPic, $nipPimpinan, $idSelesai, $availableUsers, 'ST/FIN/'.rand(100,999));
            
            DB::table('laporankeuangan')->insert([
                'id_perjadin' => $idPerjadin,
                'id_status' => $idLapSelesai, // Status 'Selesai' (ID 5)
                'verified_by' => $nipPpk,
                'verified_at' => now()->subDays(1),
                'nomor_spm' => 'SPM/'.rand(1000,9999),
                'tanggal_spm' => now()->subDays(2),
                'nomor_sp2d' => 'SP2D/'.rand(1000,9999),
                'tanggal_sp2d' => now()->subDays(1),
                'biaya_rampung' => 5000000,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(1)
            ]);
        }
    }

    // --- FUNGSI BANTUAN ---

    private function getStatusId($name) {
        $id = DB::table('statusperjadin')->where('nama_status', $name)->value('id');
        if (!$id) {
            $id = DB::table('statusperjadin')->insertGetId(['nama_status' => $name]);
        }
        return $id;
    }

    private function getStatusLaporanId($name) {
        $id = DB::table('statuslaporan')->where('nama_status', $name)->value('id');
        if (!$id) {
            $id = DB::table('statuslaporan')->insertGetId(['nama_status' => $name]);
        }
        return $id;
    }

    private function createPerjadin($i, $pembuat, $atasan, $statusId, $users, $noSurat) {
        $id = DB::table('perjalanandinas')->insertGetId([
            'id_pembuat' => $pembuat,
            'id_status' => $statusId, 
            'id_atasan' => $atasan,
            'approved_by' => $atasan,
            'approved_at' => now()->subDays(10),
            'nomor_surat' => $noSurat,
            'tanggal_surat' => now()->subDays(12),
            'tujuan' => 'Dinas ke ' . $this->getNamaKota($i),
            'tgl_mulai' => now()->subDays(rand(5,8)),
            'tgl_selesai' => now()->subDays(rand(1,3)),
            'uraian' => 'Kegiatan dummy untuk simulasi aplikasi.',
            'created_at' => now()->subDays(12),
        ]);

        shuffle($users);
        $tim = array_slice($users, 0, 2);

        foreach ($tim as $index => $nipPegawai) {
            DB::table('pegawaiperjadin')->insert([
                'id_perjadin' => $id,
                'id_user' => $nipPegawai,
                'role_perjadin' => ($index == 0) ? 'Ketua' : 'Anggota',
                'is_lead' => ($index == 0) ? 1 : 0,
                'is_finished' => 1 
            ]);

            $laporanId = DB::table('laporan_perjadin')->insertGetId([
                'id_perjadin' => $id,
                'id_user' => $nipPegawai,
                'uraian' => 'Laporan kegiatan individu.',
                'is_final' => 1,
                'created_at' => now()
            ]);

            $this->insertBiaya($laporanId, 'Tiket', 1500000);
            $this->insertInfoTeks($laporanId, 'Maskapai', 'Garuda Indonesia');
            $this->insertInfoTeks($laporanId, 'Kode Tiket', 'GA-' . rand(100, 999));
            $this->insertBiaya($laporanId, 'Penginapan', 2000000);
            $this->insertInfoTeks($laporanId, 'Nama Penginapan', 'Hotel Bintang 4');
            $this->insertInfoTeks($laporanId, 'Kota', $this->getNamaKota($i));
            $this->insertBiaya($laporanId, 'Uang Harian', 450000 * 3);
        }

        return $id;
    }

    private function insertBiaya($idLaporan, $kategori, $nominal) {
        DB::table('bukti_laporan')->insert([
            'id_laporan' => $idLaporan, 'kategori' => $kategori, 'nominal' => $nominal,
            'keterangan' => null, 'created_at' => now(), 'updated_at' => now()
        ]);
    }

    private function insertInfoTeks($idLaporan, $kategori, $isiTeks) {
        DB::table('bukti_laporan')->insert([
            'id_laporan' => $idLaporan, 'kategori' => $kategori, 'nominal' => 0,
            'keterangan' => $isiTeks, 'created_at' => now(), 'updated_at' => now()
        ]);
    }

    private function getNamaKota($i) {
        $kota = ['Bandung', 'Surabaya', 'Yogyakarta', 'Medan', 'Makassar', 'Bali', 'Semarang', 'Malang', 'Bogor', 'Solo'];
        return $kota[($i - 1) % count($kota)];
    }
}