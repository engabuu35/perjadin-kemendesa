<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
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

        // Kumpulan UKE dan Pangkat untuk data random
        $allUkes = [$ukeSetitjen, $ukeIrwil1, $ukeIrwil2, $ukeIrwil3, $ukeIrwil4, $ukeIrwil5];
        $allPangkats = [$pangkat3a, $pangkat3b, $pangkat3c];

        // --- AMBIL DATA MASTER (ROLES) ---
        // Kita petakan KODE ke ID untuk mempermudah
        $roleMap = [
            'PIMPINAN' => DB::table('roles')->where('kode', 'PIMPINAN')->value('id'),
            'PIC'      => DB::table('roles')->where('kode', 'PIC')->value('id'),
            'PPK'      => DB::table('roles')->where('kode', 'PPK')->value('id'),
            'PEGAWAI'  => DB::table('roles')->where('kode', 'PEGAWAI')->value('id'),
        ];


        // --- GRUP 1: 10 USER UTAMA (UNTUK DATA SELESAI) ---
        $usersGrup1 = [
            [
                'data' => [
                    'id_uke' => $ukeItjen, 'pangkat_gol_id' => $pangkat4a, 'nip' => '198001012010011001',
                    'nama' => 'Paijo Pimpinan', 'email' => 'pimpinan@example.com',
                    'password_hash' => Hash::make('password'), 'is_aktif' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ],
                'roles' => ['PIMPINAN'] // <-- DIUBAH: Hanya satu peran utama
            ],
            [
                'data' => [
                    'id_uke' => $ukeSetitjen, 'pangkat_gol_id' => $pangkat3a, 'nip' => '199002022020022002',
                    'nama' => 'Cahyo PPK', 'email' => 'ppk@example.com',
                    'password_hash' => Hash::make('password'), 'is_aktif' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ],
                'roles' => ['PPK'] // <-- DIUBAH: Hanya satu peran utama
            ],
            [
                'data' => [
                    'id_uke' => $ukeIrwil1, 'pangkat_gol_id' => $pangkat3a, 'nip' => '199103032021031003',
                    'nama' => 'PIC Irwil 1', 'email' => 'pic.irwil1@example.com',
                    'password_hash' => Hash::make('password'), 'is_aktif' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ],
                'roles' => ['PIC'] // <-- DIUBAH: Hanya satu peran utama
            ],
            [
                'data' => [
                    'id_uke' => $ukeIrwil2, 'pangkat_gol_id' => $pangkat3a, 'nip' => '199204042022042004',
                    'nama' => 'PIC Irwil 2', 'email' => 'pic.irwil2@example.com',
                    'password_hash' => Hash::make('password'), 'is_aktif' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ],
                'roles' => ['PIC'] // <-- DIUBAH: Hanya satu peran utama
            ],
            [
                'data' => [
                    'id_uke' => $ukeIrwil3, 'pangkat_gol_id' => $pangkat3a, 'nip' => '199305052023052005',
                    'nama' => 'PIC Irwil 3', 'email' => 'pic.irwil3@example.com',
                    'password_hash' => Hash::make('password'), 'is_aktif' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ],
                'roles' => ['PIC'] // <-- DIUBAH: Hanya satu peran utama
            ],
            [
                'data' => [
                    'id_uke' => $ukeIrwil4, 'pangkat_gol_id' => $pangkat3a, 'nip' => '199406062024062006',
                    'nama' => 'PIC Irwil 4', 'email' => 'pic.irwil4@example.com',
                    'password_hash' => Hash::make('password'), 'is_aktif' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ],
                'roles' => ['PIC'] // <-- DIUBAH: Hanya satu peran utama
            ],
            [
                'data' => [
                    'id_uke' => $ukeIrwil5, 'pangkat_gol_id' => $pangkat3a, 'nip' => '199507072025072007',
                    'nama' => 'PIC Irwil 5', 'email' => 'pic.irwil5@example.com',
                    'password_hash' => Hash::make('password'), 'is_aktif' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ],
                'roles' => ['PIC'] // <-- DIUBAH: Hanya satu peran utama
            ],
            [
                'data' => [
                    'id_uke' => $ukeSetitjen, 'pangkat_gol_id' => $pangkat3a, 'nip' => '199608082026082008',
                    'nama' => 'PIC Sekretariat', 'email' => 'pic.setitjen@example.com',
                    'password_hash' => Hash::make('password'), 'is_aktif' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ],
                'roles' => ['PIC'] // <-- DIUBAH: Hanya satu peran utama
            ],
            [
                'data' => [
                    'id_uke' => $ukeIrwil1, 'pangkat_gol_id' => $pangkat3b, 'nip' => '199909092029092009',
                    'nama' => 'Budi Pegawai', 'email' => 'pegawai.murni@example.com',
                    'password_hash' => Hash::make('password'), 'is_aktif' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ],
                'roles' => ['PEGAWAI'] // <-- Ini sudah benar (Hanya pegawai)
            ],
            [
                'data' => [
                    'id_uke' => $ukeIrwil2, 'pangkat_gol_id' => $pangkatPpn, 'nip' => 'PPNPN-001', 
                    'nama' => 'Dimas PPNPN', 'email' => 'ppnpn.kontrak@example.com',
                    'password_hash' => Hash::make('password'), 'is_aktif' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ],
                'roles' => ['PEGAWAI'] // <-- Ini sudah benar (Hanya pegawai)
            ]
        ];

        // Memasukkan user Grup 1 dan peran mereka
        foreach ($usersGrup1 as $user) {
            DB::table('users')->insert($user['data']);
            $nip = $user['data']['nip'];
            foreach ($user['roles'] as $roleKode) {
                DB::table('penugasanperan')->insert([
                    'user_id' => $nip,
                    'role_id' => $roleMap[$roleKode]
                ]);
            }
        }

        // --- GRUP 2: 10 USER BARU (BELUM ADA PERJADIN) ---
        for ($i = 1; $i <= 10; $i++) {
            $nip = '2000101020301010' . str_pad($i, 2, '0', STR_PAD_LEFT);
            DB::table('users')->insert([
                'id_uke' => $allUkes[array_rand($allUkes)],
                'pangkat_gol_id' => $allPangkats[array_rand($allPangkats)],
                'nip' => $nip,
                'nama' => 'Pegawai Baru ' . $i,
                'email' => 'pegawai.baru.' . $i . '@example.com',
                'password_hash' => Hash::make('password'),
                'is_aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            // Langsung tugaskan peran
            DB::table('penugasanperan')->insert([
                'user_id' => $nip,
                'role_id' => $roleMap['PEGAWAI'] // <-- Sudah benar (Hanya pegawai)
            ]);
        }

        // --- GRUP 3: 10 USER ON-PROGRESS (UNTUK PERJADIN AKTIF) ---
        for ($i = 1; $i <= 10; $i++) {
            $nip = '2001111120311110' . str_pad($i, 2, '0', STR_PAD_LEFT);
            DB::table('users')->insert([
                'id_uke' => $allUkes[array_rand($allUkes)],
                'pangkat_gol_id' => $allPangkats[array_rand($allPangkats)],
                'nip' => $nip,
                'nama' => 'Pegawai Progress ' . $i,
                'email' => 'pegawai.progress.' . $i . '@example.com',
                'password_hash' => Hash::make('password'),
                'is_aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            // Langsung tugaskan peran
            DB::table('penugasanperan')->insert([
                'user_id' => $nip,
                'role_id' => $roleMap['PEGAWAI'] // <-- Sudah benar (Hanya pegawai)
            ]);
        }
    }
}