<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class TripMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'user_id',
        'jabatan_saat_perdin',
        'is_lead',
    ];

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
