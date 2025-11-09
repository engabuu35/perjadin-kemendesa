<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = false; // dump-mu tidak punya timestamps
    protected $fillable = ['kode','nama'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'penugasanperan',
            'role_id',
            'user_id'
        );
    }
}
