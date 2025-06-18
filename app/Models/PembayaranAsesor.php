<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PembayaranAsesor extends Model
{
    protected $table = 'pembayaran_asesor';
    protected $fillable = ['asesor_id', 'jadwal_id', 'bukti_pembayaran', 'status'];

    public $statusPembayaran = [
        1 => 'Menunggu Pembayaran',
        2 => 'Menunggu Verifikasi',
        3 => 'Selesai',
    ];

    public function getStatusTextAttribute()
    {
        return $this->statusPembayaran[$this->status] ?? 'Tidak Diketahui';
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
