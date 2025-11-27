<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummyPegawaiWithPerjadinSeeder extends Seeder
{
    public function run(): void
    {
        // Biar semua insert rapi dalam 1 transaksi
        DB::transaction(function () {

            $now   = Carbon::now();
            $today = Carbon::today();

            // -------------------------------------------------
            // 1. Ambil referensi (unit kerja, pangkat, role, status)
            // -------------------------------------------------
            $unitIds    = DB::table('unitkerja')->pluck('id')->all();
            $pangkatIds = DB::table('pangkatgolongan')->pluck('id')->all();

            if (empty($unitIds) || empty($pangkatIds)) {
                throw new \RuntimeException('unitkerja / pangkatgolongan belum terisi, jalankan seeder utamanya dulu.');
            }

            $roleIds = DB::table('roles')->pluck('id', 'kode');            // 'PEGAWAI' => 2, dst
            $statusPerjadin = DB::table('statusperjadin')->pluck('id', 'nama_status');
            $statusLaporan  = DB::table('statuslaporan')->pluck('id', 'nama_status');

            $idStatusSedang    = $statusPerjadin['Sedang Berlangsung']        ?? 3;
            $idStatusSelesai   = $statusPerjadin['Selesai']                    ?? 7;
            $idStatusBelum     = $statusPerjadin['Belum Berlangsung']         ?? 2;
            $idStatusMenungguL = $statusPerjadin['Menunggu Laporan']          ?? 4;
            $idStatusMenungguP = $statusPerjadin['Menunggu Validasi PPK']     ?? 6;

            $idStatusLapSelesai = $statusLaporan['Selesai Dibayar'] ?? 6;

            // Cari salah satu PPK (kalau ada) untuk kolom verified_by
            $ppkNip = null;
            if (isset($roleIds['PPK'])) {
                $ppkNip = DB::table('penugasanperan')
                    ->where('role_id', $roleIds['PPK'])
                    ->value('user_id');
            }

            // -------------------------------------------------
            // 2. Buat 50 pegawai dummy + role PEGAWAI
            // -------------------------------------------------
            $pegawai = []; // [ [nip, nama], ... ]

            for ($i = 1; $i <= 50; $i++) {
                // Generate NIP unik
                do {
                    $nip = (string) rand(1980000000000000, 1999999999999999);
                } while (DB::table('users')->where('nip', $nip)->exists());

                $nama  = "Pegawai Dummy {$i}";
                $email = "pegawai{$i}@dummy.test";

                DB::table('users')->insert([
                    'id_uke'         => $unitIds[array_rand($unitIds)],
                    'pangkat_gol_id' => $pangkatIds[array_rand($pangkatIds)],
                    'nip'           => $nip,
                    'nama'          => $nama,
                    'email'         => $email,
                    'no_telp'       => '08' . rand(1000000000, 9999999999),
                    'password_hash' => Hash::make('password'), // password = 'password'
                    'is_aktif'      => 1,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);

                // role PEGAWAI
                if (isset($roleIds['PEGAWAI'])) {
                    DB::table('penugasanperan')->insert([
                        'user_id' => $nip,
                        'role_id' => $roleIds['PEGAWAI'],
                    ]);
                }

                $pegawai[] = ['nip' => $nip, 'nama' => $nama];
            }

            // -------------------------------------------------
            // 3. Buat Perjalanan Dinas untuk tiap pegawai
            //    - minimal 1 perjadin PASTI "Sedang Berlangsung" hari ini
            //    - sisanya acak: sudah selesai / belum mulai / sedang berjalan
            // -------------------------------------------------
            $perjadinMeta = []; // simpan info utk dipakai di langkah 4

            foreach ($pegawai as $index => $pgw) {
                $nip  = $pgw['nip'];
                $nama = $pgw['nama'];

                if ($index === 0) {
                    // Skenario khusus: PASTI sedang berlangsung hari ini
                    $mulai   = $today->copy()->subDay();   // kemarin
                    $selesai = $today->copy()->addDay();   // besok
                    $status  = $idStatusSedang;
                } else {
                    // Skenario umum: tanggal random ± beberapa bulan dari sekarang
                    $monthOffset = rand(-5, 1); // 5 bulan lalu s/d 1 bulan depan
                    $start       = $today->copy()->addMonths($monthOffset)->day(rand(1, 25));
                    $duration    = rand(2, 5);
                    $end         = $start->copy()->addDays($duration);

                    if ($end->lt($today)) {
                        // sudah lewat -> sebagian selesai, sebagian menunggu validasi
                        $status = rand(0, 1) ? $idStatusSelesai : $idStatusMenungguP;
                    } elseif ($start->gt($today)) {
                        // belum mulai
                        $status = $idStatusBelum;
                    } else {
                        // sedang berlangsung
                        $status = $idStatusSedang;
                    }

                    $mulai   = $start;
                    $selesai = $end;
                }

                $nomorSurat = 'ST-' . Str::upper(Str::random(6));
                $tujuan     = 'Kota ' . Str::upper(Str::random(5));

                $perjadinId = DB::table('perjalanandinas')->insertGetId([
                    'id_pembuat'    => $nip,
                    'id_status'     => $status,
                    'approved_by'   => null,
                    'approved_at'   => null,
                    'nomor_surat'   => $nomorSurat,
                    'tanggal_surat' => $mulai->copy()->subDays(3)->toDateString(),
                    'tujuan'        => $tujuan,
                    'tgl_mulai'     => $mulai->toDateString(),
                    'tgl_selesai'   => $selesai->toDateString(),
                    'uraian'        => "Perjalanan dinas dummy untuk {$nama}",
                    'surat_tugas'   => null,
                    'id_atasan'     => null,
                    'tgl_acc'       => null,
                    'pdf_keuangan'  => null,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);

                // relasi pegawai-perjadin (pegawai ini sebagai lead)
                DB::table('pegawaiperjadin')->insert([
                    'id_perjadin'      => $perjadinId,
                    'id_user'          => $nip,
                    'role_perjadin'    => 'Anggota',
                    'is_finished'      => 0,
                    'is_lead'          => 1,
                    'laporan_individu' => null,
                ]);

                $perjadinMeta[] = [
                    'id'         => $perjadinId,
                    'nip'        => $nip,
                    'mulai'      => $mulai,
                    'selesai'    => $selesai,
                    'status_pj'  => $status,
                ];
            }

            // -------------------------------------------------
            // 4. Buat laporan_perjadin, bukti_laporan, dan laporankeuangan
            //    - hanya untuk perjadin yang SUDAH selesai (tgl_selesai <= today)
            // -------------------------------------------------
            foreach ($perjadinMeta as $meta) {
                $perjadinId = $meta['id'];
                $nip        = $meta['nip'];
                $mulai      = $meta['mulai'];
                $selesai    = $meta['selesai'];

                // Hanya buat laporan & keuangan utk yang sudah selesai (tgl_selesai <= hari ini)
                if ($selesai->gt($today)) {
                    continue;
                }

                // --- laporan_perjadin (header laporan per pegawai) ---
                $laporanId = DB::table('laporan_perjadin')->insertGetId([
                    'id_perjadin' => $perjadinId,
                    'id_user'     => $nip,
                    'uraian'      => 'Laporan dummy perjalanan dinas.',
                    'is_final'    => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);

                // --- bukti_laporan (rinciannya) ---
                $buktiList = [
                    [
                        'kategori'   => 'Tiket',
                        'nominal'    => rand(300_000, 1_500_000),
                        'keterangan' => 'Tiket transportasi',
                    ],
                    [
                        'kategori'   => 'Uang Harian',
                        'nominal'    => rand(500_000, 2_500_000),
                        'keterangan' => 'Uang harian perjalanan',
                    ],
                    [
                        'kategori'   => 'Penginapan',
                        'nominal'    => rand(500_000, 3_000_000),
                        'keterangan' => 'Biaya penginapan',
                    ],
                ];

                $total = 0;
                foreach ($buktiList as $b) {
                    $total += $b['nominal'];

                    DB::table('bukti_laporan')->insert([
                        'id_laporan' => $laporanId,
                        'nama_file'  => null,
                        'path_file'  => null,
                        'kategori'   => $b['kategori'],
                        'nominal'    => $b['nominal'],
                        'keterangan' => $b['keterangan'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                // --- laporankeuangan (dipakai PPK untuk tabel rekap) ---
                DB::table('laporankeuangan')->insert([
                    'id_perjadin'   => $perjadinId,
                    'id_status'     => $idStatusLapSelesai,          // "Selesai Dibayar"
                    'verified_by'   => $ppkNip,
                    'verified_at'   => $selesai->copy()->addDays(3),
                    'nomor_spm'     => 'SPM-' . Str::upper(Str::random(6)),
                    'tanggal_spm'   => $selesai->copy()->addDay()->toDateString(),
                    'nomor_sp2d'    => 'SP2D-' . Str::upper(Str::random(6)),
                    'tanggal_sp2d'  => $selesai->copy()->addDays(2)->toDateString(),
                    'biaya_rampung' => $total,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        });
    }
}
