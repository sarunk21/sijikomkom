<?php

namespace App\Console\Commands;

use App\Mail\AsesorTidakHadirMail;
use App\Mail\AsesorTidakHadirAdminMail;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestAsesorTidakHadirEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:asesor-tidak-hadir-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notifikasi asesor tidak hadir';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        // Ambil jadwal pertama untuk testing
        $jadwal = Jadwal::with(['skema', 'tuk'])->first();

        if (!$jadwal) {
            $this->error('Tidak ada jadwal yang ditemukan. Silakan buat jadwal terlebih dahulu.');
            return 1;
        }

        // Ambil user pertama sebagai asesor
        $asesor = User::first();

        if (!$asesor) {
            $this->error('Tidak ada user yang ditemukan. Silakan buat user terlebih dahulu.');
            return 1;
        }

        // Data untuk email
        $emailData = [
            'jadwal' => $jadwal,
            'asesor' => $asesor,
            'alasan' => 'Ini adalah test email - Asesor sedang sakit dan tidak dapat hadir',
            'tanggal_ujian' => \Carbon\Carbon::parse($jadwal->tanggal_ujian)->format('d-m-Y'),
            'skema' => $jadwal->skema->nama,
            'tuk' => $jadwal->tuk->nama
        ];

        try {
            $this->info('Mengirim email test ke: ' . $email);

            // Kirim email test untuk asesi
            Mail::to($email)->send(new AsesorTidakHadirMail('Test Asesi', $emailData));
            $this->info('✅ Email untuk asesi berhasil dikirim');

            // Kirim email test untuk admin
            Mail::to($email)->send(new AsesorTidakHadirAdminMail('Test Admin', $emailData));
            $this->info('✅ Email untuk admin berhasil dikirim');

            $this->info('🎉 Semua email test berhasil dikirim!');

        } catch (\Exception $e) {
            $this->error('❌ Error mengirim email: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
