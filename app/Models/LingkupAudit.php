<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model ini dibuat berdasarkan migrasi:
 * 2025_11_08_073101_create_lingkupaudit_table.php
 *
 * Diperlukan agar relasi 'lingkupAudit()' di model UnitKerja.php berfungsi.
 */
class LingkupAudit extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     */
    protected $table = 'lingkupaudit';

    /**
     * Menunjukkan bahwa model ini TIDAK menggunakan timestamps (created_at, updated_at).
     * Sesuai migrasi.
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'unit_kerja_id',
        'nama_auditi',
    ];

    /**
     * Mendefinisikan relasi one-to-many ke UnitKerja.
     */
    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_kerja_id', 'id');
    }
}