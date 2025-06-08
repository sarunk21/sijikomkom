<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendaftaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pendaftaran';
    protected $fillable = ['jadwal_id', 'user_id', 'skema_id', 'tuk_id', 'verif_stage'];

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
}
