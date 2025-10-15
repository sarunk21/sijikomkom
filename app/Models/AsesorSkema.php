<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsesorSkema extends Model
{
    protected $table = 'asesor_skema';
    public $timestamps = false;
    protected $fillable = ['asesor_id', 'skema_id'];

    public function asesor()
    {
        return $this->belongsTo(User::class, 'asesor_id');
    }

    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }
}
