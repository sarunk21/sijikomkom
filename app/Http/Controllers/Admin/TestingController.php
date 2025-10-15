<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\PendaftaranUjikom;
use App\Models\PembayaranAsesor;
use App\Models\Jadwal;
use App\Models\Sertif;
use App\Services\EmailService;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestingController extends Controller
{
    use MenuTrait;

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Tampilkan halaman testing tools
     */
    public function index()
    {
        $lists = $this->getMenuListAdmin('testing');

        // Statistik untuk ditampilkan
        $jadwalAktif = Jadwal::where('status', 1)->count();
        $jadwalUjianBerlangsung = Jadwal::where('status', 3)->count();
        $jadwalSelesai = Jadwal::where('status', 4)->count();

        $pendaftaranVerifikasi = Pendaftaran::whereIn('status', [1, 3])->count();
        $pendaftaranMenungguDistribusi = Pendaftaran::where('status', 4)->count();

        $pendaftaranUjikomMenunggu = PendaftaranUjikom::where('status', 6)->count();
        $pendaftaranUjikomBerlangsung = PendaftaranUjikom::where('status', 2)->count();
        $pendaftaranUjikomSelesai = PendaftaranUjikom::where('status', 3)->count();

        $pembayaranAsesorMenunggu = PembayaranAsesor::where('status', 1)->count();
        $sertifikatAktif = Sertif::where('status', 'aktif')->count();
        $pendaftaranSudahSertifikat = Pendaftaran::where('status', 7)->count();

        return view('components.pages.admin.testing.index', compact(
            'lists',
            'jadwalAktif',
            'jadwalUjianBerlangsung',
            'jadwalSelesai',
            'pendaftaranVerifikasi',
            'pendaftaranMenungguDistribusi',
            'pendaftaranUjikomMenunggu',
            'pendaftaranUjikomBerlangsung',
            'pendaftaranUjikomSelesai',
            'pembayaranAsesorMenunggu',
            'sertifikatAktif',
            'pendaftaranSudahSertifikat'
        ));
    }

    /**
     * Trigger manual distribusi ujikom
     */
    public function triggerDistribusi()
    {
        try {
            // Ambil pendaftaran dengan status 4 (Menunggu Ujian)
            $pendaftaran = Pendaftaran::where('status', 4)
                ->with(['jadwal', 'user'])
                ->get();

            if ($pendaftaran->isEmpty()) {
                return redirect()->back()->with('info', 'Tidak ada pendaftaran yang perlu didistribusikan.');
            }

            // Grup pendaftaran berdasarkan skema
            $pendaftaranBySkema = $pendaftaran->groupBy('skema_id');

            $totalInserted = 0;
            $asesorWithJadwal = [];

            foreach ($pendaftaranBySkema as $skemaId => $pendaftaranSkema) {
                // Ambil asesor AKTIF yang memiliki skema ini
                $asesorSkema = User::where('user_type', 'asesor')
                    ->whereHas('skemas', function ($query) use ($skemaId) {
                        $query->where('skema_id', $skemaId);
                    })
                    ->get();

                if ($asesorSkema->isEmpty()) {
                    Log::info("Tidak ada asesor aktif untuk skema ID: {$skemaId}");
                    continue;
                }

                $jumlahPendaftaran = $pendaftaranSkema->count();
                $jumlahAsesor = $asesorSkema->count();
                $pendaftaranPerAsesor = ceil($jumlahPendaftaran / $jumlahAsesor);

                $asesorArray = $asesorSkema->toArray();
                $asesorIndex = 0;

                foreach ($pendaftaranSkema as $index => $pendaftar) {
                    $asesorId = $asesorArray[$asesorIndex]['id'];

                    // Cek apakah sudah ada pendaftaran ujikom
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

                    // Track jadwal untuk asesor
                    if (!isset($asesorWithJadwal[$asesorId])) {
                        $asesorWithJadwal[$asesorId] = [];
                    }
                    $asesorWithJadwal[$asesorId][] = $pendaftar->jadwal_id;

                    $totalInserted++;

                    // Pindah ke asesor berikutnya
                    if (($index + 1) % $pendaftaranPerAsesor == 0 && $asesorIndex < count($asesorArray) - 1) {
                        $asesorIndex++;
                    }
                }
            }

            // Kirim email konfirmasi ke asesor
            $this->sendConfirmationEmailsToAsesor($asesorWithJadwal);

            return redirect()->back()->with('success', "Distribusi berhasil! Total {$totalInserted} pendaftaran ujikom telah dibuat.");
        } catch (\Exception $e) {
            Log::error('Error saat trigger distribusi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Trigger manual pembayaran asesor untuk jadwal yang sudah selesai
     */
    public function triggerPembayaranAsesor()
    {
        try {
            // Ambil jadwal yang sudah selesai (status 4)
            $jadwalSelesai = Jadwal::where('status', 4)->get();

            if ($jadwalSelesai->isEmpty()) {
                return redirect()->back()->with('info', 'Tidak ada jadwal yang sudah selesai.');
            }

            $totalCreated = 0;

            foreach ($jadwalSelesai as $jadwal) {
                // Ambil asesor unik yang menguji di jadwal ini
                $asesorIds = PendaftaranUjikom::where('jadwal_id', $jadwal->id)
                    ->distinct()
                    ->pluck('asesor_id');

                foreach ($asesorIds as $asesorId) {
                    // Cek apakah pembayaran sudah dibuat
                    $existingPembayaran = PembayaranAsesor::where('asesor_id', $asesorId)
                        ->where('jadwal_id', $jadwal->id)
                        ->first();

                    if (!$existingPembayaran) {
                        PembayaranAsesor::create([
                            'asesor_id' => $asesorId,
                            'jadwal_id' => $jadwal->id,
                            'status' => 1, // Menunggu Pembayaran
                        ]);
                        $totalCreated++;
                    }
                }
            }

            return redirect()->back()->with('success', "Pembayaran asesor berhasil dibuat! Total {$totalCreated} pembayaran baru.");
        } catch (\Exception $e) {
            Log::error('Error saat trigger pembayaran asesor: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update status pendaftaran untuk testing (lolos verifikasi)
     */
    public function updateStatusPendaftaran()
    {
        try {
            // Update semua pendaftaran dengan status 1 atau 3 menjadi status 4 (Menunggu Ujian)
            $updated = Pendaftaran::whereIn('status', [1, 3])
                ->update(['status' => 4]);

            if ($updated === 0) {
                return redirect()->back()->with('info', 'Tidak ada pendaftaran yang perlu diupdate.');
            }

            return redirect()->back()->with('success', "Berhasil! {$updated} pendaftaran diupdate ke status 'Menunggu Ujian'.");
        } catch (\Exception $e) {
            Log::error('Error saat update status pendaftaran: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update status jadwal menjadi Ujian Berlangsung
     */
    public function startJadwal()
    {
        try {
            // Update jadwal aktif yang memiliki ujikom menjadi status 3 (Ujian Berlangsung)
            $updated = Jadwal::where('status', 1)
                ->whereHas('pendaftaran', function($query) {
                    $query->whereHas('pendaftaranUjikom');
                })
                ->update(['status' => 3]);

            // Update status PendaftaranUjikom dari 6 (Menunggu Konfirmasi) ke 1 (Belum Ujikom)
            PendaftaranUjikom::where('status', 6)->update(['status' => 1]);

            if ($updated === 0) {
                return redirect()->back()->with('info', 'Tidak ada jadwal yang perlu distart.');
            }

            return redirect()->back()->with('success', "Berhasil! {$updated} jadwal dimulai (Ujian Berlangsung).");
        } catch (\Exception $e) {
            Log::error('Error saat start jadwal: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Simulasi ujikom berlangsung (asesor verifikasi peserta)
     */
    public function simulasiUjikom()
    {
        try {
            // Update PendaftaranUjikom dari status 1 (Belum Ujikom) ke 2 (Ujikom Berlangsung)
            $updated = PendaftaranUjikom::where('status', 1)
                ->update(['status' => 2]);

            if ($updated === 0) {
                return redirect()->back()->with('info', 'Tidak ada ujikom yang perlu disimulasikan.');
            }

            return redirect()->back()->with('success', "Berhasil! {$updated} ujikom dimulai (Ujikom Berlangsung).");
        } catch (\Exception $e) {
            Log::error('Error saat simulasi ujikom: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Selesaikan ujikom (simulasi selesai ujian)
     */
    public function selesaikanUjikom()
    {
        try {
            // Update PendaftaranUjikom dari status 2 (Ujikom Berlangsung) ke 3 (Ujikom Selesai)
            $updated = PendaftaranUjikom::where('status', 2)
                ->update(['status' => 3]);

            if ($updated === 0) {
                return redirect()->back()->with('info', 'Tidak ada ujikom yang berlangsung.');
            }

            return redirect()->back()->with('success', "Berhasil! {$updated} ujikom diselesaikan.");
        } catch (\Exception $e) {
            Log::error('Error saat selesaikan ujikom: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Selesaikan jadwal (trigger otomatis pembayaran asesor)
     */
    public function selesaikanJadwal()
    {
        try {
            // Update jadwal yang ujian berlangsung menjadi selesai
            $updated = Jadwal::where('status', 3)
                ->update(['status' => 4]);

            // Update status pendaftaran terkait menjadi selesai
            Pendaftaran::whereHas('jadwal', function($query) {
                $query->where('status', 4);
            })->update(['status' => 6]);

            if ($updated === 0) {
                return redirect()->back()->with('info', 'Tidak ada jadwal yang berlangsung.');
            }

            return redirect()->back()->with('success', "Berhasil! {$updated} jadwal diselesaikan. Silakan trigger pembayaran asesor.");
        } catch (\Exception $e) {
            Log::error('Error saat selesaikan jadwal: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Kirim email konfirmasi kehadiran ke asesor
     */
    private function sendConfirmationEmailsToAsesor($asesorWithJadwal)
    {
        $totalEmailsSent = 0;

        foreach ($asesorWithJadwal as $asesorId => $jadwalIds) {
            $asesor = User::find($asesorId);
            if (!$asesor || !$asesor->email) {
                continue;
            }

            $uniqueJadwalIds = array_unique($jadwalIds);

            foreach ($uniqueJadwalIds as $jadwalId) {
                $jadwal = Jadwal::with(['skema', 'tuk'])->find($jadwalId);
                if (!$jadwal) {
                    continue;
                }

                $jumlahAsesi = PendaftaranUjikom::where('jadwal_id', $jadwalId)
                    ->where('asesor_id', $asesorId)
                    ->count();

                try {
                    $this->emailService->sendKonfirmasiKehadiranEmail(
                        $asesor->email,
                        $asesor->name,
                        $jadwal,
                        $jumlahAsesi
                    );
                    $totalEmailsSent++;
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim email ke {$asesor->name}: " . $e->getMessage());
                }
            }
        }

        Log::info("Total {$totalEmailsSent} email konfirmasi terkirim.");
    }

    /**
     * Upload sertifikat bertanda tangan untuk asesi yang lulus
     */
    public function uploadSertifikat()
    {
        try {
            // Ambil pendaftaran ujikom yang sudah selesai (status 3)
            $pendaftaranUjikomSelesai = PendaftaranUjikom::where('status', 3)
                ->with(['pendaftaran.asesi', 'pendaftaran.skema', 'asesor'])
                ->get();

            if ($pendaftaranUjikomSelesai->isEmpty()) {
                return redirect()->back()->with('info', 'Tidak ada ujikom yang sudah selesai untuk diupload sertifikatnya.');
            }

            $totalUploaded = 0;
            $totalAsesiLulus = 0;

            foreach ($pendaftaranUjikomSelesai as $ujikom) {
                $pendaftaran = $ujikom->pendaftaran;
                $asesi = $pendaftaran->asesi;
                $skema = $pendaftaran->skema;
                $asesor = $ujikom->asesor;

                // Simulasi: semua asesi lulus (untuk testing)
                $lulus = true; // Dalam real case, ini bisa dari penilaian asesor

                if ($lulus) {
                    // Buat sertifikat
                    $sertifikat = Sertif::create([
                        'pendaftaran_id' => $pendaftaran->id,
                        'asesor_id' => $asesor->id,
                        'nomor_sertifikat' => $this->generateNomorSertifikat($skema->kode),
                        'tanggal_terbit' => now()->toDateString(),
                        'tanggal_expired' => now()->addYears(3)->toDateString(), // Sertifikat berlaku 3 tahun
                        'file_path' => 'sertifikat/' . $pendaftaran->id . '_' . time() . '.pdf', // Simulasi path file
                        'status' => 'aktif',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Update status pendaftaran menjadi sudah sertifikat
                    $pendaftaran->update(['status' => 7]); // Status 7 = Sudah Sertifikat

                    // Update status pendaftaran ujikom
                    $ujikom->update(['status' => 4]); // Status 4 = Sudah Sertifikat

                    $totalUploaded++;
                    $totalAsesiLulus++;

                    Log::info("Sertifikat dibuat untuk asesi: {$asesi->name}, Nomor: {$sertifikat->nomor_sertifikat}");
                }
            }

            return redirect()->back()->with('success',
                "Upload sertifikat berhasil! {$totalUploaded} sertifikat dibuat untuk {$totalAsesiLulus} asesi yang lulus."
            );

        } catch (\Exception $e) {
            Log::error('Error saat upload sertifikat: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate nomor sertifikat
     */
    private function generateNomorSertifikat($kodeSkema)
    {
        $tahun = date('Y');
        $bulan = date('m');

        // Hitung jumlah sertifikat yang sudah ada tahun ini
        $count = Sertif::whereYear('created_at', $tahun)->count() + 1;

        return "{$kodeSkema}/{$tahun}/{$bulan}/" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}

