<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankSoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bank_soal';

    protected $fillable = [
        'skema_id',
        'tipe',
        'target',
        'file_path',
        'original_filename',
        'is_active',
        'keterangan',
        'variables',
        'field_configurations',
        'field_mappings',
        'custom_variables'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'variables' => 'array',
        'field_configurations' => 'array',
        'field_mappings' => 'array',
        'custom_variables' => 'array'
    ];

    // Relasi
    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }

    // Accessor untuk mendapatkan nama tipe yang user-friendly
    public function getTipeTextAttribute()
    {
        $tipeMap = [
            'FR IA 03' => 'FR IA 03 - Formulir Asesmen Mandiri',
            'FR IA 06' => 'FR IA 06 - Formulir Asesmen Praktik',
            'FR IA 07' => 'FR IA 07 - Ceklis Observasi Asesor'
        ];

        return $tipeMap[$this->tipe] ?? $this->tipe;
    }
}
