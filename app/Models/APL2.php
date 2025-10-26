<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class APL2 extends Model
{
    use HasFactory;

    protected $table = 'apl2';
    protected $fillable = [
        'skema_id',
        'question_text',
        'question_config',
        'question_type',
        'question_options',
        'bukti_isian_tes',
        'is_bk_k_question',
        'urutan',
        'custom_data'
    ];

    protected $casts = [
        'question_config' => 'array',
        'question_options' => 'array',
        'is_bk_k_question' => 'boolean'
    ];

    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'apl2_id', 'id');
    }
}
