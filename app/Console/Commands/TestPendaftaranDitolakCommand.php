<?php

namespace App\Console\Commands;

use App\Mail\PendaftaranDitolakMail;
use App\Models\Pendaftaran;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestPendaftaranDitolakCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-pendaftaran-ditolak {--pendaftaran_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test pengiriman email notifikasi pendaftaran ditolak';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendaftaranId = $this->option('pendaftaran_id');

        if ($pendaftaranId) {
            $pendaftaran = Pendaftaran::with(['user', 'jadwal.skema', 'jadwal.tuk'])->find($pendaftaranId);
            if (!$pendaftaran) {
                $this->error('Pendaftaran dengan ID ' . $pendaftaranId . ' tidak ditemukan');
                return 1;
            }
        } else {
            $pendaftaran = Pendaftaran::with(['user', 'jadwal.skema', 'jadwal.tuk'])->first();
            if (!$pendaftaran) {
                $this->error('Tidak ada pendaftaran di database');
                return 1;
            }
        }

        if (!$pendaftaran->user) {
            $this->error('Pendaftaran tidak memiliki user yang terkait');
            return 1;
        }

        $this->info('Mengirim email test pendaftaran ditolak ke: ' . $pendaftaran->user->email);
        $this->info('Nama Asesi: ' . $pendaftaran->user->name);
        $this->info('Skema: ' . $pendaftaran->jadwal->skema->nama);

        try {
            Mail::to($pendaftaran->user->email)->send(new PendaftaranDitolakMail($pendaftaran));
            $this->info('Email berhasil dikirim ke: ' . $pendaftaran->user->email);
        } catch (\Exception $e) {
            $this->error('Gagal mengirim email: ' . $e->getMessage());
            return 1;
        }

        $this->info('Test pengiriman email pendaftaran ditolak selesai!');
        $this->info('Cek inbox Mailtrap untuk melihat email yang dikirim.');

        return 0;
    }
}
