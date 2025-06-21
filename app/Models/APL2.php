<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class APL2 extends Model
{
    use HasFactory;

    protected $table = 'apl2';
    protected $fillable = ['skema_id', 'question_text'];

    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}
