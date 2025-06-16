<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranAsesor extends Model
{
    protected $table = 'pembayaran_asesor';
    protected $fillable = ['asesor_id', 'jadwal_id', 'bukti_pembayaran', 'status'];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function skema()
    {
        return $this->belongsTo(Skema::class, 'skema_id');
    }
}
