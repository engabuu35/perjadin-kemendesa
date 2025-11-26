<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\StatusPerjadin;
use App\Models\LaporanKeuangan;

class PerjalananDinas extends Model
{
    use HasFactory;

    protected $table = 'perjalanandinas';

    protected $fillable = [
        'id_pembuat',
        'id_status',
        'approved_by',
        'approved_at',
        'nomor_surat',
        'tanggal_surat',
        'tujuan',
        'tgl_mulai',
        'tgl_selesai',
        'uraian',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'tanggal_surat' => 'date',
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Jika primary key bukan 'id', aktifkan baris ini
    // protected $primaryKey = 'id';

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'id_pembuat', 'nip');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'nip');
    }

    public function status()
    {
        return $this->belongsTo(StatusPerjadin::class, 'id_status', 'id');
    }

    public function laporanKeuangan()
    {
        return $this->hasOne(LaporanKeuangan::class, 'id_perjadin', 'id');
    }

    public function pegawai()
    {
        return $this->belongsToMany(
            User::class,
            'pegawaiperjadin', // pivot table
            'id_perjadin',     // FK on pivot to perjalanandinas
            'id_user',         // FK on pivot to users (we store NIP here)
            'id',              // local key on this model
            'nip'              // related key on users table
        )->withPivot('role_perjadin', 'is_lead', 'laporan_individu');
    }

    /**
     * Helper accessor: ambil nama status (jika relasi ada).
     */
    public function getStatusNameAttribute()
    {
        return $this->status->nama_status ?? null;
    }

    /**
     * Helper accessor: mapping class warna untuk UI berdasarkan id_status.
     */
    public function getStatusClassAttribute()
    {
        $map = [
            1 => 'bg-red-500',    // contoh: menunggu
            2 => 'bg-yellow-500', // contoh: on progress
            3 => 'bg-blue-500',
            4 => 'bg-green-600',  // selesai
        ];

        return $map[intval($this->id_status)] ?? 'bg-gray-500';
    }
}
