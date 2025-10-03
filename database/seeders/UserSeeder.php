<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash; // <-- Import Hash

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cari dulu role 'Admin IT'
        $adminRole = Role::where('slug', 'admin-it')->first();

        // 2. Buat user baru
        $adminUser = User::create([
            'name' => 'Admin Aplikasi',
            'email' => 'admin@kemendesa.go.id',
            'password' => Hash::make('password') // <-- Password di-hash
        ]);

        // 3. Berikan peran 'Admin IT' ke user tersebut
        $adminUser->roles()->attach($adminRole);
    }
}