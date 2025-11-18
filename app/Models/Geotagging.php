<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geotagging extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     * Sesuai migrasi: 'geotagging'
     */
    protected $table = 'geotagging';

    /**
     * Sesuai migrasi, tabel ini HANYA memiliki 'created_at',
     * tapi TIDAK memiliki 'updated_at'.
     * Kita harus memberitahu Eloquent agar tidak mencoba mengisi 'updated_at'.
     */
    public const UPDATED_AT = null;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'id_perjadin',
        'id_user',
        'id_tipe',
        'latitude',
        'longitude',
    ];

    /**
     * Tipe data casting untuk atribut.
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
    ];

    // -------------------------------------------------------------------
    // DEFINISI RELASI (BELONGS TO)
    // -------------------------------------------------------------------

    /**
     * Mendapatkan data User (pegawai) yang melakukan geotagging.
     *
     * Relasi ini menghubungkan:
     * - Foreign Key: 'geotagging.id_user'
     * - Owner Key: 'users.nip' (Sesuai dengan model User.php Anda)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'nip');
    }

    /**
     * Mendapatkan data PerjalananDinas terkait.
     *
     * Relasi ini menghubungkan:
     * - Foreign Key: 'geotagging.id_perjadin'
     * - Owner Key: 'perjalanandinas.id' (default)
     */
    public function perjalananDinas()
    {
        return $this->belongsTo(PerjalananDinas::class, 'id_perjadin', 'id');
    }

    /**
     * Mendapatkan data TipeGeotagging (mis: Keberangkatan, Kedatangan).
     *
     * Relasi ini menghubungkan:
     * - Foreign Key: 'geotagging.id_tipe'
     * - Owner Key: 'tipegeotagging.id' (default)
     */
    public function tipeGeotagging()
    {
        // Asumsi nama modelnya adalah TipeGeotagging
        return $this->belongsTo(TipeGeotagging::class, 'id_tipe', 'id');
    }
}