<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendaftaranUjikom extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pendaftaran_ujikom';
    protected $fillable = ['pendaftar_id', 'jadwal_id', 'asesi_id', 'asesor_id'];

    public function pendaftar()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function asesi()
    {
        return $this->belongsTo(User::class);
    }

    public function asesor()
    {
        return $this->belongsTo(User::class);
    }
}
