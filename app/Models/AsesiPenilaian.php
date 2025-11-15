<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsesiPenilaian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asesi_penilaian';

    protected $fillable = [
        'jadwal_id',
        'user_id',
        'asesor_id',
        'formulir_status',
        'fr_ai_07_completed',
        'fr_ai_07_data',
        'hasil_akhir',
        'catatan_asesor',
        'penilaian_at',
    ];

    protected $casts = [
        'formulir_status' => 'array',
        'fr_ai_07_completed' => 'boolean',
        'fr_ai_07_data' => 'array',
        'penilaian_at' => 'datetime',
    ];

    // Relasi
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function asesi()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    // Scope
    public function scopeByJadwal($query, $jadwalId)
    {
        return $query->where('jadwal_id', $jadwalId);
    }

    public function scopeKompeten($query)
    {
        return $query->where('hasil_akhir', 'kompeten');
    }

    public function scopeBelumKompeten($query)
    {
        return $query->where('hasil_akhir', 'belum_kompeten');
    }

    public function scopeBelumDinilai($query)
    {
        return $query->where('hasil_akhir', 'belum_dinilai');
    }

    // Helper methods
    public function canGiveHasilAkhir()
    {
        // Cek apakah semua formulir sudah dicek dan FR AI 07 sudah diisi
        if (!$this->fr_ai_07_completed) {
            return false;
        }

        if (!$this->formulir_status) {
            return false;
        }

        // Cek apakah semua formulir sudah dicek
        foreach ($this->formulir_status as $status) {
            if (!isset($status['is_checked']) || !$status['is_checked']) {
                return false;
            }
        }

        return true;
    }
}
