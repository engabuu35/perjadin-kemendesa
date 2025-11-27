<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PangkatGolongan extends Model
{
    // Nama tabel sesuai migrasimu
    protected $table = 'pangkatgolongan';

    // Primary key di tabel adalah integer id
    protected $primaryKey = 'id';

    // Jika kamu tidak memakai timestamps pada tabel
    public $timestamps = false;

    // Fillable jika perlu
    protected $fillable = ['kode_golongan', 'nama_pangkat'];

    // Jika ingin relasi ke Users (one-to-many)
    public function users()
    {
        return $this->hasMany(User::class, 'pangkat_gol_id', 'id');
    }
}
