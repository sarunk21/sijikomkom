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

        // Tambahkan asesor untuk Analis Program
        $asesorDataAnalisProgram = [
            [
                'name' => 'Muhammad Panji Muslim, S.Pd., M.Kom.',
                'nik' => '3201234567890003',
                'nim' => null,
                'telephone' => '628123456003',
                'email' => 'panji.muslim@asesor.com',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1986-03-10',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Asesor No 3, Jakarta',
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
                'password' => Hash::make('panji.muslim@asesor.com'),
            ],
            [
                'name' => 'Nurhuda Maulana, S.T., M.T.',
                'nik' => '3201234567890004',
                'nim' => null,
                'telephone' => '628123456004',
                'email' => 'nurhuda.maulana@asesor.com',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1988-06-25',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Asesor No 4, Surabaya',
                'kebangsaan' => 'Indonesia',
                'pekerjaan' => 'Asesor',
                'pendidikan' => 'S2 Magister Teknik',
                'jurusan' => 'Teknik Informatika',
                'photo_diri' => null,
                'photo_ktp' => null,
                'photo_sertifikat' => null,
                'photo_ktmkhs' => null,
                'photo_administatif' => null,
                'tanda_tangan' => null,
                'user_type' => 'asesor',
                'password' => Hash::make('nurhuda.maulana@asesor.com'),
            ],
        ];

        foreach ($asesorDataAnalisProgram as $data) {
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

        // Hubungkan asesor dengan skema Analis Program
        $analisProgramSkema = Skema::where('kode', 'AP')->first();

        if ($analisProgramSkema) {
            $asesorPanji = User::where('email', 'panji.muslim@asesor.com')->first();
            $asesorNurhuda = User::where('email', 'nurhuda.maulana@asesor.com')->first();

            if ($asesorPanji) {
                AsesorSkema::create([
                    'asesor_id' => $asesorPanji->id,
                    'skema_id' => $analisProgramSkema->id,
                ]);
            }

            if ($asesorNurhuda) {
                AsesorSkema::create([
                    'asesor_id' => $asesorNurhuda->id,
                    'skema_id' => $analisProgramSkema->id,
                ]);
            }
        }

        // Tambahkan asesor untuk Asisten Pemrograman
        $asesorDataAsistenPemrograman = [
            [
                'name' => 'Bambang Tri Wahyono, S.Kom., M.Si',
                'nik' => '3201234567890005',
                'nim' => null,
                'telephone' => '628123456005',
                'email' => 'bambang.tri@asesor.com',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1985-07-12',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Asesor No 5, Jakarta',
                'kebangsaan' => 'Indonesia',
                'pekerjaan' => 'Asesor',
                'pendidikan' => 'S2 Magister Sains',
                'jurusan' => 'Ilmu Komputer',
                'photo_diri' => null,
                'photo_ktp' => null,
                'photo_sertifikat' => null,
                'photo_ktmkhs' => null,
                'photo_administatif' => null,
                'tanda_tangan' => null,
                'user_type' => 'asesor',
                'password' => Hash::make('bambang.tri@asesor.com'),
            ],
            [
                'name' => 'M. Bayu Wibisono, S.Kom.,MM',
                'nik' => '3201234567890006',
                'nim' => null,
                'telephone' => '628123456006',
                'email' => 'bayu.wibisono@asesor.com',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1987-09-18',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Asesor No 6, Yogyakarta',
                'kebangsaan' => 'Indonesia',
                'pekerjaan' => 'Asesor',
                'pendidikan' => 'S2 Magister Manajemen',
                'jurusan' => 'Manajemen',
                'photo_diri' => null,
                'photo_ktp' => null,
                'photo_sertifikat' => null,
                'photo_ktmkhs' => null,
                'photo_administatif' => null,
                'tanda_tangan' => null,
                'user_type' => 'asesor',
                'password' => Hash::make('bayu.wibisono@asesor.com'),
            ],
        ];

        foreach ($asesorDataAsistenPemrograman as $data) {
            User::create($data);
        }

        // Hubungkan asesor dengan skema Asisten Pemrograman
        $asistenPemrogramanSkema = Skema::where('kode', 'ASP')->first();

        if ($asistenPemrogramanSkema) {
            $asesorBambang = User::where('email', 'bambang.tri@asesor.com')->first();
            $asesorBayu = User::where('email', 'bayu.wibisono@asesor.com')->first();

            if ($asesorBambang) {
                AsesorSkema::create([
                    'asesor_id' => $asesorBambang->id,
                    'skema_id' => $asistenPemrogramanSkema->id,
                ]);
            }

            if ($asesorBayu) {
                AsesorSkema::create([
                    'asesor_id' => $asesorBayu->id,
                    'skema_id' => $asistenPemrogramanSkema->id,
                ]);
            }
        }
    }
}
