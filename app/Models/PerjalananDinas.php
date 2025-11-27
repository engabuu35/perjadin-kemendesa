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
            1 => 'bg-blue-500',    
            2 => 'bg-yellow-500',
            3 => 'bg-yellow-600',
            4 => 'bg-green-600',  // selesai
        ];

        return $map[intval($this->id_status)] ?? 'bg-gray-500';
    }

    public function updateStatus(): bool
    {
        // Ambil mapping nama -> id status
        $statusPerjadin = DB::table('statusperjadin')->pluck('id', 'nama_status')->toArray();
        $statusLaporan = DB::table('statuslaporan')->pluck('id', 'nama_status')->toArray();

        $changed = false;
        $now = now()->startOfDay();

        // helper existence checks (cek hanya keberadaan key dalam array)
        $has = fn($arr, $key) => array_key_exists($key, $arr);

        // 1) Belum Berlangsung -> Sedang Berlangsung (jika tgl_mulai <= now)
        if ($has($statusPerjadin, 'Belum Berlangsung')
            && intval($this->id_status) === intval($statusPerjadin['Belum Berlangsung'])
            && $this->tgl_mulai !== null
            && $this->tgl_mulai->startOfDay()->lte($now)
        ) {
            if ($has($statusPerjadin, 'Sedang Berlangsung')) {
                $this->id_status = $statusPerjadin['Sedang Berlangsung'];
                $this->save();
                return true;
            }
        }

        // Ambil list pegawai, total dan jumlah yang sudah finish
        $pegawaiNips = DB::table('pegawaiperjadin')->where('id_perjadin', $this->id)->pluck('id_user')->toArray();
        $totalPegawai = count($pegawaiNips);
        $totalSelesai = $totalPegawai ? DB::table('pegawaiperjadin')
            ->where('id_perjadin', $this->id)
            ->where('is_finished', 1)->count() : 0;

        // 2) Sedang Berlangsung -> Pembuatan Laporan
        // Kondisi yang kamu inginkan:
        //  - semua pegawai is_finished = 1
        //  OR
        //  - semua pegawai punya laporan_individu >= 100 chars
        //  OR
        //  - perjalanandinas.uraian >= 100 chars
        if ($has($statusPerjadin, 'Sedang Berlangsung')
            && intval($this->id_status) === intval($statusPerjadin['Sedang Berlangsung'])
        ) {
            $allFinished = ($totalPegawai > 0) && ($totalSelesai >= $totalPegawai);

            $completedReportsCount = DB::table('pegawaiperjadin')
                ->where('id_perjadin', $this->id)
                ->whereNotNull('laporan_individu')
                ->whereRaw('CHAR_LENGTH(TRIM(laporan_individu)) >= ?', [100])
                ->count();

            if ($totalPegawai > 0 && $completedReportsCount === $totalPegawai) {
                // semua sudah isi uraian individu
                $this->id_status = $statusPerjadin['Pembuatan Laporan'];
                $this->save();
                return true;
            }
        }

        // Ambil laporan keuangan jika ada
        $laporanKeu = DB::table('laporankeuangan')->where('id_perjadin', $this->id)->first();

        // 3) Pembuatan Laporan -> Menunggu Validasi PPK
        if ($has($statusPerjadin, 'Pembuatan Laporan')
            && intval($this->id_status) === intval($statusPerjadin['Pembuatan Laporan'])
            && $laporanKeu
            && $has($statusLaporan, 'Menunggu Verifikasi')
            && intval($laporanKeu->id_status) === intval($statusLaporan['Menunggu Verifikasi'])
        ) {
            if ($has($statusPerjadin, 'Menunggu Validasi PPK')) {
                $this->id_status = $statusPerjadin['Menunggu Validasi PPK'];
                $this->save();
                return true;
            }
        }

        // 4) Menunggu Validasi PPK -> Selesai / Perlu Tindakan
        if ($has($statusPerjadin, 'Menunggu Validasi PPK')
            && intval($this->id_status) === intval($statusPerjadin['Menunggu Validasi PPK'])
            && $laporanKeu
            && $has($statusLaporan, 'Disetujui')
            && $has($statusLaporan, 'Perlu Tindakan')
        ) {
            if (intval($laporanKeu->id_status) === intval($statusLaporan['Disetujui'])) {
                $this->id_status = $statusPerjadin['Selesai'] ?? $this->id_status;
                $this->save();
                return true;
            }

            if (intval($laporanKeu->id_status) === intval($statusLaporan['Perlu Tindakan'])) {
                $this->id_status = $statusPerjadin['Perlu Tindakan'] ?? $this->id_status;
                $this->save();
                return true;
            }
        }

        // 5) Dari Perlu Tindakan kembali -> Menunggu Validasi PPK / Selesai
        if ($has($statusPerjadin, 'Perlu Tindakan')
            && intval($this->id_status) === intval($statusPerjadin['Perlu Tindakan'])
            && $laporanKeu
            && $has($statusLaporan, 'Menunggu Verifikasi')
            && $has($statusLaporan, 'Disetujui')
        ) {
            if (intval($laporanKeu->id_status) === intval($statusLaporan['Menunggu Verifikasi'])) {
                $this->id_status = $statusPerjadin['Menunggu Validasi PPK'];
                $this->save();
                return true;
            }
            if (intval($laporanKeu->id_status) === intval($statusLaporan['Disetujui'])) {
                $this->id_status = $statusPerjadin['Selesai'];
                $this->save();
                return true;
            }
        }

        return $changed;
    }


}
