<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranAsesor extends Model
{
    protected $table = 'pembayaran_asesor';
    protected $fillable = ['jadwal_id', 'bukti_pembayaran', 'status'];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function skema()
    {
        return $this->belongsTo(Skema::class, 'skema_id');
    }
}
