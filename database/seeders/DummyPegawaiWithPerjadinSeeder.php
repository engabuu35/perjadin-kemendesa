<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class DummyPegawaiWithPerjadinSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil ID Status Penting
        $statusMap = DB::table('statusperjadin')->pluck('id', 'nama_status');
        
        $idBelum       = $statusMap['Belum Berlangsung'] ?? 1;
        $idSedang      = $statusMap['Sedang Berlangsung'] ?? 2;
        $idTungguVerif = $statusMap['Menunggu Verifikasi Laporan'] ?? 3; 
        $idTungguValid = $statusMap['Menunggu Validasi PPK'] ?? 4;       
        $idSelesai     = $statusMap['Selesai'] ?? 5;                     

        // 2. Buat 50 Pegawai Dummy
        $pegawai = User::factory()->pegawai()->count(50)->create();
        $chunks = $pegawai->chunk(10); 

        $today = Carbon::today();

        // --- KELOMPOK 1 & 2: BELUM & SEDANG (Tanpa Keuangan) ---
        if (isset($chunks[0])) {
            foreach ($chunks[0] as $user) $this->createPerjadinForPegawai($user, $today->copy()->addDays(5), 3, $idBelum, false);
        }
        if (isset($chunks[1])) {
            foreach ($chunks[1] as $user) $this->createPerjadinForPegawai($user, $today->copy()->subDays(1), 3, $idSedang, false);
        }

        // --- KELOMPOK 3: MEJA PIC (Menunggu Verifikasi - Keuangan Kosong/Partial) ---
        if (isset($chunks[2])) {
            foreach ($chunks[2] as $user) {
                // Kita buat partial (seolah PIC baru isi sebagian)
                $this->createPerjadinForPegawai($user, $today->copy()->subDays(5), 3, $idTungguVerif, true, true, 'partial'); 
            }
        }

        // --- KELOMPOK 4: MEJA PPK (Menunggu Validasi - Keuangan LENGKAP) ---
        if (isset($chunks[3])) {
            foreach ($chunks[3] as $user) {
                $this->createPerjadinForPegawai($user, $today->copy()->subDays(10), 4, $idTungguValid, true, true, 'full');
            }
        }

        // --- KELOMPOK 5: SELESAI (Arsip - Keuangan LENGKAP) ---
        if (isset($chunks[4])) {
            foreach ($chunks[4] as $user) {
                $this->createPerjadinForPegawai($user, $today->copy()->subDays(20), 5, $idSelesai, true, true, 'full');
            }
        }
    }

    protected function createPerjadinForPegawai(
        User $user,
        Carbon $mulai,
        int $durasi,
        int $statusId,
        bool $isPegawaiSelesai = false,
        bool $withFinancialData = false,
        string $financialMode = 'full' // 'partial' atau 'full'
    ): void {
        $akhir = $mulai->copy()->addDays($durasi);

        // 1. Insert Surat Tugas
        $perjadinId = DB::table('perjalanandinas')->insertGetId([
            'id_pembuat'    => $user->nip,
            'id_status'     => $statusId,
            'approved_by'   => '198001012010011001',
            'approved_at'   => $mulai->copy()->subDays(5),
            'nomor_surat'   => 'ST-DUMMY-' . Str::upper(Str::random(5)),
            'tanggal_surat' => $mulai->copy()->subDays(7),
            'tujuan'        => 'Kota ' . Str::title(Str::random(6)),
            'tgl_mulai'     => $mulai,
            'tgl_selesai'   => $akhir,
            'uraian'        => $isPegawaiSelesai ? 'Kegiatan telah selesai dilaksanakan.' : null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // 2. Insert Pegawai
        DB::table('pegawaiperjadin')->insert([
            'id_perjadin'   => $perjadinId,
            'id_user'       => $user->nip,
            'role_perjadin' => 'Anggota',
            'is_lead'       => 0,
            'is_finished'   => $isPegawaiSelesai ? 1 : 0,
        ]);

        // 3. Insert Keuangan (JIKA DIMINTA)
        if ($withFinancialData) {
            
            $laporanId = DB::table('laporan_perjadin')->insertGetId([
                'id_perjadin' => $perjadinId,
                'id_user'     => $user->nip,
                'uraian'      => 'Laporan keuangan dummy.',
                'is_final'    => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // --- DATA WAJIB (SELALU ADA) ---
            
            // A. Tiket
            DB::table('bukti_laporan')->insert([
                'id_laporan' => $laporanId, 'kategori' => 'Tiket', 'nominal' => rand(1000000, 3000000), 'keterangan' => null, 'created_at' => now()
            ]);
            DB::table('bukti_laporan')->insert([
                'id_laporan' => $laporanId, 'kategori' => 'Maskapai', 'nominal' => 0, 'keterangan' => 'Garuda Indonesia', 'created_at' => now()
            ]);
            DB::table('bukti_laporan')->insert([
                'id_laporan' => $laporanId, 'kategori' => 'Kode Tiket', 'nominal' => 0, 'keterangan' => 'GA-' . rand(100, 999), 'created_at' => now()
            ]);

            // B. Uang Harian
            DB::table('bukti_laporan')->insert([
                'id_laporan' => $laporanId, 'kategori' => 'Uang Harian', 'nominal' => 450000 * $durasi, 'keterangan' => null, 'created_at' => now()
            ]);

            // --- DATA TAMBAHAN (HANYA JIKA MODE FULL) ---
            if ($financialMode === 'full') {
                // C. Penginapan
                DB::table('bukti_laporan')->insert([
                    'id_laporan' => $laporanId, 'kategori' => 'Penginapan', 'nominal' => rand(1500000, 4000000), 'keterangan' => null, 'created_at' => now()
                ]);
                DB::table('bukti_laporan')->insert([
                    'id_laporan' => $laporanId, 'kategori' => 'Nama Penginapan', 'nominal' => 0, 'keterangan' => 'Hotel Bintang ' . rand(3,5), 'created_at' => now()
                ]);
                DB::table('bukti_laporan')->insert([
                    'id_laporan' => $laporanId, 'kategori' => 'Kota', 'nominal' => 0, 'keterangan' => 'Jakarta', 'created_at' => now()
                ]);

                // D. Transport Lokal (Random muncul)
                if (rand(0, 1)) {
                    DB::table('bukti_laporan')->insert([
                        'id_laporan' => $laporanId, 'kategori' => 'Transport', 'nominal' => rand(100000, 500000), 'keterangan' => 'Taksi Bandara', 'created_at' => now()
                    ]);
                }

                // E. Uang Representasi (Random muncul untuk pejabat)
                if (rand(0, 1)) {
                    DB::table('bukti_laporan')->insert([
                        'id_laporan' => $laporanId, 'kategori' => 'Uang Representasi', 'nominal' => 150000 * $durasi, 'keterangan' => null, 'created_at' => now()
                    ]);
                }
            }
        }
    }
}