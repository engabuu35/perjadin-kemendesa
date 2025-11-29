<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\StatusPerjadin;
use App\Models\LaporanKeuangan;
use Illuminate\Support\Facades\DB;

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
        'approved_at'    => 'datetime',
        'tanggal_surat'  => 'date',
        'tgl_mulai'      => 'date',
        'tgl_selesai'    => 'date',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    // cache mapping nama_status -> id untuk mengurangi query berulang
    protected static array $statusCache = [];

    protected static function statusId(string $name): ?int
    {
        if (empty(self::$statusCache)) {
            self::$statusCache = DB::table('statusperjadin')->pluck('id', 'nama_status')->toArray();
        }
        return isset(self::$statusCache[$name]) ? intval(self::$statusCache[$name]) : null;
    }

    // relations (pembuat, approver, status, laporanKeuangan, pegawai) tetap sama...
    public function pembuat() { return $this->belongsTo(User::class, 'id_pembuat', 'nip'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by', 'nip'); }
    public function status() { return $this->belongsTo(StatusPerjadin::class, 'id_status', 'id'); }
    public function laporanKeuangan() { return $this->hasOne(LaporanKeuangan::class, 'id_perjadin', 'id'); }
    public function pegawai()
    {
        return $this->belongsToMany(
            User::class,
            'pegawaiperjadin',
            'id_perjadin',
            'id_user',
            'id',
            'nip'
        )->withPivot('role_perjadin', 'is_lead', 'laporan_individu', 'is_finished');
    }

    public function getStatusNameAttribute() 
    { 
        return $this->status->nama_status ?? null; 
    }

    public function getStatusClassAttribute()
    {
        $map = [
            1 => 'bg-blue-500',
            2 => 'bg-yellow-400',
            3 => 'bg-yellow-600',
            4 => 'bg-yellow-700',
            5 => 'bg-green-600',
            6 => 'bg-red-500',
            7 => 'bg-gray-600',
            8 => 'bg-gray-600',
        ];
        return $map[intval($this->id_status)] ?? 'bg-gray-500';
    }

    protected static function booted()
    {
        // capture perubahan tanggal sebelum disimpan
        static::updating(function ($model) {
            $final1 = self::statusId('Diselesaikan Manual');
            $final2 = self::statusId('Dibatalkan');
            if (in_array(intval($model->id_status), array_filter([$final1, $final2]))) {
                return;
            }

            if ($model->isDirty(['tgl_mulai', 'tgl_selesai'])) {
                $belum = self::statusId('Belum Berlangsung');
                if ($belum) {
                    $model->id_status = $belum;
                }
            }
        });

        // setelah tersimpan, evaluasi status lain
        static::saved(function ($model) {
            $model->updateStatus();
        });
    }

    public function updateStatus(): bool
    {
        $belum     = self::statusId('Belum Berlangsung');
        $sedang    = self::statusId('Sedang Berlangsung');
        $pembuatan = self::statusId('Pembuatan Laporan');
        $menunggu  = self::statusId('Menunggu Validasi PPK');
        $selesai   = self::statusId('Selesai');
        $perlu     = self::statusId('Perlu Tindakan');
        $final1    = self::statusId('Diselesaikan Manual');
        $final2    = self::statusId('Dibatalkan');

        if (in_array(intval($this->id_status), array_filter([$final1, $final2]))) {
            return false;
        }

        $now = now()->startOfDay();

        // 1) Belum -> Sedang berdasarkan tanggal
        if ($belum && intval($this->id_status) === $belum && $this->tgl_mulai && $this->tgl_mulai->startOfDay()->lte($now)) {
            if ($sedang) { $this->id_status = $sedang; $this->saveQuietly(); return true; }
        }

        // gunakan relasi pegawai untuk hitung cepat
        $totalPegawai = $this->pegawai()->count();
        $totalSelesai = $this->pegawai()->wherePivot('is_finished', 1)->count();

        // hitung laporan lengkap di PHP untuk portabilitas
        $laporanLengkap = $this->pegawai()
            ->whereNotNull('laporan_individu')
            ->get()
            ->filter(function($p){ return mb_strlen(trim($p->pivot->laporan_individu)) >= 100; })
            ->count();

        if ($sedang && intval($this->id_status) === $sedang) {
            if ($totalPegawai > 0 && ($totalSelesai === $totalPegawai || $laporanLengkap === $totalPegawai)) {
                if ($pembuatan) { $this->id_status = $pembuatan; $this->saveQuietly(); return true; }
            }
        }

        $lapKeu = $this->laporanKeuangan()->first();
        $statusLaporan = DB::table('statuslaporan')->pluck('id', 'nama_status')->toArray();

        if ($pembuatan && intval($this->id_status) === $pembuatan && $lapKeu) {
            if (intval($lapKeu->id_status) === intval($statusLaporan['Menunggu Verifikasi'] ?? -1)) {
                if ($menunggu) { $this->id_status = $menunggu; $this->saveQuietly(); return true; }
            }
        }

        if ($menunggu && intval($this->id_status) === $menunggu && $lapKeu) {
            if (intval($lapKeu->id_status) === intval($statusLaporan['Disetujui'] ?? -1)) {
                if ($selesai) { $this->id_status = $selesai; $this->saveQuietly(); return true; }
            }
            if (intval($lapKeu->id_status) === intval($statusLaporan['Perlu Tindakan'] ?? -1)) {
                if ($perlu) { $this->id_status = $perlu; $this->saveQuietly(); return true; }
            }
        }

        if ($perlu && intval($this->id_status) === $perlu && $lapKeu) {
            if (intval($lapKeu->id_status) === intval($statusLaporan['Menunggu Verifikasi'] ?? -1)) {
                if ($menunggu) { $this->id_status = $menunggu; $this->saveQuietly(); return true; }
            }
            if (intval($lapKeu->id_status) === intval($statusLaporan['Disetujui'] ?? -1)) {
                if ($selesai) { $this->id_status = $selesai; $this->saveQuietly(); return true; }
            }
        }

        return false;
    }

}
