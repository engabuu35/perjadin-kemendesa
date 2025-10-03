<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    /**
     * Sebuah Trip dibuat oleh satu User.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Sebuah Trip memiliki banyak anggota (peserta).
     */
    public function members()
    {
        return $this->hasMany(TripMember::class);
    }

    /**
     * Sebuah Trip memiliki banyak User sebagai anggota melalui tabel trip_members.
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'trip_members');
    }
}
