<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKeuangan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'laporankeuangan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_perjadin',
        'id_status',
        'verified_by',
        'verified_at',
        'nomor_spm',
        'tanggal_spm',
        'nomor_sp2d',
        'tanggal_sp2d',
        'biaya_rampung',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'tanggal_spm' => 'date',
        'tanggal_sp2d' => 'date',
        'biaya_rampung' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the related PerjalananDinas.
     */
    public function perjalananDinas()
    {
        return $this->belongsTo(PerjalananDinas::class, 'id_perjadin', 'id');
    }

    public function status()
    {
        return $this->belongsTo(StatusLaporan::class, 'id_status', 'id');
    }

    /**
     * Get the user who verified the report.
     * Note the foreign key 'verified_by' links to 'nip' on 'users' table.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'nip');
    }

    /**
     * Get all rincian anggaran for the report.
     */
    public function rincianAnggaran()
    {
        return $this->hasMany(RincianAnggaran::class, 'id_laporan', 'id');
    }
}