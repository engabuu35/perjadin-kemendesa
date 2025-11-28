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
            'pegawaiperjadin',
            'id_perjadin',
            'id_user',
            'id',
            'nip'
        )->withPivot('role_perjadin', 'is_lead', 'laporan_individu');
    }

    public function getStatusNameAttribute()
    {
        return $this->status->nama_status ?? null;
    }

    public function getStatusClassAttribute()
    {
        $map = [
            1 => 'bg-blue-500',
            2 => 'bg-yellow-500',
            3 => 'bg-yellow-600',
            4 => 'bg-green-600',
        ];

        return $map[intval($this->id_status)] ?? 'bg-gray-500';
    }

    /**
     * - Status 7 & 8 tidak boleh berubah.
     * - Jika tanggal diubah → status = 1.
     * - Sisanya mengikuti flow laporan & perjalanan.
     */
    public function updateStatus(): bool
    {
        $statusPerjadin = DB::table('statusperjadin')->pluck('id', 'nama_status')->toArray();
        $statusLaporan  = DB::table('statuslaporan')->pluck('id', 'nama_status')->toArray();
        $now = now()->startOfDay();

        // FINAL STATES — tidak boleh berubah
        if (in_array($this->id_status, [7, 8])) {
            return false;
        }

        // RESET KE STATUS 1 JIKA TANGGAL DIUBAH
        if ($this->isDirty(['tgl_mulai', 'tgl_selesai'])) {
            if (isset($statusPerjadin['Belum Berlangsung'])) {
                $this->id_status = $statusPerjadin['Belum Berlangsung'];
                $this->save();
                return true;
            }
        }

        $exists = fn($arr, $key) => array_key_exists($key, $arr);

        /* ============================================================
         * 1. Belum Berlangsung → Sedang Berlangsung
         * ============================================================ */
        if ($exists($statusPerjadin, 'Belum Berlangsung')
            && intval($this->id_status) === intval($statusPerjadin['Belum Berlangsung'])
            && $this->tgl_mulai
            && $this->tgl_mulai->startOfDay()->lte($now)
        ) {
            if ($exists($statusPerjadin, 'Sedang Berlangsung')) {
                $this->id_status = $statusPerjadin['Sedang Berlangsung'];
                $this->save();
                return true;
            }
        }

        /* ============================================================
         * DATA PEGAWAI & LAPORAN INDIVIDU
         * ============================================================ */
        $pegawai = DB::table('pegawaiperjadin')->where('id_perjadin', $this->id)->get();
        $totalPegawai = $pegawai->count();
        $totalSelesai = $pegawai->where('is_finished', 1)->count();

        $laporanLengkap = DB::table('pegawaiperjadin')
            ->where('id_perjadin', $this->id)
            ->whereNotNull('laporan_individu')
            ->whereRaw("CHAR_LENGTH(TRIM(laporan_individu)) >= 100")
            ->count();

        /* ============================================================
         * 2. Sedang Berlangsung → Pembuatan Laporan
         * ============================================================ */
        if ($exists($statusPerjadin, 'Sedang Berlangsung')
            && intval($this->id_status) === intval($statusPerjadin['Sedang Berlangsung'])
        ) {
            // Semua pegawai selesai
            if ($totalPegawai > 0 && $totalSelesai === $totalPegawai) {
                $this->id_status = $statusPerjadin['Pembuatan Laporan'];
                $this->save();
                return true;
            }

            // Semua uraian sudah lengkap min 100 karakter
            if ($totalPegawai > 0 && $laporanLengkap === $totalPegawai) {
                $this->id_status = $statusPerjadin['Pembuatan Laporan'];
                $this->save();
                return true;
            }
        }

        /* ============================================================
         * AMBIL LAPORAN KEUANGAN
         * ============================================================ */
        $lapKeu = DB::table('laporankeuangan')->where('id_perjadin', $this->id)->first();

        /* ============================================================
         * 3. Pembuatan Laporan → Menunggu Validasi PPK
         * ============================================================ */
        if ($exists($statusPerjadin, 'Pembuatan Laporan')
            && intval($this->id_status) === intval($statusPerjadin['Pembuatan Laporan'])
            && $lapKeu
            && intval($lapKeu->id_status) === intval($statusLaporan['Menunggu Verifikasi'] ?? -1)
        ) {
            if ($exists($statusPerjadin, 'Menunggu Validasi PPK')) {
                $this->id_status = $statusPerjadin['Menunggu Validasi PPK'];
                $this->save();
                return true;
            }
        }

        /* ============================================================
         * 4. Menunggu Validasi PPK → Selesai / Perlu Tindakan
         * ============================================================ */
        if ($exists($statusPerjadin, 'Menunggu Validasi PPK')
            && intval($this->id_status) === intval($statusPerjadin['Menunggu Validasi PPK'])
            && $lapKeu
        ) {
            if (intval($lapKeu->id_status) === intval($statusLaporan['Disetujui'] ?? -1)) {
                $this->id_status = $statusPerjadin['Selesai'] ?? $this->id_status;
                $this->save();
                return true;
            }

            if (intval($lapKeu->id_status) === intval($statusLaporan['Perlu Tindakan'] ?? -1)) {
                $this->id_status = $statusPerjadin['Perlu Tindakan'] ?? $this->id_status;
                $this->save();
                return true;
            }
        }

        /* ============================================================
         * 5. Perlu Tindakan → Menunggu Validasi / Selesai
         * ============================================================ */
        if ($exists($statusPerjadin, 'Perlu Tindakan')
            && intval($this->id_status) === intval($statusPerjadin['Perlu Tindakan'])
            && $lapKeu
        ) {
            if (intval($lapKeu->id_status) === intval($statusLaporan['Menunggu Verifikasi'] ?? -1)) {
                $this->id_status = $statusPerjadin['Menunggu Validasi PPK'];
                $this->save();
                return true;
            }

            if (intval($lapKeu->id_status) === intval($statusLaporan['Disetujui'] ?? -1)) {
                $this->id_status = $statusPerjadin['Selesai'];
                $this->save();
                return true;
            }
        }

        return false;
    }
}
