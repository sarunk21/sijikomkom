<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jadwal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jadwal';
    protected $fillable = ['skema_id', 'tuk_id', 'tanggal_ujian', 'tanggal_selesai', 'tanggal_maksimal_pendaftaran', 'status', 'kuota', 'keterangan'];

    public $statusJadwal = [
        1 => 'Aktif',
        2 => 'Tidak Aktif',
        3 => 'Ujian Berlangsung',
        4 => 'Selesai',
        5 => 'Menunggu Konfirmasi Kepala TUK',
        6 => 'TUK Tidak Dapat Digunakan'
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

    public function jumlah_asesi()
    {
        return $this->hasMany(Pendaftaran::class, 'jadwal_id', 'id')
            ->where('status', 4)
            ->orWhere('status', 5)
            ->orWhere('status', 6);
    }

    public function jumlah_kompeten()
    {
        return $this->hasMany(Report::class, 'jadwal_id', 'id')->where('status', 1);
    }

    public function jumlah_tidak_kompeten()
    {
        return $this->hasMany(Report::class, 'jadwal_id', 'id')->where('status', 2);
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'jadwal_id', 'id');
    }
}
