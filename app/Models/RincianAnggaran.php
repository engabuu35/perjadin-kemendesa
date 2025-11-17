<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RincianAnggaran extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rinciananggaran';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // Based on migration, no created_at/updated_at

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_laporan',
        'id_kategori',
        'tanggal_biaya',
        'deskripsi_biaya',
        'jumlah_biaya',
        'path_bukti',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_biaya' => 'date',
        'jumlah_biaya' => 'decimal:2',
    ];

    /**
     * Get the parent LaporanKeuangan.
     */
    public function laporanKeuangan()
    {
        return $this->belongsTo(LaporanKeuangan::class, 'id_laporan', 'id');
    }

    /**
     * Get the cost category.
     */
    public function kategori()
    {
        // Asumsi ada model KategoriBiaya
        return $this->belongsTo(KategoriBiaya::class, 'id_kategori', 'id');
    }
}