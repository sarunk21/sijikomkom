<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendaftaranUjikom extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_ujikom';
    protected $fillable = ['pendaftar_id', 'jadwal_id', 'asesi_id', 'asesor_id'];

    protected $statusUjikom = [
        1 => 'Belum Ujikom',
        2 => 'Ujikom Berlangsung',
        3 => 'Ujikom Selesai',
        4 => 'Tidak Kompeten',
        5 => 'Kompeten',
    ];

    public function getStatusTextAttribute()
    {
        return $this->statusUjikom[$this->status] ?? 'Tidak Diketahui';
    }

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
