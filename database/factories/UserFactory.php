<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UnitKerja;
use App\Models\PangkatGolongan;
use App\Models\Role;
use App\Models\PenugasanPeran;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id_uke'         => UnitKerja::inRandomOrder()->value('id'),
            'pangkat_gol_id' => PangkatGolongan::inRandomOrder()->value('id'),

            // NIP random, string, unik (silakan sesuaikan pola)
            'nip'  => $this->faker->unique()->numerify('1980############'),
            'nama' => $this->faker->name(),

            'email'   => $this->faker->unique()->safeEmail(),
            'no_telp' => $this->faker->phoneNumber(),

            // SEMUA user pakai password sama → "password"
            'password_hash' => Hash::make('password'),

            'is_aktif' => true,
        ];
    }

    /**
     * Tambahkan satu role ke user via tabel penugasanperan.
     * Asumsi tabel roles punya kolom 'kode' berisi 'PEGAWAI', 'PIC', dst.
     */
    public function withRole(string $kodeRole): static
    {
        return $this->afterCreating(function (User $user) use ($kodeRole) {
            $roleId = Role::where('kode', $kodeRole)->value('id');

            if ($roleId) {
                PenugasanPeran::create([
                    'user_id' => $user->nip,   // FK ke users.nip
                    'role_id' => $roleId,
                ]);
            }
        });
    }

    /** State khusus untuk PEGAWAI */
    public function pegawai(): static
    {
        return $this->withRole('PEGAWAI');
    }
}
