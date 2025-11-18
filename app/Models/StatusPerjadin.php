<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPerjadin extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     * Sesuai migrasi: 'statusperjadin'
     */
    protected $table = 'statusperjadin';

    /**
     * Menunjukkan bahwa model ini TIDAK menggunakan timestamps (created_at, updated_at).
     * Sesuai migrasi: '2025_11_08_073101_create_statusperjadin_table.php'
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'nama_status',
    ];

    /**
     * Mendefinisikan relasi one-to-many ke PerjalananDinas.
     * Satu status bisa dimiliki oleh banyak perjalanan dinas.
     */
    public function perjalananDinas()
    {
        return $this->hasMany(PerjalananDinas::class, 'id_status', 'id');
    }
}