<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tuk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tuk';
    protected $fillable = ['nama', 'kode', 'kategori', 'alamat'];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
