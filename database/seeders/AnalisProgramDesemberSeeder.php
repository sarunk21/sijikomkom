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

class AnalisProgramDesemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data skema Analis Program
        $skema = Skema::where('kode', 'AP')->first();

        // Ambil TUK FIKLAB 302
        $tuk = Tuk::where('kode', 'TUK9')->first();

        if (!$skema || !$tuk) {
            $this->command->error('Skema Analis Program atau TUK tidak ditemukan!');
            return;
        }

        // Buat jadwal ujian tanggal 2 Desember 2024
        $jadwal = Jadwal::create([
            'skema_id' => $skema->id,
            'tuk_id' => $tuk->id,
            'tanggal_ujian' => '2024-12-02',
            'tanggal_selesai' => '2024-12-02',
            'tanggal_maksimal_pendaftaran' => '2024-11-25',
            'status' => 4, // Selesai
            'kuota' => 2,
            'keterangan' => 'Ujian Sertifikasi Analis Program - 2 Desember 2024',
        ]);

        // Ambil 2 asesi
        $asesiEmails = [
            'aurel.izzety@gmail.com',
            'deandra.satriyo@gmail.com',
        ];

        $asesiList = User::whereIn('email', $asesiEmails)->get();

        if ($asesiList->count() !== 2) {
            $this->command->error('Jumlah asesi tidak sesuai! Ditemukan: ' . $asesiList->count());
            return;
        }

        // Ambil asesor
        $asesor1 = User::where('email', 'panji.muslim@asesor.com')->first();
        $asesor2 = User::where('email', 'nurhuda.maulana@asesor.com')->first();

        if (!$asesor1 || !$asesor2) {
            $this->command->error('Asesor tidak ditemukan!');
            return;
        }

        // Distribute asesi ke asesor (1 asesi per asesor)
        $asesorDistribution = [
            $asesor1->id => [$asesiList[0]], // Panji - Aurel Izzety
            $asesor2->id => [$asesiList[1]], // Nurhuda - Deandra Satriyo Setiawan
        ];

        // Buat data pendaftaran, pendaftaran ujikom, penilaian, dan report untuk setiap asesi
        // Set tanggal pendaftaran di bulan November 2024
        $tanggalPendaftaran = Carbon::parse('2024-11-20 10:00:00');

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
                    'asesor_confirmed_at' => Carbon::parse('2024-12-02 16:00:00'),
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
                            'checked_at' => '2024-12-02 14:00:00',
                        ],
                        [
                            'formulir_id' => 2,
                            'formulir_name' => 'FR.AK.02',
                            'is_checked' => true,
                            'checked_at' => '2024-12-02 14:30:00',
                        ],
                        [
                            'formulir_id' => 3,
                            'formulir_name' => 'FR.AK.03',
                            'is_checked' => true,
                            'checked_at' => '2024-12-02 15:00:00',
                        ],
                    ],
                    'fr_ai_07_completed' => true,
                    'fr_ai_07_data' => [
                        'rekomendasi' => 'K', // Kompeten
                        'catatan' => 'Memenuhi seluruh kriteria kompetensi',
                        'completed_at' => '2024-12-02 15:30:00',
                    ],
                    'hasil_akhir' => 'kompeten',
                    'catatan_asesor' => 'Asesi menunjukkan pemahaman yang baik dan mampu menerapkan konsep Analis Program dengan tepat',
                    'penilaian_at' => Carbon::parse('2024-12-02 15:30:00'),
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
        $this->command->info('Seeder Analis Program Desember 2024 selesai!');
        $this->command->info('========================================');
        $this->command->info('Tanggal: 2 Desember 2024');
        $this->command->info('TUK: ' . $tuk->nama);
        $this->command->info('Asesor 1: ' . $asesor1->name);
        $this->command->info('Asesor 2: ' . $asesor2->name);
        $this->command->info('Total Asesi: ' . $asesiList->count() . ' orang');
        $this->command->info('Asesor 1 (' . $asesor1->name . '): 1 asesi');
        $this->command->info('Asesor 2 (' . $asesor2->name . '): 1 asesi');
        $this->command->info('Semua asesi dinyatakan KOMPETEN');
        $this->command->info('========================================');
    }
}

