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

class SystemAnalystJuliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data skema System Analyst
        $skema = Skema::where('kode', 'SA')->first();

        // Ambil TUK Laboratorium AI & Robotics (TUK5)
        $tuk = Tuk::where('kode', 'TUK5')->first();

        if (!$skema || !$tuk) {
            $this->command->error('Skema System Analyst atau TUK tidak ditemukan!');
            return;
        }

        // Buat jadwal ujian tanggal 1-7 Juli 2025
        $jadwal = Jadwal::create([
            'skema_id' => $skema->id,
            'tuk_id' => $tuk->id,
            'tanggal_ujian' => '2025-07-01',
            'tanggal_selesai' => '2025-07-07',
            'tanggal_maksimal_pendaftaran' => '2025-06-24',
            'status' => 4, // Selesai
            'kuota' => 40,
            'keterangan' => 'Ujian Sertifikasi System Analyst - Juli 2025 (40 Peserta)',
        ]);

        // Daftar 40 asesi dari kelompok 2 (no 6-29) dan kelompok 3 (no 30-45)
        $asesiEmails = [
            // Kelompok 2 (24 orang)
            'zara.zyasky@gmail.com',
            'adelia.deswita@gmail.com',
            'thoriq.abdurachman@gmail.com',
            'nugraha.adhitama@gmail.com',
            'andhara.carol@gmail.com',
            'ivan.ahmad@gmail.com',
            'ananda.sahinaz@gmail.com',
            'damar.nurfadhil@gmail.com',
            'olga.silvia@gmail.com',
            'danendra.satya@gmail.com',
            'ahmad.roin@gmail.com',
            'alya.triananda@gmail.com',
            'farhah.safrila@gmail.com',
            'fildzah.arista@gmail.com',
            'ghania.syakira@gmail.com',
            'putu.widhi@gmail.com',
            'mahdiyyah.febriana@gmail.com',
            'syaikhu.fadhli@gmail.com',
            'tegar.hartoto@gmail.com',
            'yoshiki.citra@gmail.com',
            'ahmad.farriz@gmail.com',
            'dina.oktaviana@gmail.com',
            'adinda.nazwa@gmail.com',
            'zhafirah.aqilah@gmail.com',

            // Kelompok 3 (16 orang)
            'samuel.halomoan@gmail.com',
            'farras.ilman@gmail.com',
            'rifqi.fauzan@gmail.com',
            'lisa.febriyanti@gmail.com',
            'abdu.hafizh@gmail.com',
            'farrel.alfaqih@gmail.com',
            'rizal.alief@gmail.com',
            'sendi.wildanto@gmail.com',
            'sazkia.alifiah@gmail.com',
            'patricia.nessa@gmail.com',
            'difa.ananda@gmail.com',
            'fathir.deny@gmail.com',
            'aura.rachmawaty@gmail.com',
            'ikhsan.fathirizky@gmail.com',
            'farhan.abubakar@gmail.com',
            'rafli.alfiardi@gmail.com',
        ];

        $asesiList = User::whereIn('email', $asesiEmails)->get();

        if ($asesiList->count() !== 40) {
            $this->command->error('Jumlah asesi tidak sesuai! Ditemukan: ' . $asesiList->count());
            return;
        }

        // Ambil asesor Sarika
        $asesor = User::where('email', 'sarika@asesor.com')->first();

        if (!$asesor) {
            $this->command->error('Asesor Sarika tidak ditemukan!');
            return;
        }

        // Buat data pendaftaran, pendaftaran ujikom, penilaian, dan report untuk setiap asesi
        // Set tanggal pendaftaran di bulan Juni 2025
        $tanggalPendaftaran = Carbon::parse('2025-06-15 10:00:00');

        foreach ($asesiList as $asesi) {
            // 1. Buat Pendaftaran
            $pendaftaran = Pendaftaran::create([
                'jadwal_id' => $jadwal->id,
                'user_id' => $asesi->id,
                'skema_id' => $skema->id,
                'tuk_id' => $tuk->id,
                'status' => 6, // Selesai
                'keterangan' => 'Ujian telah selesai dilaksanakan',
                'custom_variables' => null,
                'ttd_asesi_path' => null,
                'ttd_asesor_path' => null,
                'asesor_assessment' => null,
                'asesor_data' => [
                    'asesor_id' => $asesor->id,
                    'asesor_name' => $asesor->name,
                ],
                'created_at' => $tanggalPendaftaran,
                'updated_at' => $tanggalPendaftaran,
            ]);

            // 2. Buat Pendaftaran Ujikom
            $pendaftaranUjikom = PendaftaranUjikom::create([
                'pendaftaran_id' => $pendaftaran->id,
                'jadwal_id' => $jadwal->id,
                'asesi_id' => $asesi->id,
                'asesor_id' => $asesor->id,
                'status' => 5, // Kompeten
                'keterangan' => 'Asesi dinyatakan KOMPETEN',
                'asesor_confirmed' => true,
                'asesor_confirmed_at' => Carbon::parse('2025-07-07 16:00:00'),
                'asesor_notes' => 'Asesi menunjukkan kompetensi yang baik dalam seluruh aspek penilaian',
            ]);

            // 3. Buat Asesi Penilaian
            $asesiPenilaian = AsesiPenilaian::create([
                'jadwal_id' => $jadwal->id,
                'user_id' => $asesi->id,
                'asesor_id' => $asesor->id,
                'formulir_status' => [
                    [
                        'formulir_id' => 1,
                        'formulir_name' => 'FR.AK.01',
                        'is_checked' => true,
                        'checked_at' => '2025-07-07 14:00:00',
                    ],
                    [
                        'formulir_id' => 2,
                        'formulir_name' => 'FR.AK.02',
                        'is_checked' => true,
                        'checked_at' => '2025-07-07 14:30:00',
                    ],
                    [
                        'formulir_id' => 3,
                        'formulir_name' => 'FR.AK.03',
                        'is_checked' => true,
                        'checked_at' => '2025-07-07 15:00:00',
                    ],
                ],
                'fr_ai_07_completed' => true,
                'fr_ai_07_data' => [
                    'rekomendasi' => 'K', // Kompeten
                    'catatan' => 'Memenuhi seluruh kriteria kompetensi',
                    'completed_at' => '2025-07-07 15:30:00',
                ],
                'hasil_akhir' => 'kompeten',
                'catatan_asesor' => 'Asesi menunjukkan pemahaman yang baik dan mampu menerapkan konsep System Analyst dengan tepat',
                'penilaian_at' => Carbon::parse('2025-07-07 15:30:00'),
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

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Seeder System Analyst Juli 2025 selesai!');
        $this->command->info('========================================');
        $this->command->info('Tanggal: 1-7 Juli 2025');
        $this->command->info('TUK: ' . $tuk->nama);
        $this->command->info('Asesor: ' . $asesor->name);
        $this->command->info('Total Asesi: ' . $asesiList->count() . ' orang');
        $this->command->info('Semua asesi dinyatakan KOMPETEN');
        $this->command->info('========================================');
    }
}
