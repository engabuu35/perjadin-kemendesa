<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenugasanPeranSeeder extends Seeder
{
    public function run(): void
    {
        // --- AMBIL ID ROLES ---
        $roleIdPimpinan = DB::table('roles')->where('kode', 'PIMPINAN')->first()->id;
        $roleIdPic = DB::table('roles')->where('kode', 'PIC')->first()->id;
        $roleIdPpk = DB::table('roles')->where('kode', 'PPK')->first()->id;
        $roleIdPegawai = DB::table('roles')->where('kode', 'PEGAWAI')->first()->id;

        // --- AMBIL ID USERS ---
        $userPimpinan = DB::table('users')->where('email', 'pimpinan@example.com')->first()->id;
        $userPpk = DB::table('users')->where('email', 'ppk@example.com')->first()->id;
        $userPic1 = DB::table('users')->where('email', 'pic.irwil1@example.com')->first()->id;
        $userPic2 = DB::table('users')->where('email', 'pic.irwil2@example.com')->first()->id;
        $userPic3 = DB::table('users')->where('email', 'pic.irwil3@example.com')->first()->id;
        $userPic4 = DB::table('users')->where('email', 'pic.irwil4@example.com')->first()->id;
        $userPic5 = DB::table('users')->where('email', 'pic.irwil5@example.com')->first()->id;
        $userPicSet = DB::table('users')->where('email', 'pic.setitjen@example.com')->first()->id;
        $userPegawaiMurni = DB::table('users')->where('email', 'pegawai.murni@example.com')->first()->id;
        $userPpn = DB::table('users')->where('email', 'ppnpn.kontrak@example.com')->first()->id;

        // --- TUGASKAN PERAN ---
        DB::table('penugasanperan')->insert([
            
            // Pimpinan punya peran PIMPINAN dan PEGAWAI
            [ 'user_id' => $userPimpinan, 'role_id' => $roleIdPimpinan ],
            [ 'user_id' => $userPimpinan, 'role_id' => $roleIdPegawai ],

            // PPK punya peran PPK dan PEGAWAI
            [ 'user_id' => $userPpk, 'role_id' => $roleIdPpk ],
            [ 'user_id' => $userPpk, 'role_id' => $roleIdPegawai ],

            // Semua PIC punya peran PIC dan PEGAWAI
            [ 'user_id' => $userPic1, 'role_id' => $roleIdPic ],
            [ 'user_id' => $userPic1, 'role_id' => $roleIdPegawai ],

            [ 'user_id' => $userPic2, 'role_id' => $roleIdPic ],
            [ 'user_id' => $userPic2, 'role_id' => $roleIdPegawai ],
            
            [ 'user_id' => $userPic3, 'role_id' => $roleIdPic ],
            [ 'user_id' => $userPic3, 'role_id' => $roleIdPegawai ],

            [ 'user_id' => $userPic4, 'role_id' => $roleIdPic ],
            [ 'user_id' => $userPic4, 'role_id' => $roleIdPegawai ],

            [ 'user_id' => $userPic5, 'role_id' => $roleIdPic ],
            [ 'user_id' => $userPic5, 'role_id' => $roleIdPegawai ],

            [ 'user_id' => $userPicSet, 'role_id' => $roleIdPic ],
            [ 'user_id' => $userPicSet, 'role_id' => $roleIdPegawai ],

            //PEGAWAI MURNI
            [
                'user_id' => $userPegawaiMurni, 'role_id' => $roleIdPegawai
            ],

            //Pegawai PPNPN 
            [
                'user_id' => $userPpn, 'role_id' => $roleIdPegawai
            ],
        ]);
    }
}