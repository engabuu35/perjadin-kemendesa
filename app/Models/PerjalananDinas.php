<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerjalananDinas extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'perjalanandinas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'hasil_perjadin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approved_at' => 'datetime',
        'tanggal_surat' => 'date',
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created the document.
     * Links to 'nip' on 'users' table.
     */
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'id_pembuat', 'nip');
    }

    /**
     * Get the user who approved the document.
     * Links to 'nip' on 'users' table.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'nip');
    }

    /**
     * Get the status of the travel.
     */
    public function status()
    {
        // Asumsi ada model StatusPerjadin
        return $this->belongsTo(StatusPerjadin::class, 'id_status', 'id');
    }

    /**
     * Get the associated financial report.
     */
    public function laporanKeuangan()
    {
        return $this->hasOne(LaporanKeuangan::class, 'id_perjadin', 'id');
    }

    /**
     * Get the employees associated with this travel (Many-to-Many).
     * Links 'perjalanandinas.id' to 'users.nip' via 'pegawaiperjadin'.
     */
    public function pegawai()
    {
        return $this->belongsToMany(
            User::class,
            'pegawaiperjadin', // nama pivot table
            'id_perjadin',     // FK di pivot ke perjalanandinas
            'id_user',         // FK di pivot ke users
            'id',              // local key di perjalanandinas
            'nip'              // related key di users
        )->withPivot('role_perjadin', 'is_lead', 'laporan_individu');
    }
}