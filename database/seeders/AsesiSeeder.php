<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AsesiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $asesiData = [
            [
                'name' => 'Muhammad Rafli Alfiardi',
                'nik' => '3275011811020010',
                'nim' => '2110512014',
                'telephone' => '6281296845922',
                'email' => 'rafli@gmail.com',
                'tempat_lahir' => 'Bekasi',
                'tanggal_lahir' => '2002-11-18',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Borneo 1 Blok D5 No 17',
                'kebangsaan' => 'Indonesia',
                'pekerjaan' => 'Mahasiswa',
                'pendidikan' => 'S1 Sistem Informasi',
                'jurusan' => 'S1 Sistem Informasi',
                'photo_diri' => null,
                'photo_ktp' => null,
                'photo_sertifikat' => null,
                'photo_ktmkhs' => null,
                'photo_administatif' => null,
                'tanda_tangan' => null,
                'user_type' => 'asesi',
                'password' => Hash::make('rafli@gmail.com'),
            ],
            [
                'name' => 'Muhammad Kautsar Panggawa',
                'nik' => '3275011811020022',
                'nim' => '2110512022',
                'telephone' => '6281234567890',
                'email' => 'kautsar@gmail.com',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2002-05-10',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Melati No 10',
                'kebangsaan' => 'Indonesia',
                'pekerjaan' => 'Mahasiswa',
                'pendidikan' => 'S1 Sistem Informasi',
                'jurusan' => 'S1 Sistem Informasi',
                'photo_diri' => null,
                'photo_ktp' => null,
                'photo_sertifikat' => null,
                'photo_ktmkhs' => null,
                'photo_administatif' => null,
                'tanda_tangan' => null,
                'user_type' => 'asesi',
                'password' => Hash::make('kautsar@gmail.com'),
            ],
            [
                'name' => 'Mochammad Faishal Abyansyah',
                'nik' => '3275011811020033',
                'nim' => '2110512033',
                'telephone' => '6289876543210',
                'email' => 'faishal@gmail.com',
                'tempat_lahir' => 'Depok',
                'tanggal_lahir' => '2002-08-15',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Kenanga No 5',
                'kebangsaan' => 'Indonesia',
                'pekerjaan' => 'Mahasiswa',
                'pendidikan' => 'S1 Sistem Informasi',
                'jurusan' => 'S1 Sistem Informasi',
                'photo_diri' => null,
                'photo_ktp' => null,
                'photo_sertifikat' => null,
                'photo_ktmkhs' => null,
                'photo_administatif' => null,
                'tanda_tangan' => null,
                'user_type' => 'asesi',
                'password' => Hash::make('faishal@gmail.com'),
            ],
        ];

        foreach ($asesiData as $data) {
            User::create($data);
        }
    }
}
