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

        // Tambahkan asesor untuk Junior Web Programmer
        $asesorDataJuniorWebProgrammer = [
            [
                'name' => 'Theresiawati, S.Kom.,M.TI',
                'nik' => '3201234567890007',
                'nim' => null,
                'telephone' => '628123456007',
                'email' => 'theresiawati@asesor.com',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1986-04-15',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Asesor No 7, Jakarta',
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
                'password' => Hash::make('theresiawati@asesor.com'),
            ],
            [
                'name' => 'Tri Rahayu, S.Kom., M.M.',
                'nik' => '3201234567890008',
                'nim' => null,
                'telephone' => '628123456008',
                'email' => 'tri.rahayu@asesor.com',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1987-05-20',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Asesor No 8, Yogyakarta',
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
                'password' => Hash::make('tri.rahayu@asesor.com'),
            ],
            [
                'name' => 'Rio Wirawan, S.Kom., MMSI.',
                'nik' => '3201234567890009',
                'nim' => null,
                'telephone' => '628123456009',
                'email' => 'rio.wirawan@asesor.com',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1988-06-25',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Asesor No 9, Bandung',
                'kebangsaan' => 'Indonesia',
                'pekerjaan' => 'Asesor',
                'pendidikan' => 'S2 Magister Manajemen Sistem Informasi',
                'jurusan' => 'Manajemen Sistem Informasi',
                'photo_diri' => null,
                'photo_ktp' => null,
                'photo_sertifikat' => null,
                'photo_ktmkhs' => null,
                'photo_administatif' => null,
                'tanda_tangan' => null,
                'user_type' => 'asesor',
                'password' => Hash::make('rio.wirawan@asesor.com'),
            ],
        ];

        foreach ($asesorDataJuniorWebProgrammer as $data) {
            User::create($data);
        }

        // Hubungkan asesor dengan skema Junior Web Programmer
        $juniorWebProgrammerSkema = Skema::where('kode', 'JWP')->first();

        if ($juniorWebProgrammerSkema) {
            $asesorTheresiawati = User::where('email', 'theresiawati@asesor.com')->first();
            $asesorTriRahayu = User::where('email', 'tri.rahayu@asesor.com')->first();
            $asesorRioWirawan = User::where('email', 'rio.wirawan@asesor.com')->first();

            if ($asesorTheresiawati) {
                AsesorSkema::create([
                    'asesor_id' => $asesorTheresiawati->id,
                    'skema_id' => $juniorWebProgrammerSkema->id,
                ]);
            }

            if ($asesorTriRahayu) {
                AsesorSkema::create([
                    'asesor_id' => $asesorTriRahayu->id,
                    'skema_id' => $juniorWebProgrammerSkema->id,
                ]);
            }

            if ($asesorRioWirawan) {
                AsesorSkema::create([
                    'asesor_id' => $asesorRioWirawan->id,
                    'skema_id' => $juniorWebProgrammerSkema->id,
                ]);
            }
        }

        // Tambahkan asesor untuk Junior / Associate Data Scientist
        $asesorDataJuniorDataScientist = [
            [
                'name' => 'Ika Nurlaili Isnainiyah, S.Kom.M.Sc.',
                'nik' => '3201234567890010',
                'nim' => null,
                'telephone' => '628123456010',
                'email' => 'ika.nurlaili@asesor.com',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1987-08-15',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Asesor No 10, Jakarta',
                'kebangsaan' => 'Indonesia',
                'pekerjaan' => 'Asesor',
                'pendidikan' => 'S2 Master of Science',
                'jurusan' => 'Ilmu Komputer',
                'photo_diri' => null,
                'photo_ktp' => null,
                'photo_sertifikat' => null,
                'photo_ktmkhs' => null,
                'photo_administatif' => null,
                'tanda_tangan' => null,
                'user_type' => 'asesor',
                'password' => Hash::make('ika.nurlaili@asesor.com'),
            ],
            [
                'name' => 'Iin Ernawati, S.Kom.M.Si.',
                'nik' => '3201234567890011',
                'nim' => null,
                'telephone' => '628123456011',
                'email' => 'iin.ernawati@asesor.com',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1988-09-20',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Asesor No 11, Bandung',
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
                'password' => Hash::make('iin.ernawati@asesor.com'),
            ],
        ];

        foreach ($asesorDataJuniorDataScientist as $data) {
            User::create($data);
        }

        // Hubungkan asesor dengan skema Junior / Associate Data Scientist
        $juniorDataScientistSkema = Skema::where('kode', 'JADS')->first();

        if ($juniorDataScientistSkema) {
            $asesorIka = User::where('email', 'ika.nurlaili@asesor.com')->first();
            $asesorIin = User::where('email', 'iin.ernawati@asesor.com')->first();

            if ($asesorIka) {
                AsesorSkema::create([
                    'asesor_id' => $asesorIka->id,
                    'skema_id' => $juniorDataScientistSkema->id,
                ]);
            }

            if ($asesorIin) {
                AsesorSkema::create([
                    'asesor_id' => $asesorIin->id,
                    'skema_id' => $juniorDataScientistSkema->id,
                ]);
            }
        }

        // Tambahkan asesor untuk Cyber Security Analyst
        $asesorDataCyberSecurityAnalyst = [
            [
                'name' => 'Bayu Hananto, S.Kom.M.Kom',
                'nik' => '3201234567890012',
                'nim' => null,
                'telephone' => '628123456012',
                'email' => 'bayu.hananto@asesor.com',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1986-10-12',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Asesor No 12, Jakarta',
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
                'password' => Hash::make('bayu.hananto@asesor.com'),
            ],
            [
                'name' => 'Henki Bayu Seta, S.Kom.MTI',
                'nik' => '3201234567890013',
                'nim' => null,
                'telephone' => '628123456013',
                'email' => 'henki.bayu@asesor.com',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1987-11-18',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Asesor No 13, Surabaya',
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
                'password' => Hash::make('henki.bayu@asesor.com'),
            ],
        ];

        foreach ($asesorDataCyberSecurityAnalyst as $data) {
            User::create($data);
        }

        // Hubungkan asesor dengan skema Cyber Security Analyst
        $cyberSecurityAnalystSkema = Skema::where('kode', 'CSA')->first();

        if ($cyberSecurityAnalystSkema) {
            $asesorBayuHananto = User::where('email', 'bayu.hananto@asesor.com')->first();
            $asesorHenkiBayu = User::where('email', 'henki.bayu@asesor.com')->first();

            if ($asesorBayuHananto) {
                AsesorSkema::create([
                    'asesor_id' => $asesorBayuHananto->id,
                    'skema_id' => $cyberSecurityAnalystSkema->id,
                ]);
            }

            if ($asesorHenkiBayu) {
                AsesorSkema::create([
                    'asesor_id' => $asesorHenkiBayu->id,
                    'skema_id' => $cyberSecurityAnalystSkema->id,
                ]);
            }
        }

        // Tambahkan asesor untuk Junior / Associate Data Engineer
        $asesorDataJuniorDataEngineer = [
            [
                'name' => 'I Wayan Widi Pradnyana',
                'nik' => '3201234567890014',
                'nim' => null,
                'telephone' => '628123456014',
                'email' => 'wayan.widi@asesor.com',
                'tempat_lahir' => 'Denpasar',
                'tanggal_lahir' => '1985-12-05',
                'jenis_kelamin' => 'L',
                'alamat' => 'Jl. Asesor No 14, Denpasar',
                'kebangsaan' => 'Indonesia',
                'pekerjaan' => 'Asesor',
                'pendidikan' => 'S2',
                'jurusan' => 'Ilmu Komputer',
                'photo_diri' => null,
                'photo_ktp' => null,
                'photo_sertifikat' => null,
                'photo_ktmkhs' => null,
                'photo_administatif' => null,
                'tanda_tangan' => null,
                'user_type' => 'asesor',
                'password' => Hash::make('wayan.widi@asesor.com'),
            ],
            [
                'name' => 'Nindy Irzavika, S.SI., M.T.',
                'nik' => '3201234567890015',
                'nim' => null,
                'telephone' => '628123456015',
                'email' => 'nindy.irzavika@asesor.com',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1986-01-10',
                'jenis_kelamin' => 'P',
                'alamat' => 'Jl. Asesor No 15, Yogyakarta',
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
                'password' => Hash::make('nindy.irzavika@asesor.com'),
            ],
        ];

        foreach ($asesorDataJuniorDataEngineer as $data) {
            User::create($data);
        }

        // Hubungkan asesor dengan skema Junior / Associate Data Engineer
        $juniorDataEngineerSkema = Skema::where('kode', 'JADE')->first();

        if ($juniorDataEngineerSkema) {
            $asesorWayan = User::where('email', 'wayan.widi@asesor.com')->first();
            $asesorNindy = User::where('email', 'nindy.irzavika@asesor.com')->first();

            if ($asesorWayan) {
                AsesorSkema::create([
                    'asesor_id' => $asesorWayan->id,
                    'skema_id' => $juniorDataEngineerSkema->id,
                ]);
            }

            if ($asesorNindy) {
                AsesorSkema::create([
                    'asesor_id' => $asesorNindy->id,
                    'skema_id' => $juniorDataEngineerSkema->id,
                ]);
            }
        }
    }
}
