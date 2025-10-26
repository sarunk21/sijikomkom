<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendaftaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pendaftaran';
    protected $fillable = ['jadwal_id', 'user_id', 'skema_id', 'tuk_id', 'status', 'keterangan', 'custom_variables', 'ttd_asesi_path', 'asesor_assessment'];

    protected $casts = [
        'custom_variables' => 'array',
        'asesor_assessment' => 'array',
    ];

    protected $statusPendaftaran = [
        1 => 'Menunggu Verifikasi Kaprodi',
        2 => 'Tidak Lolos Verifikasi Kaprodi',
        3 => 'Menunggu Verifikasi Admin',
        4 => 'Menunggu Ujian',
        5 => 'Ujian Berlangsung',
        6 => 'Selesai',
        7 => 'Asesor Tidak Dapat Hadir',
    ];

    public function getStatusTextAttribute()
    {
        return $this->statusPendaftaran[$this->status] ?? 'Tidak Diketahui';
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
}
