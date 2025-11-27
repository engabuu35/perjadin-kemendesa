<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = false;
    protected $fillable = ['kode','nama'];

    /**
     * Mendefinisikan relasi many-to-many ke User.
     *
     * PERBAIKAN:
     * Karena model User menggunakan 'nip' sebagai Primary Key,
     * kita harus mendefinisikan 'local key' (id) dan 'related key' (nip)
     * secara eksplisit pada relasi ini agar Eloquent dapat menghubungkannya.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'penugasanperan', // tabel pivot
            'role_id',        // foreign key di pivot yg merujuk ke Role
            'user_id',        // foreign key di pivot yg merujuk ke User
            'id',             // local key di model INI (Role)
            'nip'             // related key di model User (sesuai $primaryKey di User.php)
        );
    }
}