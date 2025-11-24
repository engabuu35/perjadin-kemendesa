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
        'kategori',
        'nominal', // <--- TAMBAHKAN INI AGAR BISA DISIMPAN
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanPerjadin::class, 'id_laporan', 'id');
    }
}