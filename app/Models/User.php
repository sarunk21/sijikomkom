<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',
        'nik',
        'telephone',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'kebangsaan',
        'pekerjaan',
        'pendidikan',
        'jurusan',
        'photo_diri',
        'tanda_tangan',
        'user_type',
        'photo_ktp',
        'photo_sertifikat',
        'photo_ktmkhs',
        'photo_administatif',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function checkProfileLengkapAsesi()
    {
        $requiredFields = [
            'nim',
            'nik',
            'telephone',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'alamat',
            'kebangsaan',
            'pekerjaan',
            'pendidikan',
            'jurusan',
            'photo_diri'
        ];

        foreach ($requiredFields as $field) {
            if (!$this->$field || $this->$field == null || $this->$field == '') {
                return false;
            }
        }

        return true;
    }

    // Relasi many-to-many dengan Skema (untuk asesor)
    public function skemas()
    {
        return $this->belongsToMany(Skema::class, 'asesor_skema', 'asesor_id', 'skema_id');
    }

    // Relasi untuk asesor_skema
    public function asesorSkemas()
    {
        return $this->hasMany(AsesorSkema::class, 'asesor_id');
    }
}
