<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Panggil seeder yang sudah kita buat untuk mengisi tabel 'roles'
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);
        $this->call(LaporanKeuanganSeeder::class);
        
    }

    
}