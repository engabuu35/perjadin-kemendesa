<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class DummyPegawaiWithPerjadinSeeder extends Seeder
{
    public function run(): void
    {
        // 1. JALANKAN SEEDER STATUS TERLEBIH DAHULU (WAJIB)
        $this->call([
            StatusPerjadinSeeder::class,
            StatusLaporanSeeder::class,
        ]);

        DB::transaction(function () {

            $now   = Carbon::now();
            $today = Carbon::today();

            $roleIds = DB::table('roles')->pluck('id', 'kode'); 
            
            // Ambil ID Status
            $statusPerjadin = DB::table('statusperjadin')->pluck('id', 'nama_status');
            $statusLaporan  = DB::table('statuslaporan')->pluck('id', 'nama_status');

            // --- MAPPING STATUS ---
            $idStatusSedang    = $statusPerjadin['Sedang Berlangsung']    ?? 2;
            $idStatusSelesai   = $statusPerjadin['Selesai']               ?? 5;
            $idStatusBelum     = $statusPerjadin['Belum Berlangsung']     ?? 1;
            
            // Status PPK
            $idStatusMenungguP = $statusPerjadin['Menunggu Validasi PPK'] ?? 
                                 $statusPerjadin['Menunggu Verifikasi'] ?? 4; 

            // PERBAIKAN UTAMA: Tambahkan Status PIC
            $idStatusPic = $statusPerjadin['Menunggu Verifikasi Laporan'] ?? 
                           DB::table('statusperjadin')->insertGetId(['nama_status' => 'Menunggu Verifikasi Laporan']);

            $idStatusLapSelesai = $statusLaporan['Selesai Dibayar'] ?? 
                                  $statusLaporan['Selesai'] ?? 6;

            $ppkNip = '199002022020022002'; 

            // 3. Buat 50 Pegawai Dummy
            $pegawaiList = User::factory()->count(50)->create([
                'password_hash' => Hash::make('password'),
                'is_aktif' => 1
            ]);

            if (isset($roleIds['PEGAWAI'])) {
                $roleData = [];
                foreach ($pegawaiList as $p) {
                    $roleData[] = ['user_id' => $p->nip, 'role_id' => $roleIds['PEGAWAI']];
                }
                DB::table('penugasanperan')->insertOrIgnore($roleData);
            }

            // 4. Buat Perjalanan Dinas
            $perjadinMeta = []; 

            foreach ($pegawaiList as $index => $user) {
                $nip  = $user->nip;
                $nama = $user->nama;

                if ($index === 0) {
                    $mulai   = $today->copy()->subDay();
                    $selesai = $today->copy()->addDay();
                    $status  = $idStatusSedang;
                } else {
                    $monthOffset = rand(-2, 1);
                    $start       = $today->copy()->addMonths($monthOffset)->day(rand(1, 25));
                    $duration    = rand(2, 4);
                    $end         = $start->copy()->addDays($duration);

                    if ($end->lt($today)) {
                        // LOGIKA RANDOM BARU:
                        // 33% Selesai (Riwayat)
                        // 33% Menunggu PPK (Dashboard PPK)
                        // 33% Menunggu PIC (Dashboard PIC - INI YANG ANDA CARI)
                        
                        $rand = rand(1, 3);
                        if ($rand == 1) {
                            $status = $idStatusSelesai;
                        } elseif ($rand == 2) {
                            $status = $idStatusMenungguP;
                        } else {
                            $status = $idStatusPic; // Masuk ke dashboard PIC
                        }

                    } elseif ($start->gt($today)) {
                        $status = $idStatusBelum;
                    } else {
                        $status = $idStatusSedang;
                    }

                    $mulai   = $start;
                    $selesai = $end;
                }

                $perjadinId = DB::table('perjalanandinas')->insertGetId([
                    'id_pembuat'    => $nip, 
                    'id_status'     => $status,
                    'approved_by'   => $ppkNip, 
                    'approved_at'   => $mulai->copy()->subDays(5),
                    'nomor_surat'   => 'ST-DUMMY-' . Str::upper(Str::random(4)),
                    'tanggal_surat' => $mulai->copy()->subDays(3),
                    'tujuan'        => 'Kota ' . Str::upper(Str::random(5)),
                    'tgl_mulai'     => $mulai,
                    'tgl_selesai'   => $selesai,
                    'uraian'        => "Perjalanan dinas dummy untuk {$nama}",
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);

                // Pivot Pegawai
                DB::table('pegawaiperjadin')->insert([
                    'id_perjadin'   => $perjadinId,
                    'id_user'       => $nip,
                    'role_perjadin' => 'Anggota',
                    // Pegawai dianggap sudah selesai tugasnya jika status sudah masuk PIC/PPK/Selesai
                    'is_finished'   => ($status == $idStatusSelesai || $status == $idStatusMenungguP || $status == $idStatusPic) ? 1 : 0,
                    'is_lead'       => 1,
                ]);

                // Buat Laporan Keuangan Dummy HANYA jika status Menunggu Validasi PPK (Agar PIC bersih)
                if ($status == $idStatusMenungguP) {
                    $idLapMenunggu = $statusLaporan['Menunggu Verifikasi'] ?? 3;

                    DB::table('laporankeuangan')->insert([
                        'id_perjadin'   => $perjadinId,
                        'id_status'     => $idLapMenunggu,
                        'verified_by'   => null,
                        'verified_at'   => null,
                        'biaya_rampung' => rand(3000000, 5000000),
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ]);
                }

                $perjadinMeta[] = [
                    'id'        => $perjadinId,
                    'nip'       => $nip,
                    'selesai'   => $selesai,
                    'status_pj' => $status,
                ];
            }

            // 5. Buat Laporan Pegawai & Bukti (Untuk semua yang sudah lewat tanggalnya)
            foreach ($perjadinMeta as $meta) {
                // Generate bukti untuk: Selesai, Menunggu PPK, DAN Menunggu PIC
                // Agar PIC bisa melihat rincian biaya saat mau kirim
                if ($meta['status_pj'] != $idStatusSelesai && 
                    $meta['status_pj'] != $idStatusMenungguP && 
                    $meta['status_pj'] != $idStatusPic) continue;

                $perjadinId = $meta['id'];
                $nip        = $meta['nip'];
                $selesai    = $meta['selesai'];

                $laporanId = DB::table('laporan_perjadin')->insertGetId([
                    'id_perjadin' => $perjadinId,
                    'id_user'     => $nip,
                    'uraian'      => 'Laporan dummy generated.',
                    'is_final'    => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);

                $this->insertBuktiDummy($laporanId, $now);

                // Jika Selesai, buat laporan keuangan final
                if ($meta['status_pj'] == $idStatusSelesai) {
                    DB::table('laporankeuangan')->updateOrInsert(
                        ['id_perjadin' => $perjadinId],
                        [
                            'id_status'     => $idStatusLapSelesai,
                            'verified_by'   => $ppkNip,
                            'verified_at'   => $selesai->copy()->addDays(3),
                            'nomor_spm'     => 'SPM-' . Str::random(5),
                            'tanggal_spm'   => $selesai->copy()->addDay(),
                            'nomor_sp2d'    => 'SP2D-' . Str::random(5),
                            'tanggal_sp2d'  => $selesai->copy()->addDays(2),
                            'biaya_rampung' => 4700000,
                            'created_at'    => $now,
                            'updated_at'    => $now,
                        ]
                    );
                }
            }
        });
    }

    private function insertBuktiDummy($laporanId, $now) {
        $buktiData = [
            ['kategori' => 'Tiket', 'nominal' => 1500000, 'ket' => null],
            ['kategori' => 'Uang Harian', 'nominal' => 1200000, 'ket' => null],
            ['kategori' => 'Penginapan', 'nominal' => 2000000, 'ket' => null],
            ['kategori' => 'Maskapai', 'nominal' => 0, 'ket' => 'Garuda GA-123'],
            ['kategori' => 'Nama Penginapan', 'nominal' => 0, 'ket' => 'Hotel Grand'],
        ];
        
        foreach ($buktiData as $b) {
            DB::table('bukti_laporan')->insert([
                'id_laporan' => $laporanId,
                'kategori'   => $b['kategori'],
                'nominal'    => $b['nominal'],
                'keterangan' => $b['ket'],
                'nama_file'  => null,
                'path_file'  => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}