<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPerjadin extends Model
{
    use HasFactory;

    // Tentukan nama tabel secara eksplisit (opsional, tapi disarankan)
    protected $table = 'laporan_perjadin';

    // Kolom yang boleh diisi secara massal (create/update)
    protected $fillable = [
        'id_perjadin',
        'id_user',
        'uraian',
        'is_final'
    ];

    // Cast 'is_final' jadi boolean biar otomatis true/false (bukan 1/0)
    protected $casts = [
        'is_final' => 'boolean',
    ];

    /**
     * Relasi: Satu Laporan memiliki BANYAK Bukti.
     * (One to Many)
     */
    public function bukti()
    {
        return $this->hasMany(BuktiLaporan::class, 'id_laporan', 'id');
    }

    /**
     * Relasi: Laporan ini milik satu Surat Tugas (PerjalananDinas).
     * (Belongs To)
     */
    public function perjadin()
    {
        return $this->belongsTo(PerjalananDinas::class, 'id_perjadin', 'id');
    }

    /**
     * Relasi: Laporan ini milik satu Pegawai (User).
     * (Belongs To)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'nip');
    }
}