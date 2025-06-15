<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jadwal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jadwal';
    protected $fillable = ['skema_id', 'tuk_id', 'tanggal_ujian', 'status', 'kuota'];

    public $statusJadwal = [
        1 => 'Aktif',
        2 => 'Tidak Aktif',
        3 => 'Selesai',
    ];

    public function getStatusTextAttribute()
    {
        return $this->statusJadwal[$this->status] ?? 'Tidak Diketahui';
    }

    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }

    public function tuk()
    {
        return $this->belongsTo(Tuk::class);
    }
}
