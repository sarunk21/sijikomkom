<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sertif extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sertif';
    protected $fillable = ['user_id', 'skema_id', 'pendaftaran_id', 'sertifikat', 'status'];
    protected $statusSertif = [
        1 => 'Belum Terverifikasi',
        2 => 'Terverifikasi',
        3 => 'Tidak Terverifikasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function getStatusTextAttribute()
    {
        return $this->statusSertif[$this->status] ?? 'Tidak Diketahui';
    }
}
