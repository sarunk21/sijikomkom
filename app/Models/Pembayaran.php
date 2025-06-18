<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $fillable = ['jadwal_id', 'user_id', 'bukti_pembayaran', 'status'];

    protected $statusPembayaran = [
        1 => 'Belum Bayar',
        2 => 'Menunggu Verifikasi',
        3 => 'Tidak Lolos Verifikasi',
        4 => 'Dikonfirmasi',
    ];

    public function getStatusTextAttribute()
    {
        return $this->statusPembayaran[$this->status] ?? 'Tidak Diketahui';
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
