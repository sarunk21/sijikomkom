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

class JuniorAssociateDataScientistDesemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data skema Junior / Associate Data Scientist
        $skema = Skema::where('kode', 'JADS')->first();

        // Ambil TUK Laboratorium Data Science dan Data Mining
        $tuk = Tuk::where('kode', 'TUK11')->first();

        if (!$skema || !$tuk) {
            $this->command->error('Skema Junior / Associate Data Scientist atau TUK tidak ditemukan!');
            return;
        }

        // Buat jadwal ujian tanggal 5 Desember 2024
        $jadwal = Jadwal::create([
            'skema_id' => $skema->id,
            'tuk_id' => $tuk->id,
            'tanggal_ujian' => '2024-12-05',
            'tanggal_selesai' => '2024-12-05',
            'tanggal_maksimal_pendaftaran' => '2024-11-28',
            'status' => 4, // Selesai
            'kuota' => 26,
            'keterangan' => 'Ujian Sertifikasi Junior / Associate Data Scientist - 5 Desember 2024',
        ]);

        // Ambil 26 asesi
        $asesiEmails = [
            'frida.putriassa@gmail.com',
            'arif.rahman@gmail.com',
            'risma.nurcahyani@gmail.com',
            'angelia@gmail.com',
            'arsi.demike@gmail.com',
            'rostiana.brigita@gmail.com',
            'razzi.permana@gmail.com',
            'rafiano.daniswara@gmail.com',
            'bintang.dwi@gmail.com',
            'hawna.adisty@gmail.com',
            'thesa.pebrianti@gmail.com',
            'dyah.pramesti@gmail.com',
            'natasha.azzahra@gmail.com',
            'anisa.fadilah@gmail.com',
            'cecilia.isadora@gmail.com',
            'divasya.valentiaji@gmail.com',
            'renatha.adzuria@gmail.com',
            'rakha.dimas@gmail.com',
            'meutia.quroti@gmail.com',
            'anggita.sondang@gmail.com',
            'zidane.zukhrufa@gmail.com',
            'andreas.malvino@gmail.com',
            'rubben.siahaan@gmail.com',
            'miftahul.ahmadil@gmail.com', // BK
            'novany.sheila@gmail.com',
            'nova.enjelina@gmail.com',
        ];

        $asesiList = User::whereIn('email', $asesiEmails)->get();

        if ($asesiList->count() !== 26) {
            $this->command->error('Jumlah asesi tidak sesuai! Ditemukan: ' . $asesiList->count());
            return;
        }

        // Ambil asesor
        $asesor1 = User::where('email', 'ika.nurlaili@asesor.com')->first();
        $asesor2 = User::where('email', 'iin.ernawati@asesor.com')->first();

        if (!$asesor1 || !$asesor2) {
            $this->command->error('Asesor tidak ditemukan!');
            return;
        }

        // Distribute asesi ke asesor (13 untuk asesor1, 13 untuk asesor2)
        $asesorDistribution = [
            $asesor1->id => [], // Ika Nurlaili
            $asesor2->id => [], // Iin Ernawati
        ];

        // Pembagian: 13 asesi untuk asesor1, 13 asesi untuk asesor2
        $counter = 0;
        foreach ($asesiList as $asesi) {
            if ($counter < 13) {
                $asesorDistribution[$asesor1->id][] = $asesi;
            } else {
                $asesorDistribution[$asesor2->id][] = $asesi;
            }
            $counter++;
        }

        // Buat data pendaftaran, pendaftaran ujikom, penilaian, dan report untuk setiap asesi
        // Set tanggal pendaftaran di bulan November 2024
        $tanggalPendaftaran = Carbon::parse('2024-11-20 10:00:00');

        foreach ($asesorDistribution as $asesorId => $asesiGroup) {
            foreach ($asesiGroup as $asesi) {
                // Tentukan apakah asesi ini BK (Miftahul Ahmadil Khair)
                $isBK = $asesi->email === 'miftahul.ahmadil@gmail.com';

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
                    'status' => $isBK ? 4 : 5, // 4 = Tidak Kompeten, 5 = Kompeten
                    'keterangan' => $isBK ? 'Asesi dinyatakan BELUM KOMPETEN' : 'Asesi dinyatakan KOMPETEN',
                    'asesor_confirmed' => true,
                    'asesor_confirmed_at' => Carbon::parse('2024-12-05 16:00:00'),
                    'asesor_notes' => $isBK
                        ? 'Asesi belum memenuhi beberapa kriteria kompetensi yang ditetapkan'
                        : 'Asesi menunjukkan kompetensi yang baik dalam seluruh aspek penilaian',
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
                            'checked_at' => '2024-12-05 14:00:00',
                        ],
                        [
                            'formulir_id' => 2,
                            'formulir_name' => 'FR.AK.02',
                            'is_checked' => true,
                            'checked_at' => '2024-12-05 14:30:00',
                        ],
                        [
                            'formulir_id' => 3,
                            'formulir_name' => 'FR.AK.03',
                            'is_checked' => true,
                            'checked_at' => '2024-12-05 15:00:00',
                        ],
                    ],
                    'fr_ai_07_completed' => true,
                    'fr_ai_07_data' => [
                        'rekomendasi' => $isBK ? 'BK' : 'K', // BK = Belum Kompeten, K = Kompeten
                        'catatan' => $isBK
                            ? 'Belum memenuhi beberapa kriteria kompetensi'
                            : 'Memenuhi seluruh kriteria kompetensi',
                        'completed_at' => '2024-12-05 15:30:00',
                    ],
                    'hasil_akhir' => $isBK ? 'belum_kompeten' : 'kompeten',
                    'catatan_asesor' => $isBK
                        ? 'Asesi perlu meningkatkan pemahaman dan kemampuan dalam beberapa aspek Data Scientist'
                        : 'Asesi menunjukkan pemahaman yang baik dan mampu menerapkan konsep Junior / Associate Data Scientist dengan tepat',
                    'penilaian_at' => Carbon::parse('2024-12-05 15:30:00'),
                ]);

                // 4. Buat Report
                $report = Report::create([
                    'user_id' => $asesi->id,
                    'skema_id' => $skema->id,
                    'jadwal_id' => $jadwal->id,
                    'pendaftaran_id' => $pendaftaran->id,
                    'status' => $isBK ? 2 : 1, // 2 = Tidak Kompeten, 1 = Kompeten
                ]);

                $statusText = $isBK ? 'BELUM KOMPETEN' : 'KOMPETEN';
                $this->command->info("Data ujikom untuk {$asesi->name} berhasil dibuat - Status: {$statusText}");
            }
        }

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Seeder Junior / Associate Data Scientist Desember 2024 selesai!');
        $this->command->info('========================================');
        $this->command->info('Tanggal: 5 Desember 2024');
        $this->command->info('TUK: ' . $tuk->nama);
        $this->command->info('Asesor 1: ' . $asesor1->name);
        $this->command->info('Asesor 2: ' . $asesor2->name);
        $this->command->info('Total Asesi: ' . $asesiList->count() . ' orang');
        $this->command->info('Asesor 1 (' . $asesor1->name . '): 13 asesi');
        $this->command->info('Asesor 2 (' . $asesor2->name . '): 13 asesi');
        $this->command->info('Kompeten: 25 orang');
        $this->command->info('Belum Kompeten: 1 orang (Miftahul Ahmadil Khair)');
        $this->command->info('========================================');
    }
}

