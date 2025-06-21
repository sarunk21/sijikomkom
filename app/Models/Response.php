<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Response extends Model
{
    use HasFactory;

    protected $table = 'responses';
    protected $fillable = ['pendaftaran_id', 'apl2_id', 'answer_text'];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function apl2()
    {
        return $this->belongsTo(APL2::class);
    }
}
