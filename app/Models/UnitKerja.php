<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitKerja extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     * Sesuai migrasi: 'unitkerja'
     */
    protected $table = 'unitkerja';

    /**
     * Menunjukkan bahwa model ini TIDAK menggunakan timestamps (created_at, updated_at).
     * Sesuai migrasi: '2025_11_08_073101_create_unitkerja_table.php'
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'id_induk',
        'kode_uke',
        'nama_uke',
    ];

    /**
     * Mendefinisikan relasi one-to-many ke User.
     * Satu Unit Kerja bisa memiliki banyak User.
     * Ini adalah kebalikan dari relasi di User.php
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_uke', 'id');
    }

    /**
     * Mendefinisikan relasi self-referencing untuk unit kerja induk (parent).
     * Sesuai migrasi: 'add_foreign_keys_to_unitkerja_table.php'
     */
    public function induk()
    {
        return $this->belongsTo(UnitKerja::class, 'id_induk', 'id');
    }

    /**
     * Mendefinisikan relasi self-referencing untuk unit kerja anak (children).
     */
    public function anak()
    {
        return $this->hasMany(UnitKerja::class, 'id_induk', 'id');
    }

    /**
     * Mendefinisikan relasi one-to-many ke LingkupAudit.
     * Sesuai migrasi: 'create_lingkupaudit_table.php'
     */
    public function lingkupAudit()
    {
        // Asumsi nama modelnya adalah LingkupAudit
        return $this->hasMany(LingkupAudit::class, 'unit_kerja_id', 'id');
    }
}