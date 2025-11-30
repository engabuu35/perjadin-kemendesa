<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_nip','type','status','perjalanan_id','payload','available_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_nip', 'nip');
    }

    public function perjalanan()
    {
        return $this->belongsTo(PerjalananDinas::class, 'perjalanan_id', 'id');
    }
}
