<?php

namespace App\Console\Commands;

use App\Mail\KonfirmasiKehadiranMail;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestKonfirmasiKehadiranEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:konfirmasi-kehadiran-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email konfirmasi kehadiran asesor';

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

        // Jumlah asesi dummy untuk testing
        $jumlahAsesi = 5;

        try {
            $this->info('Mengirim email test konfirmasi kehadiran ke: ' . $email);

            // Kirim email test konfirmasi kehadiran
            Mail::to($email)->send(new KonfirmasiKehadiranMail($asesor->name, $jadwal, $jumlahAsesi));
            $this->info('✅ Email konfirmasi kehadiran berhasil dikirim');

            $this->info('🎉 Email test berhasil dikirim!');

        } catch (\Exception $e) {
            $this->error('❌ Error mengirim email: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
