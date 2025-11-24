<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Skema;
use App\Models\AsesorSkema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AsesorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $asesorData = [
            [
                'name' => 'Anita Muliawati, S.Kom. MTI.',
                'nik' => '3201234567890001',
                'nim' => null,
                'telephone' => '628123456001',
                'email' => 'anita.muliawati@asesor.com',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1985-05-15',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Asesor No 1, Jakarta',
                'kebangsaan' => 'Indonesia',
                'pekerjaan' => 'Asesor',
                'pendidikan' => 'S2 Magister Teknologi Informasi',
                'jurusan' => 'Teknologi Informasi',
                'photo_diri' => null,
                'photo_ktp' => null,
                'photo_sertifikat' => null,
                'photo_ktmkhs' => null,
                'photo_administatif' => null,
                'tanda_tangan' => null,
                'user_type' => 'asesor',
                'password' => Hash::make('anita.muliawati@asesor.com'),
            ],
            [
                'name' => 'Sarika, S.Kom,M.Kom.',
                'nik' => '3201234567890002',
                'nim' => null,
                'telephone' => '628123456002',
                'email' => 'sarika@asesor.com',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1987-08-20',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Asesor No 2, Bandung',
                'kebangsaan' => 'Indonesia',
                'pekerjaan' => 'Asesor',
                'pendidikan' => 'S2 Magister Komputer',
                'jurusan' => 'Ilmu Komputer',
                'photo_diri' => null,
                'photo_ktp' => null,
                'photo_sertifikat' => null,
                'photo_ktmkhs' => null,
                'photo_administatif' => null,
                'tanda_tangan' => null,
                'user_type' => 'asesor',
                'password' => Hash::make('sarika@asesor.com'),
            ],
        ];

        foreach ($asesorData as $data) {
            User::create($data);
        }

        // Hubungkan asesor dengan skema System Analyst
        $systemAnalystSkema = Skema::where('kode', 'SA')->first();

        if ($systemAnalystSkema) {
            $asesor1 = User::where('email', 'anita.muliawati@asesor.com')->first();
            $asesor2 = User::where('email', 'sarika@asesor.com')->first();

            if ($asesor1) {
                AsesorSkema::create([
                    'asesor_id' => $asesor1->id,
                    'skema_id' => $systemAnalystSkema->id,
                ]);
            }

            if ($asesor2) {
                AsesorSkema::create([
                    'asesor_id' => $asesor2->id,
                    'skema_id' => $systemAnalystSkema->id,
                ]);
            }
        }
    }
}
