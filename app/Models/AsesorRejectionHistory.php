<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AsesorRejectionHistory extends Model
{
    use HasFactory;

    protected $table = 'asesor_rejection_history';

    protected $fillable = [
        'pendaftaran_id',
        'jadwal_id',
        'asesor_id',
        'notes'
    ];

    // Relasi
    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }
}
