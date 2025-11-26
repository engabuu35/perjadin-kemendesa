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
        // -----------------------------
        // 1. Ambil ID status perjadin dari kolom NAMA_STATUS
        // -----------------------------
        // statusperjadin: id, nama_status
        // Isi default:
        // 1 Belum Berlangsung
        // 2 Sedang Berlangsung
        // 3 Menunggu Laporan
        // 4 Selesai
        // 5 Diselesaikan Manual
        // 6 Dibatalkan
        $statusMap = DB::table('statusperjadin')->pluck('id', 'nama_status');
        // $statusMap['Belum Berlangsung'], dll.

        // -----------------------------
        // 2. Buat 50 PEGAWAI lewat factory
        // -----------------------------
        $pegawai = User::factory()
            ->pegawai()     // state di UserFactory
            ->count(50)
            ->create();

        $today  = Carbon::today();
        $chunks = $pegawai->chunk(10); // 5 kelompok @10 orang

        // ---------------
        // Kelompok 1: perjadin "Belum Berlangsung" (akan datang)
        // ---------------
        if (isset($chunks[0])) {
            foreach ($chunks[0] as $user) {
                $mulai = $today->copy()->addDays(rand(3, 10));
                $this->createPerjadinForPegawai(
                    user: $user,
                    mulai: $mulai,
                    durasiHari: rand(2, 5),
                    statusId: $statusMap['Belum Berlangsung'] ?? null,
                    laporanLengkap: false,
                    catatan: 'Perjadin akan datang (Belum Berlangsung)'
                );
            }
        }

        // ---------------
        // Kelompok 2: "Sedang Berlangsung"
        // ---------------
        if (isset($chunks[1])) {
            foreach ($chunks[1] as $user) {
                $mulai = $today->copy()->subDays(rand(0, 1)); // kemarin / hari ini
                $this->createPerjadinForPegawai(
                    user: $user,
                    mulai: $mulai,
                    durasiHari: rand(3, 5),
                    statusId: $statusMap['Sedang Berlangsung'] ?? null,
                    laporanLengkap: false,
                    catatan: 'Perjadin sedang berlangsung'
                );
            }
        }

        // ---------------
        // Kelompok 3: "Selesai" + laporan lengkap
        // ---------------
        if (isset($chunks[2])) {
            foreach ($chunks[2] as $user) {
                $mulai = $today->copy()->subDays(rand(7, 20));
                $this->createPerjadinForPegawai(
                    user: $user,
                    mulai: $mulai,
                    durasiHari: rand(2, 5),
                    statusId: $statusMap['Selesai'] ?? null,
                    laporanLengkap: true,
                    catatan: 'Perjadin selesai, laporan lengkap'
                );
            }
        }

        // ---------------
        // Kelompok 4: "Menunggu Laporan" (perjadin berakhir, laporan belum lengkap)
        // ---------------
        if (isset($chunks[3])) {
            foreach ($chunks[3] as $user) {
                $mulai = $today->copy()->subDays(rand(3, 10));
                $this->createPerjadinForPegawai(
                    user: $user,
                    mulai: $mulai,
                    durasiHari: rand(1, 3),
                    statusId: $statusMap['Menunggu Laporan'] ?? null,
                    laporanLengkap: false,
                    catatan: 'Perjadin selesai, tapi laporan belum lengkap'
                );
            }
        }

        // ---------------
        // Kelompok 5: anggap "Diselesaikan Manual" (contoh kondisi perlu tindakan / khusus)
        // ---------------
        if (isset($chunks[4])) {
            foreach ($chunks[4] as $user) {
                $mulai = $today->copy()->subDays(rand(5, 15));
                $this->createPerjadinForPegawai(
                    user: $user,
                    mulai: $mulai,
                    durasiHari: rand(2, 4),
                    statusId: $statusMap['Diselesaikan Manual'] 
                        ?? ($statusMap['Menunggu Laporan'] ?? null),
                    laporanLengkap: false,
                    catatan: 'Perjadin diselesaikan manual / butuh perhatian khusus'
                );
            }
        }
    }

    /**
     * Helper untuk membuat satu perjalanan dinas + record di pegawaiperjadin.
     * Menggunakan DB::table agar tidak bergantung pada model lain.
     */
    protected function createPerjadinForPegawai(
        User $user,
        Carbon $mulai,
        int $durasiHari,
        ?int $statusId,
        bool $laporanLengkap,
        string $catatan
    ): void {
        $akhir = $mulai->copy()->addDays($durasiHari);

        // 1) Insert ke perjalanandinas
        // HAPUS kolom 'hasil_perjadin' dari insert
        $perjadinId = DB::table('perjalanandinas')->insertGetId([
            'id_pembuat'    => $user->nip,
            'id_status'     => $statusId ?? 1, // mis. default "Belum Berlangsung"
            'approved_by'   => null,
            'approved_at'   => null,
            'nomor_surat'   => 'ST-' . Str::upper(Str::random(6)),
            'tanggal_surat' => $mulai->copy()->subDays(3)->toDateString(),
            'tujuan'        => 'Kota ' . Str::title(Str::random(5)),
            'tgl_mulai'     => $mulai->toDateString(),
            'tgl_selesai'   => $akhir->toDateString(),
            // 'hasil_perjadin' => ...   // ← BARIS INI DIHAPUS
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // 2) Laporan individu per pegawai pada tabel pegawaiperjadin
        $laporanIndividu = null;
        if ($laporanLengkap) {
            // minimal 100 karakter (supaya logika "Selesai" bisa terpicu)
            $laporanIndividu = str_repeat(
                'Laporan individu pegawai ini memenuhi syarat minimal 100 karakter. ',
                3
            );
        } else {
            // sengaja pendek / tidak lengkap
            $laporanIndividu = 'Laporan singkat, belum lengkap. ' . $catatan;
        }

        DB::table('pegawaiperjadin')->insert([
            'id_perjadin'      => $perjadinId,
            'id_user'          => $user->nip,
            'role_perjadin'    => 'Anggota',
            'is_lead'          => 0,
            'laporan_individu' => $laporanIndividu,
        ]);
    

    }
}
