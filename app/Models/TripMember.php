<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripMember extends Model
{
    /**
     * Setiap entri TripMember milik satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Setiap entri TripMember milik satu Trip.
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
