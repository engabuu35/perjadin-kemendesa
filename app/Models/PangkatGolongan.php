<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PangkatGolongan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     * Sesuai migrasi: 'pangkatgolongan'
     */
    protected $table = 'pangkatgolongan';

    /**
     * Menunjukkan bahwa model ini TIDAK menggunakan timestamps (created_at, updated_at).
     * Sesuai migrasi: '2025_11_08_073101_create_pangkatgolongan_table.php'
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'kode_golongan',
        'nama_pangkat',
    ];

    /**
     * Mendefinisikan relasi one-to-many ke User.
     * Satu Pangkat/Golongan bisa dimiliki oleh banyak User.
     * Ini adalah kebalikan dari relasi di User.php
     */
    public function users()
    {
        return $this->hasMany(User::class, 'pangkat_gol_id', 'id');
    }
}