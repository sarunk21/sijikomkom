<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendaftaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pendaftaran';
    protected $fillable = [
        'jadwal_id', 'user_id', 'skema_id', 'tuk_id', 'status', 'keterangan', 
        'custom_variables', 'ttd_asesi_path', 'ttd_asesor_path', 'asesor_assessment', 
        'asesor_data', 'kelayakan_status', 'kelayakan_catatan', 'kelayakan_verified_at', 
        'kelayakan_verified_by'
    ];

    protected $casts = [
        'custom_variables' => 'array',
        'asesor_assessment' => 'array',
        'asesor_data' => 'array',
        'kelayakan_verified_at' => 'datetime',
    ];

    protected $statusPendaftaran = [
        1 => 'Menunggu Distribusi Asesor',
        2 => 'Tidak Lolos Verifikasi Dokumen',
        5 => 'Menunggu Verifikasi Dokumen',
        6 => 'Menunggu Verifikasi Kelayakan',
        7 => 'Tidak Lolos Kelayakan',
        8 => 'Menunggu Pembayaran',
        9 => 'Menunggu Ujian',
        10 => 'Ujian Berlangsung',
        11 => 'Selesai',
        12 => 'Asesor Tidak Dapat Hadir',
    ];

    protected $kelayankanStatus = [
        0 => 'Belum Diperiksa',
        1 => 'Layak',
        2 => 'Tidak Layak',
    ];

    public function getStatusTextAttribute()
    {
        return $this->statusPendaftaran[$this->status] ?? 'Tidak Diketahui';
    }

    public function getKelayankanStatusTextAttribute()
    {
        return $this->kelayankanStatus[$this->kelayakan_status] ?? 'Belum Diperiksa';
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }

    public function tuk()
    {
        return $this->belongsTo(Tuk::class);
    }

    public function pendaftaranUjikom()
    {
        return $this->hasOne(PendaftaranUjikom::class, 'pendaftaran_id');
    }

    public function report()
    {
        return $this->hasOne(Report::class, 'pendaftaran_id');
    }

    public function kelayankanVerifikasi()
    {
        return $this->hasMany(KelayankanVerifikasi::class, 'pendaftaran_id');
    }

    public function kelayankanVerifiedBy()
    {
        return $this->belongsTo(User::class, 'kelayakan_verified_by');
    }
}
