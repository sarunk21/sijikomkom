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
        'is_active',
        'apl2_config',
        'apl2_questions',
        'apl2_checkbox_config',
        'field_configurations',
        'field_mappings',
        'custom_variables',
        'fr_ak_05_file_path',
        'fr_ak_05_variables'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'apl2_config' => 'array',
        'apl2_questions' => 'array',
        'apl2_checkbox_config' => 'array',
        'field_configurations' => 'array',
        'field_mappings' => 'array',
        'custom_variables' => 'array',
        'fr_ak_05_variables' => 'array'
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
            'FR_AK_05' => 'FR AK 05 (Form Asesmen Asesor)',
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

    /**
     * Accessor untuk mendapatkan URL file FR AK 05 template
     */
    public function getFrAk05TemplateUrlAttribute()
    {
        return $this->fr_ak_05_file_path ? asset('storage/' . $this->fr_ak_05_file_path) : null;
    }
}
