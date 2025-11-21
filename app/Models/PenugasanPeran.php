<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenugasanPeran extends Model
{
    protected $table = 'penugasanperan';
    protected $fillable = ['user_id', 'role_id'];
    public $timestamps = false;

    // Relasi ke Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id'); 
    }
}
