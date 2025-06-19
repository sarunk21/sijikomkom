<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class APL2 extends Model
{
    use HasFactory;

    protected $table = 'apl2';
    protected $fillable = ['skema_id', 'link_ujikom_asesor', 'link_ujikom_asesi'];

    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }
}
