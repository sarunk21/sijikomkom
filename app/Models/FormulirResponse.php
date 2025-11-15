<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormulirResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'jadwal_id',
        'user_id',
        'bank_soal_id',
        'asesi_responses',
        'asesor_responses',
        'asesor_validations',
        'is_asesor_completed',
        'status',
        'catatan_asesor',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'asesi_responses' => 'array',
        'asesor_responses' => 'array',
        'asesor_validations' => 'array',
        'is_asesor_completed' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
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

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class);
    }

    // Scope
    public function scopeByJadwal($query, $jadwalId)
    {
        return $query->where('jadwal_id', $jadwalId);
    }

    public function scopeByAsesi($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }
}
