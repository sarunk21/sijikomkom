<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KelayankanVerifikasi extends Model
{
    use HasFactory;

    protected $table = 'kelayakan_verifikasi';
    protected $fillable = [
        'pendaftaran_id',
        'asesor_id',
        'status',
        'catatan',
        'verified_at'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    protected $statusVerifikasi = [
        1 => 'Layak',
        2 => 'Tidak Layak',
    ];

    public function getStatusTextAttribute()
    {
        return $this->statusVerifikasi[$this->status] ?? 'Tidak Diketahui';
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'pendaftaran_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }
}

