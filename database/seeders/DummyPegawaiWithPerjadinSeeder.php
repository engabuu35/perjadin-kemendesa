<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class DummyPegawaiWithPerjadinSeeder extends Seeder
{
    /**
     * ID status laporan keuangan (draft & selesai) untuk tabel laporankeuangan
     */
    protected ?int $statusLaporanDraftId   = null;
    protected ?int $statusLaporanSelesaiId = null;

    /**
     * NIP salah satu user PPK (untuk verified_by di laporan keuangan)
     */
    protected ?string $ppkNip = null;

    public function run(): void
    {
        // ================================
        // 1. Mapping status perjadin
        // ================================
        // statusperjadin: id, nama_status
        $statusMap = DB::table('statusperjadin')->pluck('id', 'nama_status');
        // contoh: $statusMap['Belum Berlangsung'], ['Sedang Berlangsung'], dst.

        // ================================
        // 2. Mapping status laporan keuangan
        // ================================
        $statusLaporan = DB::table('statuslaporan')->pluck('id', 'nama_status');

        // Cari status draft / belum selesai (fallback ke baris pertama kalau nama tidak ada)
        $this->statusLaporanDraftId =
            $statusLaporan['Draft']
            ?? $statusLaporan['Belum Diverifikasi']
            ?? $statusLaporan['Menunggu Verifikasi']
            ?? ($statusLaporan->first() ?: null);

        // Cari status selesai dibayar (fallback ke baris terakhir kalau nama tidak ada)
        $this->statusLaporanSelesaiId =
            $statusLaporan['Selesai Dibayar']
            ?? $statusLaporan['Selesai']
            ?? $statusLaporan['Rampung']
            ?? ($statusLaporan->last() ?: null);

        // ================================
        // 3. Ambil salah satu NIP PPK (untuk verified_by)
        // ================================
        $this->ppkNip = DB::table('penugasanperan')
            ->join('roles', 'penugasanperan.role_id', '=', 'roles.id')
            ->where('roles.kode', 'PPK')
            ->value('user_id'); // isi user_id di sini = users.nip

        // ================================
        // 4. Generate 50 pegawai (role PEGAWAI) via factory
        // ================================
        $pegawai = User::factory()
            ->pegawai() // state di UserFactory yang menambahkan role 'PEGAWAI'
            ->count(50)
            ->create();

        $tahun        = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // ================================
        // 5. Generate perjalanan dinas & laporan keuangan per bulan
        // ================================
        for ($bulan = 1; $bulan <= 12; $bulan++) {

            // 4 pegawai random per bulan (boleh sama di bulan lain)
            $sample = $pegawai->random(min(4, $pegawai->count()));

            foreach ($sample as $index => $user) {

                // tanggal mulai default (nanti bisa dioverride untuk bulan sekarang)
                $mulai = Carbon::create($tahun, $bulan, rand(1, 20));

                $statusId             = null;
                $laporanLengkap       = false;
                $catatan              = 'Perjadin dummy';
                $buatLapKeuangan      = false;
                $laporanKeuSelesai    = false;

                if ($bulan > $currentMonth) {
                    // ===============================
                    // BULAN DI DEPAN (MASA DEPAN)
                    // ===============================
                    $statusId       = $statusMap['Belum Berlangsung'] ?? null;
                    $laporanLengkap = false;
                    $catatan        = 'Perjadin akan datang (Belum Berlangsung)';
                    $buatLapKeuangan   = false; // belum ada realisasi
                    $laporanKeuSelesai = false;

                } elseif ($bulan < $currentMonth) {
                    // ===============================
                    // BULAN YANG SUDAH LEWAT
                    // ===============================
                    $rand = rand(1, 3);

                    if ($rand === 1) {
                        // Selesai, laporan lengkap + keuangan selesai
                        $statusId          = $statusMap['Selesai'] ?? null;
                        $laporanLengkap    = true;
                        $catatan           = 'Perjadin selesai di bulan lalu, laporan lengkap';
                        $buatLapKeuangan   = true;
                        $laporanKeuSelesai = true;
                    } elseif ($rand === 2) {
                        // Selesai, tapi menunggu laporan pegawai
                        $statusId          = $statusMap['Menunggu Laporan'] ?? null;
                        $laporanLengkap    = false;
                        $catatan           = 'Perjadin selesai, laporan pegawai belum lengkap';
                        $buatLapKeuangan   = true;  // bisa jadi draft keuangan sudah dibuat
                        $laporanKeuSelesai = false; // belum rampung
                    } else {
                        // Diselesaikan manual / kondisi khusus
                        $statusId          = $statusMap['Diselesaikan Manual']
                            ?? ($statusMap['Menunggu Laporan'] ?? null);
                        $laporanLengkap    = false;
                        $catatan           = 'Perjadin diselesaikan manual / khusus';
                        $buatLapKeuangan   = true;
                        $laporanKeuSelesai = true; // anggap sudah dibayar
                    }

                } else {
                    // ===============================
                    // BULAN SEKARANG
                    // ===============================
                    if ($index < 2) {
                        // 2 perjalanan: Sedang Berlangsung
                        $statusId          = $statusMap['Sedang Berlangsung'] ?? null;
                        $laporanLengkap    = false;
                        $mulai             = Carbon::now()->copy()->subDays(rand(0, 2));
                        $catatan           = 'Perjadin sedang berlangsung di bulan ini';
                        $buatLapKeuangan   = false;
                        $laporanKeuSelesai = false;
                    } elseif ($index == 2) {
                        // 1 perjalanan: Selesai + Laporan & Keuangan rampung (DIPASTIKAN ADA)
                        $statusId          = $statusMap['Selesai'] ?? null;
                        $laporanLengkap    = true;
                        $mulai             = Carbon::now()->copy()->subDays(rand(3, 20)); // masih di bulan ini
                        $catatan           = 'Perjadin selesai di bulan ini, laporan lengkap & sudah dibayar';
                        $buatLapKeuangan   = true;
                        $laporanKeuSelesai = true;   // ini yang bikin biaya_rampung terisi
                    } else {
                        // sisanya boleh random antara selesai & menunggu laporan
                        $rand = rand(1, 2);
                        if ($rand === 1) {
                            $statusId          = $statusMap['Selesai'] ?? null;
                            $laporanLengkap    = true;
                            $catatan           = 'Perjadin selesai di bulan ini, laporan lengkap';
                            $buatLapKeuangan   = true;
                            $laporanKeuSelesai = true;
                        } else {
                            $statusId          = $statusMap['Menunggu Laporan'] ?? null;
                            $laporanLengkap    = false;
                            $catatan           = 'Perjadin selesai di bulan ini, laporan belum lengkap';
                            $buatLapKeuangan   = true;
                            $laporanKeuSelesai = false;
                        }
                    }
                }


                // Buat perjadin + pegawaiperjadin + (opsional) laporan keuangan
                $this->createPerjadinForPegawai(
                    user: $user,
                    mulai: $mulai,
                    durasiHari: rand(2, 5),
                    statusId: $statusId,
                    laporanLengkap: $laporanLengkap,
                    catatan: $catatan,
                    buatLaporanKeuangan: $buatLapKeuangan,
                    laporanKeuanganSelesai: $laporanKeuSelesai
                );
            }
        }
    }

    /**
     * Helper: buat 1 perjalanan dinas + relasi pegawai + optional laporan keuangan
     */
    protected function createPerjadinForPegawai(
        User $user,
        Carbon $mulai,
        int $durasiHari,
        ?int $statusId,
        bool $laporanLengkap,
        string $catatan,
        bool $buatLaporanKeuangan,
        bool $laporanKeuanganSelesai
    ): void {
        $akhir = $mulai->copy()->addDays($durasiHari);

        // 1) Insert ke tabel perjalanandinas
        $perjadinId = DB::table('perjalanandinas')->insertGetId([
            'id_pembuat'    => $user->nip,
            'id_status'     => $statusId ?? 1, // fallback: Belum Berlangsung
            'approved_by'   => null,
            'approved_at'   => null,
            'nomor_surat'   => 'ST-' . Str::upper(Str::random(6)),
            'tanggal_surat' => $mulai->copy()->subDays(3)->toDateString(),
            'tujuan'        => 'Kota ' . Str::title(Str::random(5)),
            'tgl_mulai'     => $mulai->toDateString(),
            'tgl_selesai'   => $akhir->toDateString(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // 2) Insert ke tabel pegawaiperjadin (laporan individu pegawai)
        $laporanIndividu = $laporanLengkap
            ? str_repeat(
                'Laporan individu pegawai ini memenuhi syarat minimal 100 karakter. ',
                3
              )
            : 'Laporan singkat, belum lengkap. ' . $catatan;

        DB::table('pegawaiperjadin')->insert([
            'id_perjadin'      => $perjadinId,
            'id_user'          => $user->nip,
            'role_perjadin'    => 'Anggota',
            'is_lead'          => 0,
            'laporan_individu' => $laporanIndividu,
        ]);

        // 3) OPTIONAL: Insert ke tabel laporankeuangan
        if (
            $buatLaporanKeuangan &&
            $this->statusLaporanDraftId &&
            $this->statusLaporanSelesaiId
        ) {
            $statusLaporanId = $laporanKeuanganSelesai
                ? $this->statusLaporanSelesaiId
                : $this->statusLaporanDraftId;

            $biayaRampung = $laporanKeuanganSelesai
                ? rand(1_000_000, 15_000_000)  // 1–15 juta
                : null;

            $verifiedBy  = $laporanKeuanganSelesai ? $this->ppkNip : null;
            $verifiedAt  = $laporanKeuanganSelesai ? $akhir->copy()->addDays(7) : null;
            $nomorSpm    = $laporanKeuanganSelesai ? 'SPM-' . Str::upper(Str::random(5)) : null;
            $tanggalSpm  = $laporanKeuanganSelesai ? $akhir->copy()->addDays(5)->toDateString() : null;
            $nomorSp2d   = $laporanKeuanganSelesai ? 'SP2D-' . Str::upper(Str::random(5)) : null;
            $tanggalSp2d = $laporanKeuanganSelesai ? $akhir->copy()->addDays(6)->toDateString() : null;

            DB::table('laporankeuangan')->insert([
                'id_perjadin'   => $perjadinId,
                'id_status'     => $statusLaporanId,
                'verified_by'   => $verifiedBy,
                'verified_at'   => $verifiedAt,
                'nomor_spm'     => $nomorSpm,
                'tanggal_spm'   => $tanggalSpm,
                'nomor_sp2d'    => $nomorSp2d,
                'tanggal_sp2d'  => $tanggalSp2d,
                'biaya_rampung' => $biayaRampung,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
