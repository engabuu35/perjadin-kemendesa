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
        // Ini memastikan ID status 1-6 tersedia sebelum dipakai
        $this->call([
            StatusPerjadinSeeder::class,
            StatusLaporanSeeder::class,
        ]);

        // Biar semua insert rapi dalam 1 transaksi
        DB::transaction(function () {

            $now   = Carbon::now();
            $today = Carbon::today();

            // -------------------------------------------------
            // 2. Ambil referensi ID (Jangan Hardcode Angka)
            // -------------------------------------------------
            $unitIds    = DB::table('unitkerja')->pluck('id')->all();
            $pangkatIds = DB::table('pangkatgolongan')->pluck('id')->all();

            if (empty($unitIds) || empty($pangkatIds)) {
                // Fallback jika unit kerja kosong (untuk safety dev)
                $unitIds = [DB::table('unitkerja')->insertGetId(['nama_unit' => 'Dummy Unit', 'kode_uke' => 'DUMMY'])];
                $pangkatIds = [DB::table('pangkatgolongan')->insertGetId(['nama_pangkat' => 'Dummy Pangkat', 'kode_golongan' => 'IV/a'])];
            }

            $roleIds = DB::table('roles')->pluck('id', 'kode'); 
            
            // Ambil ID Status secara dinamis (Lebih aman daripada '?? 6')
            $statusPerjadin = DB::table('statusperjadin')->pluck('id', 'nama_status');
            $statusLaporan  = DB::table('statuslaporan')->pluck('id', 'nama_status');

            $idStatusSedang    = $statusPerjadin['Sedang Berlangsung']    ?? DB::table('statusperjadin')->insertGetId(['nama_status' => 'Sedang Berlangsung']);
            $idStatusSelesai   = $statusPerjadin['Selesai']               ?? DB::table('statusperjadin')->insertGetId(['nama_status' => 'Selesai']);
            $idStatusBelum     = $statusPerjadin['Belum Berlangsung']     ?? DB::table('statusperjadin')->insertGetId(['nama_status' => 'Belum Berlangsung']);
            $idStatusMenungguP = $statusPerjadin['Menunggu Validasi PPK'] ?? DB::table('statusperjadin')->insertGetId(['nama_status' => 'Menunggu Validasi PPK']);

            // FIX UTAMA: Pastikan ID ini ada untuk 'laporankeuangan'
            $idStatusLapSelesai = $statusLaporan['Selesai Dibayar'] 
                                ?? DB::table('statuslaporan')->where('nama_status', 'Selesai Dibayar')->value('id')
                                ?? DB::table('statuslaporan')->insertGetId(['nama_status' => 'Selesai Dibayar']);

            // Cari salah satu PPK
            $ppkNip = null;
            if (isset($roleIds['PPK'])) {
                $ppkNip = DB::table('penugasanperan')->where('role_id', $roleIds['PPK'])->value('user_id');
            }
            // Fallback PPK jika null
            if (!$ppkNip) $ppkNip = '198001012010011001'; 

            // -------------------------------------------------
            // 3. Buat 50 Pegawai Dummy
            // -------------------------------------------------
            // Menggunakan Factory User untuk mempersingkat kode
            $pegawaiList = User::factory()->count(50)->create([
                'password_hash' => Hash::make('password'),
                'is_aktif' => 1
            ]);

            // Assign role PEGAWAI
            if (isset($roleIds['PEGAWAI'])) {
                $roleData = [];
                foreach ($pegawaiList as $p) {
                    $roleData[] = ['user_id' => $p->nip, 'role_id' => $roleIds['PEGAWAI']];
                }
                DB::table('penugasanperan')->insertOrIgnore($roleData);
            }

            // -------------------------------------------------
            // 4. Buat Perjalanan Dinas
            // -------------------------------------------------
            $perjadinMeta = []; 

            foreach ($pegawaiList as $index => $user) {
                $nip  = $user->nip;
                $nama = $user->nama;

                // Skenario 1: Pasti Sedang Berlangsung (Index 0)
                if ($index === 0) {
                    $mulai   = $today->copy()->subDay();
                    $selesai = $today->copy()->addDay();
                    $status  = $idStatusSedang;
                } else {
                    // Skenario Random
                    $monthOffset = rand(-2, 1);
                    $start       = $today->copy()->addMonths($monthOffset)->day(rand(1, 25));
                    $duration    = rand(2, 4);
                    $end         = $start->copy()->addDays($duration);

                    if ($end->lt($today)) {
                        // Sudah lewat -> Random antara Selesai atau Menunggu Validasi
                        $status = rand(0, 1) ? $idStatusSelesai : $idStatusMenungguP;
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
                    'nomor_surat'   => 'ST-' . Str::upper(Str::random(6)),
                    'tanggal_surat' => $mulai->copy()->subDays(3),
                    'tujuan'        => 'Kota ' . Str::upper(Str::random(5)),
                    'tgl_mulai'     => $mulai,
                    'tgl_selesai'   => $selesai,
                    'uraian'        => "Perjalanan dinas dummy untuk {$nama}",
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);

                DB::table('pegawaiperjadin')->insert([
                    'id_perjadin'   => $perjadinId,
                    'id_user'       => $nip,
                    'role_perjadin' => 'Anggota',
                    'is_finished'   => ($status == $idStatusSelesai || $status == $idStatusMenungguP) ? 1 : 0,
                    'is_lead'       => 1,
                ]);

                $perjadinMeta[] = [
                    'id'        => $perjadinId,
                    'nip'       => $nip,
                    'selesai'   => $selesai,
                    'status_pj' => $status,
                ];
            }

            // -------------------------------------------------
            // 5. Buat Laporan & Keuangan (Hanya yg sudah lewat)
            // -------------------------------------------------
            foreach ($perjadinMeta as $meta) {
                $perjadinId = $meta['id'];
                $nip        = $meta['nip'];
                $selesai    = $meta['selesai'];

                if ($selesai->gt($today)) continue;

                // Laporan Pegawai
                $laporanId = DB::table('laporan_perjadin')->insertGetId([
                    'id_perjadin' => $perjadinId,
                    'id_user'     => $nip,
                    'uraian'      => 'Laporan dummy lengkap.',
                    'is_final'    => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);

                // Bukti Dummy
                $buktiData = [
                    ['kategori' => 'Tiket', 'nominal' => 1500000, 'keterangan' => 'Garuda GA-123'],
                    ['kategori' => 'Uang Harian', 'nominal' => 1200000, 'keterangan' => '3 Hari'],
                    ['kategori' => 'Penginapan', 'nominal' => 2000000, 'keterangan' => 'Hotel Grand'],
                ];
                
                $totalBiaya = 0;
                foreach ($buktiData as $b) {
                    $totalBiaya += $b['nominal'];
                    DB::table('bukti_laporan')->insert([
                        'id_laporan' => $laporanId,
                        'kategori'   => $b['kategori'],
                        'nominal'    => $b['nominal'],
                        'keterangan' => $b['keterangan'],
                        'nama_file'  => null,
                        'path_file'  => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                // Laporan Keuangan (Tabel Rekap PPK)
                // INSERT INI YANG TADI ERROR, SEKARANG SUDAH AMAN KARENA ID PASTI ADA
                DB::table('laporankeuangan')->insert([
                    'id_perjadin'   => $perjadinId,
                    'id_status'     => $idStatusLapSelesai, // Menggunakan ID yang valid (bukan hardcode 6)
                    'verified_by'   => $ppkNip,
                    'verified_at'   => $selesai->copy()->addDays(3),
                    'nomor_spm'     => 'SPM-' . Str::random(5),
                    'tanggal_spm'   => $selesai->copy()->addDay(),
                    'nomor_sp2d'    => 'SP2D-' . Str::random(5),
                    'tanggal_sp2d'  => $selesai->copy()->addDays(2),
                    'biaya_rampung' => $totalBiaya,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        });
    }
}