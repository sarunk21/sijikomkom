<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Response extends Model
{
    use HasFactory;

    protected $table = 'responses';
    protected $fillable = [
        'pendaftaran_id',
        'apl2_id',
        'answer_text',
        'kesimpulan',
        'bk_k_answer',
        'bukti_isian_tes',
        'bukti_file_path',
        'digital_signature',
        'signature_timestamp',
        'signature_ip',
        'asesor_signature',
        'asesor_signature_timestamp',
        'asesor_signature_ip',
        'custom_response'
    ];

    protected $casts = [
        'signature_timestamp' => 'datetime',
        'asesor_signature_timestamp' => 'datetime'
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function apl2()
    {
        return $this->belongsTo(APL2::class);
    }
}
