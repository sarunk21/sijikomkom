<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\PendaftaranUjikom;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DistributeUjikomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ujikom:distribute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Distribusikan pendaftaran ujikom berdasarkan skema dan asesor yang tersedia';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai distribusi ujikom...');

        // Ambil pendaftaran dengan status 4 dan tanggal maksimal pendaftaran sama dengan hari ini
        $pendaftaran = Pendaftaran::where('status', 4)
            ->whereHas('jadwal', function($query) {
                $query->whereDate('tanggal_maksimal_pendaftaran', Carbon::today());
            })
            ->with(['jadwal', 'user'])
            ->get();

        if ($pendaftaran->isEmpty()) {
            $this->info('Tidak ada pendaftaran yang memenuhi kriteria untuk didistribusikan.');
            return;
        }

        $this->info('Ditemukan ' . $pendaftaran->count() . ' pendaftaran untuk didistribusikan.');

        // Ambil skema yang unik dari pendaftaran
        $skemaIds = $pendaftaran->pluck('skema_id')->unique();

        // Ambil asesor berdasarkan skema
        $asesor = User::where('user_type', 'asesor')
            ->whereIn('skema_id', $skemaIds)
            ->get();

        if ($asesor->isEmpty()) {
            $this->error('Tidak ada asesor yang tersedia untuk skema yang diperlukan.');
            return;
        }

        $this->info('Ditemukan ' . $asesor->count() . ' asesor yang tersedia.');

        // Grup pendaftaran berdasarkan skema
        $pendaftaranBySkema = $pendaftaran->groupBy('skema_id');

        $totalInserted = 0;

        foreach ($pendaftaranBySkema as $skemaId => $pendaftaranSkema) {
            $this->info("Memproses skema ID: {$skemaId}");

            // Ambil asesor untuk skema ini
            $asesorSkema = $asesor->where('skema_id', $skemaId);

            if ($asesorSkema->isEmpty()) {
                $this->warn("Tidak ada asesor untuk skema ID: {$skemaId}");
                continue;
            }

            $jumlahPendaftaran = $pendaftaranSkema->count();
            $jumlahAsesor = $asesorSkema->count();

            // Hitung berapa pendaftaran per asesor
            $pendaftaranPerAsesor = ceil($jumlahPendaftaran / $jumlahAsesor);

            $this->info("Skema {$skemaId}: {$jumlahPendaftaran} pendaftaran akan dibagi ke {$jumlahAsesor} asesor ({$pendaftaranPerAsesor} per asesor)");

            // Distribusikan pendaftaran ke asesor
            $asesorArray = $asesorSkema->toArray();
            $asesorIndex = 0;

            foreach ($pendaftaranSkema as $index => $pendaftar) {
                $asesorId = $asesorArray[$asesorIndex]['id'];

                // Cek apakah sudah ada pendaftaran ujikom untuk pendaftar ini
                $existingUjikom = PendaftaranUjikom::where('pendaftar_id', $pendaftar->id)->first();

                if ($existingUjikom) {
                    $this->warn("Pendaftaran ID {$pendaftar->id} sudah memiliki ujikom, dilewati.");
                    continue;
                }

                // Insert ke tabel PendaftaranUjikom
                PendaftaranUjikom::create([
                    'pendaftar_id' => $pendaftar->id,
                    'jadwal_id' => $pendaftar->jadwal_id,
                    'asesi_id' => $pendaftar->user_id,
                    'asesor_id' => $asesorId,
                ]);

                $totalInserted++;

                // Pindah ke asesor berikutnya jika sudah mencapai batas per asesor
                if (($index + 1) % $pendaftaranPerAsesor == 0 && $asesorIndex < count($asesorArray) - 1) {
                    $asesorIndex++;
                }
            }
        }

        $this->info("Distribusi selesai! Total {$totalInserted} pendaftaran ujikom berhasil dibuat.");
    }
}
