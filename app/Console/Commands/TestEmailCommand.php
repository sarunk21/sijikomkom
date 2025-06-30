<?php

namespace App\Console\Commands;

use App\Mail\JadwalBaruMail;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {--jadwal_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test pengiriman email notifikasi jadwal baru';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jadwalId = $this->option('jadwal_id');

        if ($jadwalId) {
            $jadwal = Jadwal::with('skema', 'tuk')->find($jadwalId);
            if (!$jadwal) {
                $this->error('Jadwal dengan ID ' . $jadwalId . ' tidak ditemukan');
                return 1;
            }
        } else {
            $jadwal = Jadwal::with('skema', 'tuk')->first();
            if (!$jadwal) {
                $this->error('Tidak ada jadwal di database');
                return 1;
            }
        }

        $kepalaTukUsers = User::where('user_type', 'kepala_tuk')->get();

        if ($kepalaTukUsers->isEmpty()) {
            $this->error('Tidak ada user dengan tipe kepala_tuk');
            return 1;
        }

        $this->info('Mengirim email test ke ' . $kepalaTukUsers->count() . ' kepala TUK...');

        foreach ($kepalaTukUsers as $user) {
            try {
                Mail::to($user->email)->send(new JadwalBaruMail($jadwal));
                $this->info('Email berhasil dikirim ke: ' . $user->email);
            } catch (\Exception $e) {
                $this->error('Gagal mengirim email ke ' . $user->email . ': ' . $e->getMessage());
            }
        }

        $this->info('Test pengiriman email selesai!');
        $this->info('Cek inbox Mailtrap untuk melihat email yang dikirim.');

        return 0;
    }
}
