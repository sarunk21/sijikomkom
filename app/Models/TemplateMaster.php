<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateMaster extends Model
{
    use HasFactory;

    protected $table = 'template_master';

    protected $fillable = [
        'nama_template',
        'tipe_template',
        'skema_id',
        'deskripsi',
        'file_path',
        'variables',
        'ttd_path',
        'is_active'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Relasi ke Skema
     */
    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }

    /**
     * Scope untuk template aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk tipe template tertentu
     */
    public function scopeByType($query, $type)
    {
        return $query->where('tipe_template', $type);
    }

    /**
     * Accessor untuk mendapatkan tipe template dengan label
     */
    public function getTipeTemplateLabelAttribute()
    {
        $labels = [
            'APL1' => 'APL 1 (Asesmen Mandiri)',
            'APL2' => 'APL 2 (Portofolio)',
            'APL3' => 'APL 3 (Simulasi)',
        ];

        return $labels[$this->tipe_template] ?? $this->tipe_template;
    }

    /**
     * Accessor untuk mendapatkan URL file template
     */
    public function getTemplateUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Accessor untuk mendapatkan URL TTD
     */
    public function getTtdUrlAttribute()
    {
        return $this->ttd_path ? asset('storage/' . $this->ttd_path) : null;
    }
}
