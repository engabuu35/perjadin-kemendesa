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
        // --- AMBIL DATA DARI TABEL MASTER ---
        $ukeItjen = DB::table('unitkerja')->where('kode_uke', 'ITJEN')->first()->id;
        $ukeSetitjen = DB::table('unitkerja')->where('kode_uke', 'SETITJEN')->first()->id;
        $ukeIrwil1 = DB::table('unitkerja')->where('kode_uke', 'IRWIL1')->first()->id;
        $ukeIrwil2 = DB::table('unitkerja')->where('kode_uke', 'IRWIL2')->first()->id;
        $ukeIrwil3 = DB::table('unitkerja')->where('kode_uke', 'IRWIL3')->first()->id;
        $ukeIrwil4 = DB::table('unitkerja')->where('kode_uke', 'IRWIL4')->first()->id;
        $ukeIrwil5 = DB::table('unitkerja')->where('kode_uke', 'IRWIL5')->first()->id;
        
        $pangkat3a = DB::table('pangkatgolongan')->where('kode_golongan', 'III/a')->first()->id;
        $pangkat4a = DB::table('pangkatgolongan')->where('kode_golongan', 'IV/a')->first()->id;

        // --- BUAT USER CONTOH ---
        DB::table('users')->insert([
            [
                // User 1: Pimpinan (di UKE-1)
                'id_uke' => $ukeItjen,
                'pangkat_gol_id' => $pangkat4a,
                'nip' => '198001012010011001',
                'nama' => 'Paijo Pimpinan',
                'email' => 'pimpinan@example.com',
                'password_hash' => Hash::make('password'),
                'is_aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                // User 2: PPK (di Sekretariat)
                'id_uke' => $ukeSetitjen,
                'pangkat_gol_id' => $pangkat3a,
                'nip' => '199002022020022002',
                'nama' => 'Cahyo PPK',
                'email' => 'ppk@example.com',
                'password_hash' => Hash::make('password'),
                'is_aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                // User 3: PIC untuk IRWIL 1
                'id_uke' => $ukeIrwil1,
                'pangkat_gol_id' => $pangkat3a,
                'nip' => '199103032021031003',
                'nama' => 'PIC Irwil 1',
                'email' => 'pic.irwil1@example.com',
                'password_hash' => Hash::make('password'),
                'is_aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                // User 4: PIC untuk IRWIL 2
                'id_uke' => $ukeIrwil2,
                'pangkat_gol_id' => $pangkat3a,
                'nip' => '199204042022042004',
                'nama' => 'PIC Irwil 2',
                'email' => 'pic.irwil2@example.com',
                'password_hash' => Hash::make('password'),
                'is_aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                // User 5: PIC untuk IRWIL 3
                'id_uke' => $ukeIrwil3,
                'pangkat_gol_id' => $pangkat3a,
                'nip' => '199305052023052005',
                'nama' => 'PIC Irwil 3',
                'email' => 'pic.irwil3@example.com',
                'password_hash' => Hash::make('password'),
                'is_aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                // User 6: PIC untuk IRWIL 4
                'id_uke' => $ukeIrwil4,
                'pangkat_gol_id' => $pangkat3a,
                'nip' => '199406062024062006',
                'nama' => 'PIC Irwil 4',
                'email' => 'pic.irwil4@example.com',
                'password_hash' => Hash::make('password'),
                'is_aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                // User 7: PIC untuk IRWIL 5
                'id_uke' => $ukeIrwil5,
                'pangkat_gol_id' => $pangkat3a,
                'nip' => '199507072025072007',
                'nama' => 'PIC Irwil 5',
                'email' => 'pic.irwil5@example.com',
                'password_hash' => Hash::make('password'),
                'is_aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                // User 8: PIC untuk SETITJEN
                'id_uke' => $ukeSetitjen,
                'pangkat_gol_id' => $pangkat3a,
                'nip' => '199608082026082008',
                'nama' => 'PIC Sekretariat',
                'email' => 'pic.setitjen@example.com',
                'password_hash' => Hash::make('password'),
                'is_aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                // User 9: Pegawai Murni (di Irwil 1)
                'id_uke' => $ukeIrwil1, // <-- Kita tempatkan dia di Irwil 1
                'pangkat_gol_id' => $pangkat3a,
                'nip' => '199909092029092009',
                'nama' => 'Budi Pegawai',
                'email' => 'pegawai.murni@example.com',
                'password_hash' => Hash::make('password'),
                'is_aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                // User 10: PPNPN (di Irwil 2)
                'id_uke' => $ukeIrwil2, // <-- Kita tempatkan dia di Irwil 2
                'pangkat_gol_id' => DB::table('pangkatgolongan')->where('kode_golongan', '-')->first()->id, // <-- Ambil ID PPNPN
                'nip' => 'PPNPN-001', // NIP PPNPN mungkin formatnya beda
                'nama' => 'Dimas PPNPN',
                'email' => 'ppnpn.kontrak@example.com',
                'password_hash' => Hash::make('password'),
                'is_aktif' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]
        ]);
    }
}