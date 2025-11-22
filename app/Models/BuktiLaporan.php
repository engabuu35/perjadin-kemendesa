<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiLaporan extends Model
{
    use HasFactory;

    protected $table = 'bukti_laporan';

    protected $fillable = [
        'id_laporan',
        'nama_file',
        'path_file',
        'kategori'
    ];

    /**
     * Relasi: Bukti ini adalah milik dari satu Laporan.
     * (Inverse of One to Many)
     */
    public function laporan()
    {
        return $this->belongsTo(LaporanPerjadin::class, 'id_laporan', 'id');
    }
}