<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * Sebuah Role bisa dimiliki oleh banyak User.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
