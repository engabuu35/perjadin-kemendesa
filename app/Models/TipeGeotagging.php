<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model ini dibuat berdasarkan migrasi:
 * 2025_11_08_073101_create_tipegeotagging_table.php
 *
 * Diperlukan agar relasi 'tipeGeotagging()' di model Geotagging.php berfungsi.
 */
class TipeGeotagging extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     */
    protected $table = 'tipegeotagging';

    /**
     * Menunjukkan bahwa model ini TIDAK menggunakan timestamps (created_at, updated_at).
     * Sesuai migrasi.
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'nama_tipe',
    ];

    /**
     * Mendefinisikan relasi one-to-many ke Geotagging.
     */
    public function geotagging()
    {
        return $this->hasMany(Geotagging::class, 'id_tipe', 'id');
    }
}