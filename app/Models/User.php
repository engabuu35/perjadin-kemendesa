<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <-- 1. PASTIKAN BARIS INI BENAR

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // <-- 2. PERBAIKI TYPO DI SINI

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // ... (Fungsi relasi yang sudah kita buat sebelumnya ada di sini) ...
    public function roles()
    {
        return $this->belongsToMany(Role::class);
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