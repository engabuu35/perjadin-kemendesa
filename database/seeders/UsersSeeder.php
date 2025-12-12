<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Notifications\NewAccountNotification;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan Data Transaksi (Opsional, agar bersih)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Jangan truncate users/roles jika ingin menumpuk, tapi biasanya seeder user perlu bersih atau cek unique
        // Disini saya truncate transaksi saja agar aman
        DB::table('perjalanandinas')->truncate();
        DB::table('pegawaiperjadin')->truncate();
        DB::table('laporan_perjadin')->truncate();
        DB::table('bukti_laporan')->truncate();
        DB::table('geotagging')->truncate();
        DB::table('laporankeuangan')->truncate(); 
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Data transaksi dibersihkan. Memulai seeding user...');

        // --- AMBIL DATA MASTER (UKE & PANGKAT) ---
        $ukeItjen = DB::table('unitkerja')->where('kode_uke', 'ITJEN')->value('id');
        $ukeSetitjen = DB::table('unitkerja')->where('kode_uke', 'SETITJEN')->value('id');
        $ukeIrwil1 = DB::table('unitkerja')->where('kode_uke', 'IRWIL1')->value('id');
        $ukeIrwil2 = DB::table('unitkerja')->where('kode_uke', 'IRWIL2')->value('id');
        $ukeIrwil3 = DB::table('unitkerja')->where('kode_uke', 'IRWIL3')->value('id');
        $ukeIrwil4 = DB::table('unitkerja')->where('kode_uke', 'IRWIL4')->value('id');
        $ukeIrwil5 = DB::table('unitkerja')->where('kode_uke', 'IRWIL5')->value('id');
        
        $pangkat3a = DB::table('pangkatgolongan')->where('kode_golongan', 'III/a')->value('id');
        $pangkat3b = DB::table('pangkatgolongan')->where('kode_golongan', 'III/b')->value('id');
        $pangkat3c = DB::table('pangkatgolongan')->where('kode_golongan', 'III/c')->value('id');
        $pangkat4a = DB::table('pangkatgolongan')->where('kode_golongan', 'IV/a')->value('id');
        $pangkatPpn = DB::table('pangkatgolongan')->where('kode_golongan', '-')->value('id');

        $allUkes = [$ukeSetitjen, $ukeIrwil1, $ukeIrwil2, $ukeIrwil3, $ukeIrwil4, $ukeIrwil5];
        $allPangkats = [$pangkat3a, $pangkat3b, $pangkat3c];

        // --- AMBIL DATA MASTER (ROLES) ---
        $roleMap = [
            'PIMPINAN' => DB::table('roles')->where('kode', 'PIMPINAN')->value('id'),
            'PIC'      => DB::table('roles')->where('kode', 'PIC')->value('id'),
            'PPK'      => DB::table('roles')->where('kode', 'PPK')->value('id'),
            'PEGAWAI'  => DB::table('roles')->where('kode', 'PEGAWAI')->value('id'),
        ];

        $defaultPasswordRaw = 'password'; // Password mentah untuk email

        // --- DEFINISI USER (DATA MENTAH) ---
        // Saya gabungkan logic Grup 1, 2, dan 3 ke dalam satu array besar agar bisa diloop
        $usersToCreate = [];

        // GRUP 1: USER UTAMA
        $usersGrup1 = [
            [
                'data' => [
                    'id_uke' => $ukeItjen, 'pangkat_gol_id' => $pangkat4a, 'nip' => '198001012010011001',
                    'nama' => 'Pimpinan 1', 'email' => 'pimpinan@example.com',
                ],
                'role_kode' => 'PIMPINAN'
            ],
            [
                'data' => [
                    'id_uke' => $ukeItjen, 'pangkat_gol_id' => $pangkat4a, 'nip' => '198001012010011002',
                    'nama' => 'Pimpinan 2', 'email' => 'pimpinan@example.com',
                ],
                'role_kode' => 'PIMPINAN'
            ],
            [
                'data' => [
                    'id_uke' => $ukeSetitjen, 'pangkat_gol_id' => $pangkat3a, 'nip' => '199002022020022001',
                    'nama' => 'PPK 1', 'email' => 'ppk@example.com',
                ],
                'role_kode' => 'PPK'
            ],
            [
                'data' => [
                    'id_uke' => $ukeSetitjen, 'pangkat_gol_id' => $pangkat3a, 'nip' => '199002022020022002',
                    'nama' => 'PPK 2', 'email' => 'ppk@example.com',
                ],
                'role_kode' => 'PPK'
            ],
            [
                'data' => [
                    'id_uke' => $ukeIrwil1, 'pangkat_gol_id' => $pangkat3a, 'nip' => '199103032021031001',
                    'nama' => 'PIC Irwil 1', 'email' => 'pic.irwil1@example.com',
                ],
                'role_kode' => 'PIC'
            ],
            [
                'data' => [
                    'id_uke' => $ukeIrwil1, 'pangkat_gol_id' => $pangkat3a, 'nip' => '199103032021031002',
                    'nama' => 'PIC Irwil 2', 'email' => 'pic.irwil1@example.com',
                ],
                'role_kode' => 'PIC'
            ],
            [
                'data' => [
                    'id_uke' => $ukeIrwil1, 'pangkat_gol_id' => $pangkat3b, 'nip' => '199909092029092001',
                    'nama' => 'Pegawai 1', 'email' => 'pegawai.murni@example.com',
                ],
                'role_kode' => 'PEGAWAI'
            ],
            [
                'data' => [
                    'id_uke' => $ukeIrwil1, 'pangkat_gol_id' => $pangkat3b, 'nip' => '199909092029092002',
                    'nama' => 'Pegawai 2', 'email' => 'pegawai.murni@example.com',
                ],
                'role_kode' => 'PEGAWAI'
            ],
        ];
        $usersToCreate = array_merge($usersToCreate, $usersGrup1);

        // GRUP 2: USER BARU (Loop 1-5 saja untuk sampel agar tidak spam email terlalu banyak saat testing)
        for ($i = 1; $i <= 5; $i++) {
            $nip = '2000101020301010' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $usersToCreate[] = [
                'data' => [
                    'id_uke' => $allUkes[array_rand($allUkes)],
                    'pangkat_gol_id' => $allPangkats[array_rand($allPangkats)],
                    'nip' => $nip,
                    'nama' => 'Pegawai Baru ' . $i,
                    'email' => 'pegawai.baru.' . $i . '@example.com',
                ],
                'role_kode' => 'PEGAWAI'
            ];
        }

        // --- EKSEKUSI PEMBUATAN USER & KIRIM EMAIL ---
        foreach ($usersToCreate as $item) {
            $userData = $item['data'];
            
            // Cek apakah user sudah ada
            $user = User::where('nip', $userData['nip'])->first();

            if (!$user) {
                // Tambahkan field default yang dibutuhkan tabel users
                $userData['password_hash'] = Hash::make($defaultPasswordRaw);
                $userData['is_aktif'] = true;
                $userData['created_at'] = now();
                $userData['updated_at'] = now();

                // Gunakan Model User::create agar return instance User (untuk notifikasi)
                // Pastikan model User fillable-nya sudah mencakup field ini, atau gunakan forceCreate
                $user = User::forceCreate($userData);

                $this->command->info("User dibuat: {$user->nama} ({$user->nip})");

                // Assign Role
                if (isset($roleMap[$item['role_kode']])) {
                    DB::table('penugasanperan')->insert([
                        'user_id' => $user->nip,
                        'role_id' => $roleMap[$item['role_kode']]
                    ]);
                }
            } else {
                $this->command->warn("User sudah ada: {$user->nama}. Melewati pembuatan.");
            }

            // --- KIRIM EMAIL NOTIFIKASI ---
            try {
                // Generate Token Reset Password
                $token = Password::createToken($user);

                // Kirim Notifikasi
                $user->notify(new NewAccountNotification($token, $user->nip, $defaultPasswordRaw));
                
                $this->command->info("--> Email terkirim ke: {$user->email}");
            } catch (\Exception $e) {
                $this->command->error("--> Gagal kirim email ke {$user->email}: " . $e->getMessage());
            }
        }
    }
}