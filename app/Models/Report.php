<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $table = 'report';
    protected $fillable = ['user_id', 'skema_id', 'jadwal_id', 'pendaftaran_id', 'status'];
    protected $statusReport = [
        1 => 'Kompeten',
        2 => 'Tidak Kompeten'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function getStatusTextAttribute()
    {
        return $this->statusReport[$this->status] ?? 'Tidak Diketahui';
    }
}
