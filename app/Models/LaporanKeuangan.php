<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKeuangan extends Model
{
    use HasFactory;

    protected $table = 'laporan_keuangan';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'nama_pegawai',
        'nip',
        'uang_harian',
        'biaya_penginapan',
        'transport', // Tambahkan ini
        'nama_hotel',  // Tambahkan ini
    ];
}

