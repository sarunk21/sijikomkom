<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skema extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'skema';
    protected $fillable = ['nama', 'kode', 'kategori', 'bidang'];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}
