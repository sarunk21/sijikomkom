<?php

namespace App\Console\Commands;

use App\Models\Jadwal;
use App\Models\Pendaftaran;
use App\Models\PendaftaranUjikom;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateJadwalStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update jadwal status based on exam time and date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        Log::info($now);

        // Update jadwal yang sedang berlangsung (status 3)
        $jadwalBerlangsung = Jadwal::where('status', 1) // Aktif
            ->whereDate('tanggal_ujian', '<=', $now)
            ->whereDate('tanggal_ujian', '>=', $now->copy()->subDay())
            ->get();

        foreach ($jadwalBerlangsung as $jadwal) {
            $jadwal->update(['status' => 3]); // Ujian Berlangsung
            $this->info("Jadwal ID {$jadwal->id} diupdate ke status Ujian Berlangsung");

            // Update status pendaftaran yang terkait dengan jadwal berlangsung
            $pendaftaran = Pendaftaran::where('jadwal_id', $jadwal->id)->where('status', 4)->get();
            foreach ($pendaftaran as $p) {
                $p->status = 5;
                $p->save();
                $this->info("Pendaftaran ID {$p->id} diupdate ke status Ujian Berlangsung");
            }

            // Update status pendaftaran ujikom yang terkait dengan jadwal berlangsung
            $pendaftaranUjikom = PendaftaranUjikom::where('jadwal_id', $jadwal->id)->where('status', 4)->get();
            foreach ($pendaftaranUjikom as $p) {
                $p->status = 5;
                $p->save();
                $this->info("Pendaftaran Ujikom ID {$p->id} diupdate ke status Ujian Berlangsung");
            }
        }

        // Update jadwal yang sudah selesai (status 4)
        $jadwalSelesai = Jadwal::whereIn('status', [1, 3]) // Aktif atau Ujian Berlangsung
            ->where('tanggal_ujian', '<', $now->copy()->startOfDay()) // Hari sudah berganti
            ->get();

        foreach ($jadwalSelesai as $jadwal) {
            $jadwal->update(['status' => 4]); // Selesai
            $this->info("Jadwal ID {$jadwal->id} diupdate ke status Selesai");
        }

        $this->info('Jadwal status update completed!');
    }
}
