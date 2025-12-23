<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Skema;
use App\Models\Tuk;
use App\Models\Jadwal;
use App\Models\Pendaftaran;
use App\Models\PendaftaranUjikom;
use App\Models\AsesiPenilaian;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class JuniorWebProgrammerJuliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data skema Junior Web Programmer
        $skema = Skema::where('kode', 'JWP')->first();

        // Ambil TUK Laboratorium B&C Pemrograman
        $tuk = Tuk::where('kode', 'TUK10')->first();

        if (!$skema || !$tuk) {
            $this->command->error('Skema Junior Web Programmer atau TUK tidak ditemukan!');
            return;
        }

        // Buat jadwal ujian tanggal 4 Juli 2025
        $jadwal = Jadwal::create([
            'skema_id' => $skema->id,
            'tuk_id' => $tuk->id,
            'tanggal_ujian' => '2025-07-04',
            'tanggal_selesai' => '2025-07-04',
            'tanggal_maksimal_pendaftaran' => '2025-06-27',
            'status' => 4, // Selesai
            'kuota' => 41,
            'keterangan' => 'Ujian Sertifikasi Junior Web Programmer - 4 Juli 2025',
        ]);

        // Ambil 41 asesi
        $asesiEmails = [
            'syawalia.nurul@gmail.com',
            'faris.rama@gmail.com',
            'rizky.dwi@gmail.com',
            'albirr.inzal@gmail.com',
            'ravi.sultan@gmail.com',
            'willy.sinaga@gmail.com',
            'nuzulul.firdaus@gmail.com',
            'ahmad.azhar@gmail.com',
            'arya.indera@gmail.com',
            'lazzuazra.arindra@gmail.com',
            'rio.brian@gmail.com',
            'haifa.ludina@gmail.com',
            'febrian.hanafi@gmail.com',
            'mia.rahmatika@gmail.com',
            'anisa.yuliawati@gmail.com',
            'nazhmy.zahrian@gmail.com',
            'gia.daisy@gmail.com',
            'natasya.helmalia@gmail.com',
            'rima.putri@gmail.com',
            'fadila.asma@gmail.com',
            'nawah.nabila@gmail.com',
            'faiqotul.hikma@gmail.com',
            'raihana.qonita@gmail.com',
            'rachma.adzima@gmail.com',
            'dias.syahadatputra@gmail.com',
            'lumongga.anastasia@gmail.com',
            'nelpida.nahampun@gmail.com',
            'christian.suherman@gmail.com',
            'noor.malika@gmail.com',
            'stephanus.mark@gmail.com',
            'noufal.valery@gmail.com',
            'izzi.elghiffary@gmail.com',
            'ferdi.akhdan@gmail.com',
            'rayhan.ghazalla@gmail.com',
            'farhan.rizki@gmail.com',
            'sultan.daffa@gmail.com',
            'jidan.inas@gmail.com',
            'salsabila@gmail.com',
            'zahra.meysa@gmail.com',
            'kelvin.surya@gmail.com',
            'arkansyah.hadaya@gmail.com',
        ];

        $asesiList = User::whereIn('email', $asesiEmails)->get();

        if ($asesiList->count() !== 41) {
            $this->command->error('Jumlah asesi tidak sesuai! Ditemukan: ' . $asesiList->count());
            return;
        }

        // Ambil asesor
        $asesor1 = User::where('email', 'theresiawati@asesor.com')->first();
        $asesor2 = User::where('email', 'tri.rahayu@asesor.com')->first();
        $asesor3 = User::where('email', 'rio.wirawan@asesor.com')->first();

        if (!$asesor1 || !$asesor2 || !$asesor3) {
            $this->command->error('Asesor tidak ditemukan!');
            return;
        }

        // Distribute asesi ke asesor (14 untuk asesor1, 14 untuk asesor2, 13 untuk asesor3)
        $asesorDistribution = [
            $asesor1->id => [], // Theresiawati
            $asesor2->id => [], // Tri Rahayu
            $asesor3->id => [], // Rio Wirawan
        ];

        // Pembagian: 14 asesi untuk asesor1, 14 asesi untuk asesor2, 13 asesi untuk asesor3
        $counter = 0;
        foreach ($asesiList as $asesi) {
            if ($counter < 14) {
                $asesorDistribution[$asesor1->id][] = $asesi;
            } elseif ($counter < 28) {
                $asesorDistribution[$asesor2->id][] = $asesi;
            } else {
                $asesorDistribution[$asesor3->id][] = $asesi;
            }
            $counter++;
        }

        // Buat data pendaftaran, pendaftaran ujikom, penilaian, dan report untuk setiap asesi
        // Set tanggal pendaftaran di bulan Juni 2025
        $tanggalPendaftaran = Carbon::parse('2025-06-20 10:00:00');

        foreach ($asesorDistribution as $asesorId => $asesiGroup) {
            foreach ($asesiGroup as $asesi) {
                // 1. Buat Pendaftaran
                $pendaftaran = Pendaftaran::create([
                    'jadwal_id' => $jadwal->id,
                    'user_id' => $asesi->id,
                    'skema_id' => $skema->id,
                    'tuk_id' => $tuk->id,
                    'status' => 11, // Selesai
                    'keterangan' => 'Ujian telah selesai dilaksanakan',
                    'custom_variables' => null,
                    'ttd_asesi_path' => null,
                    'ttd_asesor_path' => null,
                    'asesor_assessment' => null,
                    'asesor_data' => [
                        'asesor_id' => $asesorId,
                        'asesor_name' => User::find($asesorId)->name,
                    ],
                    'created_at' => $tanggalPendaftaran,
                    'updated_at' => $tanggalPendaftaran,
                ]);

                // 2. Buat Pendaftaran Ujikom
                $pendaftaranUjikom = PendaftaranUjikom::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'jadwal_id' => $jadwal->id,
                    'asesi_id' => $asesi->id,
                    'asesor_id' => $asesorId,
                    'status' => 5, // Kompeten
                    'keterangan' => 'Asesi dinyatakan KOMPETEN',
                    'asesor_confirmed' => true,
                    'asesor_confirmed_at' => Carbon::parse('2025-07-04 16:00:00'),
                    'asesor_notes' => 'Asesi menunjukkan kompetensi yang baik dalam seluruh aspek penilaian',
                ]);

                // 3. Buat Asesi Penilaian
                $asesiPenilaian = AsesiPenilaian::create([
                    'jadwal_id' => $jadwal->id,
                    'user_id' => $asesi->id,
                    'asesor_id' => $asesorId,
                    'formulir_status' => [
                        [
                            'formulir_id' => 1,
                            'formulir_name' => 'FR.AK.01',
                            'is_checked' => true,
                            'checked_at' => '2025-07-04 14:00:00',
                        ],
                        [
                            'formulir_id' => 2,
                            'formulir_name' => 'FR.AK.02',
                            'is_checked' => true,
                            'checked_at' => '2025-07-04 14:30:00',
                        ],
                        [
                            'formulir_id' => 3,
                            'formulir_name' => 'FR.AK.03',
                            'is_checked' => true,
                            'checked_at' => '2025-07-04 15:00:00',
                        ],
                    ],
                    'fr_ai_07_completed' => true,
                    'fr_ai_07_data' => [
                        'rekomendasi' => 'K', // Kompeten
                        'catatan' => 'Memenuhi seluruh kriteria kompetensi',
                        'completed_at' => '2025-07-04 15:30:00',
                    ],
                    'hasil_akhir' => 'kompeten',
                    'catatan_asesor' => 'Asesi menunjukkan pemahaman yang baik dan mampu menerapkan konsep Junior Web Programmer dengan tepat',
                    'penilaian_at' => Carbon::parse('2025-07-04 15:30:00'),
                ]);

                // 4. Buat Report
                $report = Report::create([
                    'user_id' => $asesi->id,
                    'skema_id' => $skema->id,
                    'jadwal_id' => $jadwal->id,
                    'pendaftaran_id' => $pendaftaran->id,
                    'status' => 1, // Kompeten
                ]);

                $this->command->info("Data ujikom untuk {$asesi->name} berhasil dibuat");
            }
        }

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Seeder Junior Web Programmer Juli 2025 selesai!');
        $this->command->info('========================================');
        $this->command->info('Tanggal: 4 Juli 2025');
        $this->command->info('TUK: ' . $tuk->nama);
        $this->command->info('Asesor 1: ' . $asesor1->name);
        $this->command->info('Asesor 2: ' . $asesor2->name);
        $this->command->info('Asesor 3: ' . $asesor3->name);
        $this->command->info('Total Asesi: ' . $asesiList->count() . ' orang');
        $this->command->info('Asesor 1 (' . $asesor1->name . '): 14 asesi');
        $this->command->info('Asesor 2 (' . $asesor2->name . '): 14 asesi');
        $this->command->info('Asesor 3 (' . $asesor3->name . '): 13 asesi');
        $this->command->info('Semua asesi dinyatakan KOMPETEN');
        $this->command->info('========================================');
    }
}

