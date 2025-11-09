<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Pastikan memakai nama tabel 'users'
    protected $table = 'users';

    // jika primary key bukan 'id' ganti di sini, tapi dump-mu pakai id
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_uke',
        'pangkat_gol_id',
        'nip',
        'nama',
        'email',
        'no_telp',
        'password_hash',
        'is_aktif',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Inform Laravel which attribute contains the hashed password.
     * Laravel's Auth::attempt() will use this value for password verification.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Relasi many-to-many ke roles lewat tabel penugasanperan
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'penugasanperan', // pivot table
            'user_id',        // FK on pivot to this model
            'role_id'         // FK on pivot to Role model
        );
    }

    /**
     * Cek apakah user punya role tertentu berdasarkan kolom 'kode'
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('kode', $role)->exists();
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

        $roles = $this->roles()->pluck('kode')->map(fn($k) => strtoupper($k))->toArray();

        foreach ($priority as $p) {
            if (in_array($p, $roles)) {
                return $p;
            }
        }

        return $roles[0] ?? null;
    }

    public function createdTrips()
    {
        return $this->hasMany(Trip::class, 'creator_id');
    }

    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'trip_members');
    }
}
