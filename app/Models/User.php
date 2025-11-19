<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\PenugasanPeran;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Nama tabel yang digunakan oleh model.
     * Sesuai migrasi: 'users'
     */
    protected $table = 'users';

    /**
     * Primary Key tabel.
     * Sesuai migrasi: 'nip'
     */
    protected $primaryKey = 'nip';

    /**
     * Tipe data dari primary key.
     * Sesuai migrasi: 'string'
     */
    protected $keyType = 'string';

    /**
     * Menunjukkan bahwa primary key BUKAN auto-incrementing.
     * Sesuai migrasi: 'nip' bukan auto-increment
     */
    public $incrementing = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * Disesuaikan dengan migrasi 'create_users_table'.
     */
    protected $fillable = [
        'id_uke',
        'pangkat_gol_id',
        'nip',
        'nama',
        'email',
        'no_telp',
        'password_hash', // Sesuai migrasi
        'is_aktif',
    ];

    /**
     * Atribut yang harus disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Tipe data casting untuk atribut.
     */
    protected $casts = [
        'is_aktif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Memberitahu Laravel kolom mana yang berisi password ter-hash.
     * Auth::attempt() akan menggunakan ini untuk verifikasi.
     * Sesuai migrasi: 'password_hash'
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // -------------------------------------------------------------------
    // RELASI BERDASARKAN SKEMA DATABASE (MIGRASI)
    // -------------------------------------------------------------------

    /**
     * Relasi many-to-many ke 'roles' via tabel 'penugasanperan'.
     *
     * Sesuai migrasi:
     * - Tabel pivot: 'penugasanperan'
     * - Foreign key di pivot (ke User): 'user_id'
     * - Foreign key di pivot (ke Role): 'role_id'
     * - Local key di model ini (User): 'nip' (karena ini PK kita)
     * - Related key di model Role: 'id'
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'penugasanperan', // tabel pivot
            'user_id',        // foreign key di pivot yg merujuk ke User
            'role_id',        // foreign key di pivot yg merujuk ke Role
            'nip',            // local key di model INI (User)
            'id'              // related key di model Role
        );
    }

    /**
     * Relasi one-to-many ke UnitKerja (via 'id_uke').
     */
    public function unitKerja()
    {
        // Asumsi model UnitKerja ada
        return $this->belongsTo(UnitKerja::class, 'id_uke', 'id');
    }

    /**
     * Relasi one-to-many ke PangkatGolongan (via 'pangkat_gol_id').
     */
    public function pangkatGolongan()
    {
        // Asumsi model PangkatGolongan ada
        return $this->belongsTo(PangkatGolongan::class, 'pangkat_gol_id', 'id');
    }

    /**
     * Relasi PerjalananDinas yang DIBUAT oleh user ini.
     * Sesuai migrasi: perjalanandinas.id_pembuat -> users.nip
     */
    public function perjalananDinasDibuat()
    {
        return $this->hasMany(PerjalananDinas::class, 'id_pembuat', 'nip');
    }

    /**
     * Relasi PerjalananDinas yang DISETUJUI oleh user ini.
     * Sesuai migrasi: perjalanandinas.approved_by -> users.nip
     */
    public function perjalananDinasDisetujui()
    {
        return $this->hasMany(PerjalananDinas::class, 'approved_by', 'nip');
    }

    /**
     * Relasi LaporanKeuangan yang DIVERIFIKASI oleh user ini.
     * Sesuai migrasi: laporankeuangan.verified_by -> users.nip
     */
    public function laporanKeuanganDiverifikasi()
    {
        return $this->hasMany(LaporanKeuangan::class, 'verified_by', 'nip');
    }

    /**
     * Relasi Geotagging yang dibuat oleh user ini.
     * Sesuai migrasi: geotagging.id_user -> users.nip
     */
    public function geotagging()
    {
        // Asumsi model Geotagging ada
        return $this->hasMany(Geotagging::class, 'id_user', 'nip');
    }

    /**
     * Relasi many-to-many ke PerjalananDinas (sebagai pegawai/peserta).
     * Sesuai migrasi: 'pegawaiperjadin'
     */
    public function perjalananDinas()
    {
        return $this->belongsToMany(
            PerjalananDinas::class,
            'pegawaiperjadin', // tabel pivot
            'id_user',         // foreign key di pivot yg merujuk ke User
            'id_perjadin',     // foreign key di pivot yg merujuk ke Perjadin
            'nip',             // local key di model INI (User)
            'id'               // related key di model PerjalananDinas
        )->withPivot('role_perjadin', 'is_lead', 'laporan_individu');
    }


    // -------------------------------------------------------------------
    // LOGIKA BISNIS (BAWAAN DARI FILE ANDA)
    // -------------------------------------------------------------------

    /**
     * Cek apakah user punya role tertentu berdasarkan kolom 'kode'
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('kode', $role)->exists();
    }

    /**
     * Cek apakah user punya salah satu dari role yang diberikan
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('kode', $roles)->exists();
    }
    
    /**
     * Apakah user boleh mengakses fitur/route yang butuh role $requiredRole?
     *
     * Aturan hierarki:
     * - PIC => superuser (boleh akses semua)
     * - PIMPINAN atau PPK => juga boleh akses PEGAWAI
     * - PEGAWAI => hanya akses PEGAWAI
     */
    public function canAccessRole(string $requiredRole): bool
    {
        // Superuser
        if ($this->hasRole('PIC')) {
            return true;
        }

        // Jika yang diminta adalah PEGAWAI, maka PIMPINAN & PPK juga boleh
        if ($requiredRole === 'PEGAWAI') {
            return $this->hasAnyRole(['PEGAWAI', 'PIMPINAN', 'PPK']);
        }

        // Untuk role selain PEGAWAI, cek langsung kepemilikan role
        return $this->hasRole($requiredRole);
    }

    /**
     * Ambil kode role utama (berdasarkan prioritas sederhana)
     * Prioritas: PIC > PIMPINAN > PPK > PEGAWAI
     */
    public function primaryRoleCode(): ?string
    {
        $priority = ['PIC', 'PIMPINAN', 'PPK', 'PEGAWAI'];

        // Ambil semua kode role, ubah ke uppercase untuk konsistensi
        $roles = $this->roles()->pluck('kode')->map(fn($k) => strtoupper($k))->toArray();

        foreach ($priority as $p) {
            if (in_array($p, $roles)) {
                return $p;
            }
        }

        return $roles[0] ?? null;
    }
    
    public function penugasanPeran()
    {
        // nip di users sesuai user_id di penugasanperan
        return $this->hasOne(PenugasanPeran::class, 'user_id', 'nip');
    }

    // Accessor untuk kode role
    public function getRoleKodeAttribute()
    {
        return $this->penugasanPeran && $this->penugasanPeran->role
            ? $this->penugasanPeran->role->kode
            : null;
    }

}