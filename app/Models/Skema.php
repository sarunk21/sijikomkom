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

    public function apl2()
    {
        return $this->hasMany(APL2::class);
    }

    // Relasi many-to-many dengan User (asesor)
    public function asesors()
    {
        return $this->belongsToMany(User::class, 'asesor_skema', 'skema_id', 'asesor_id');
    }

    // Relasi untuk asesor_skema
    public function asesorSkemas()
    {
        return $this->hasMany(AsesorSkema::class, 'skema_id');
    }
}
