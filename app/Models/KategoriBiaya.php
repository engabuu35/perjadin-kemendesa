<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBiaya extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     * Sesuai migrasi: 'kategoribiaya'
     */
    protected $table = 'kategoribiaya';

    /**
     * Menunjukkan bahwa model ini TIDAK menggunakan timestamps (created_at, updated_at).
     * Sesuai migrasi: '2025_11_08_073101_create_kategoribiaya_table.php'
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'nama_kategori',
    ];

    /**
     * Mendefinisikan relasi one-to-many ke RincianAnggaran.
     * Satu kategori bisa dimiliki oleh banyak rincian.
     */
    public function rincianAnggaran()
    {
        return $this->hasMany(RincianAnggaran::class, 'id_kategori', 'id');
    }
}