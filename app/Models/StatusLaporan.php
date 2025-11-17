<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusLaporan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     * Sesuai migrasi: 'statuslaporan'
     */
    protected $table = 'statuslaporan';

    /**
     * Menunjukkan bahwa model ini TIDAK menggunakan timestamps (created_at, updated_at).
     * Sesuai migrasi: '2025_11_08_073101_create_statuslaporan_table.php'
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'nama_status',
    ];

    /**
     * Mendefinisikan relasi one-to-many ke LaporanKeuangan.
     * Satu status bisa dimiliki oleh banyak laporan keuangan.
     */
    public function laporanKeuangan()
    {
        return $this->hasMany(LaporanKeuangan::class, 'id_status', 'id');
    }
}