<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\PendaftaranUjikom;
use App\Models\Jadwal;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai distribusi ujikom...');

        // Ambil pendaftaran dengan status 4 dan tanggal maksimal pendaftaran sama dengan hari ini
        $pendaftaran = Pendaftaran::where('status', 4)
            ->whereHas('jadwal', function ($query) {
                $query->whereDate('tanggal_maksimal_pendaftaran', Carbon::today());
            })
            ->with(['jadwal', 'user'])
            ->get();

        if ($pendaftaran->isEmpty()) {
            $this->info('Tidak ada pendaftaran yang memenuhi kriteria untuk didistribusikan.');
            return;
        }

        $this->info('Ditemukan ' . $pendaftaran->count() . ' pendaftaran untuk didistribusikan.');

        // Grup pendaftaran berdasarkan skema
        $pendaftaranBySkema = $pendaftaran->groupBy('skema_id');

        $totalInserted = 0;
        $asesorWithJadwal = []; // Untuk tracking asesor yang mendapat jadwal

        foreach ($pendaftaranBySkema as $skemaId => $pendaftaranSkema) {
            Log::info("Memproses skema ID: {$skemaId}");

            // Ambil asesor AKTIF yang memiliki skema ini melalui relasi many-to-many
            $asesorSkema = User::where('user_type', 'asesor') // Hanya asesor aktif, bukan asesor_nonaktif
                ->whereHas('skema', function ($query) use ($skemaId) {
                    $query->where('skema_id', $skemaId);
                })
                ->get();

            if ($asesorSkema->isEmpty()) {
                Log::info("Tidak ada asesor aktif untuk skema ID: {$skemaId}");
                $this->warn("Tidak ada asesor aktif untuk skema ID: {$skemaId}");
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
                $existingUjikom = PendaftaranUjikom::where('pendaftaran_id', $pendaftar->id)->first();

                if ($existingUjikom) {
                    Log::info("Pendaftaran ID {$pendaftar->id} sudah memiliki ujikom, dilewati.");
                    continue;
                }

                // Insert ke tabel PendaftaranUjikom
                PendaftaranUjikom::create([
                    'pendaftaran_id' => $pendaftar->id,
                    'jadwal_id' => $pendaftar->jadwal_id,
                    'asesi_id' => $pendaftar->user_id,
                    'asesor_id' => $asesorId,
                    'status' => 6, // Menunggu Konfirmasi Asesor
                ]);

                // Track jadwal untuk asesor ini
                if (!isset($asesorWithJadwal[$asesorId])) {
                    $asesorWithJadwal[$asesorId] = [];
                }
                $asesorWithJadwal[$asesorId][] = $pendaftar->jadwal_id;

                $totalInserted++;

                // Pindah ke asesor berikutnya jika sudah mencapai batas per asesor
                if (($index + 1) % $pendaftaranPerAsesor == 0 && $asesorIndex < count($asesorArray) - 1) {
                    $asesorIndex++;
                }
            }
        }

        $this->info("Distribusi selesai! Total {$totalInserted} pendaftaran ujikom berhasil dibuat.");

        // Kirim email konfirmasi kehadiran ke asesor
        $this->sendConfirmationEmailsToAsesor($asesorWithJadwal);
    }

    /**
     * Kirim email konfirmasi kehadiran ke asesor
     */
    private function sendConfirmationEmailsToAsesor($asesorWithJadwal)
    {
        $this->info('Mengirim email konfirmasi kehadiran ke asesor...');

        $totalEmailsSent = 0;

        foreach ($asesorWithJadwal as $asesorId => $jadwalIds) {
            // Ambil data asesor
            $asesor = User::find($asesorId);
            if (!$asesor || !$asesor->email) {
                Log::info("Asesor ID {$asesorId} tidak ditemukan atau tidak memiliki email.");
                continue;
            }

            // Ambil jadwal unik untuk asesor ini
            $uniqueJadwalIds = array_unique($jadwalIds);

            foreach ($uniqueJadwalIds as $jadwalId) {
                // Ambil data jadwal
                $jadwal = Jadwal::with(['skema', 'tuk'])->find($jadwalId);
                if (!$jadwal) {
                    Log::info("Jadwal ID {$jadwalId} tidak ditemukan.");
                    continue;
                }

                // Hitung jumlah asesi untuk jadwal ini
                $jumlahAsesi = PendaftaranUjikom::where('jadwal_id', $jadwalId)
                    ->where('asesor_id', $asesorId)
                    ->count();

                try {
                    // Kirim email konfirmasi kehadiran
                    $this->emailService->sendKonfirmasiKehadiranEmail(
                        $asesor->email,
                        $asesor->name,
                        $jadwal,
                        $jumlahAsesi
                    );

                    $totalEmailsSent++;
                    Log::info("✅ Email konfirmasi terkirim ke {$asesor->name} untuk jadwal {$jadwal->skema->nama} - {$jadwal->tuk->nama}");
                } catch (\Exception $e) {
                    Log::info("❌ Gagal mengirim email ke {$asesor->name}: " . $e->getMessage());
                }
            }
        }

        $this->info("Total {$totalEmailsSent} email konfirmasi kehadiran berhasil dikirim ke asesor.");
    }
}
