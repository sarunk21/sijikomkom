<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\PendaftaranUjikom;
use App\Models\PembayaranAsesor;
use App\Models\Pembayaran;
use App\Models\Jadwal;
use App\Models\Sertif;
use App\Services\EmailService;
use App\Services\SecondRegistrationService;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        $pendaftaranMenungguVerifAsesor = Pendaftaran::where('status', 5)->count();
        $pendaftaranMenungguApprovalKelayakan = Pendaftaran::where('status', 6)->count();
        $pendaftaranTidakLulus = Pendaftaran::where('status', 7)->count();
        // Hitung pendaftaran yang menunggu pembayaran (status 8)
        // TAPI tidak memiliki pembayaran yang sudah dikonfirmasi (status 4) untuk user dan jadwal yang sama
        $pendaftaranMenungguPembayaran = Pendaftaran::where('status', 8)
            ->whereNotExists(function($query) {
                $query->select(\DB::raw(1))
                    ->from('pembayaran')
                    ->whereColumn('pembayaran.jadwal_id', 'pendaftaran.jadwal_id')
                    ->whereColumn('pembayaran.user_id', 'pendaftaran.user_id')
                    ->where('pembayaran.status', 4); // Dikonfirmasi
            })
            ->count();
        $pendaftaranMenungguUjian = Pendaftaran::where('status', 9)->count();

        $pendaftaranUjikomMenunggu = PendaftaranUjikom::where('status', 6)->count();
        $pendaftaranUjikomBerlangsung = PendaftaranUjikom::where('status', 2)->count();
        $pendaftaranUjikomSelesai = PendaftaranUjikom::where('status', 3)->count();

        // Pembayaran asesor tidak digunakan lagi
        // $pembayaranAsesorMenunggu = PembayaranAsesor::where('status', 1)->count();
        $pembayaranAsesorMenunggu = 0;
        $sertifikatAktif = Sertif::where('status', 'aktif')->count();

        // Count stuck distributions (status 12 = Asesor Tidak Dapat Hadir)
        $pendaftaranStuckDistribution = Pendaftaran::where('status', 12)->count();

        // Untuk backward compatibility
        $pendaftaranSudahSertifikat = 0;

        return view('components.pages.admin.testing.index', compact(
            'lists',
            'jadwalAktif',
            'jadwalUjianBerlangsung',
            'jadwalSelesai',
            'pendaftaranVerifikasi',
            'pendaftaranMenungguDistribusi',
            'pendaftaranMenungguVerifAsesor',
            'pendaftaranMenungguApprovalKelayakan',
            'pendaftaranTidakLulus',
            'pendaftaranMenungguPembayaran',
            'pendaftaranMenungguUjian',
            'pendaftaranUjikomMenunggu',
            'pendaftaranUjikomBerlangsung',
            'pendaftaranUjikomSelesai',
            'pembayaranAsesorMenunggu',
            'sertifikatAktif',
            'pendaftaranSudahSertifikat',
            'pendaftaranStuckDistribution'
        ));
    }

    /**
     * Trigger manual distribusi ujikom
     */
    public function triggerDistribusi()
    {
        try {
            // Ambil pendaftaran dengan status 1 (Menunggu Distribusi Asesor)
            $pendaftaran = Pendaftaran::where('status', 1)
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

                    // Update status pendaftaran ke 5 (Menunggu Verifikasi Asesor)
                    $pendaftar->update(['status' => 5]);

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
            // Update semua pendaftaran dengan status 1 atau 3 menjadi status 4 (Menunggu Distribusi Asesor)
            $updated = Pendaftaran::whereIn('status', [1, 3])
                ->update(['status' => 4]);

            if ($updated === 0) {
                return redirect()->back()->with('info', 'Tidak ada pendaftaran yang perlu diupdate.');
            }

            return redirect()->back()->with('success', "Berhasil! {$updated} pendaftaran diupdate ke status 'Menunggu Distribusi Asesor'.");
        } catch (\Exception $e) {
            Log::error('Error saat update status pendaftaran: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * AUTO APPROVE verifikasi kelayakan untuk testing
     */
    public function autoApproveKelayakan()
    {
        try {
            DB::beginTransaction();

            // Ambil pendaftaran dengan status 5 (Menunggu Verifikasi Asesor)
            $pendaftaranList = Pendaftaran::where('status', 5)->get();

            if ($pendaftaranList->isEmpty()) {
                return redirect()->back()->with('info', 'Tidak ada pendaftaran yang menunggu verifikasi asesor.');
            }

            $updated = 0;
            foreach ($pendaftaranList as $pendaftaran) {
                // Auto approve oleh asesor - set ke status 6
                $pendaftaran->update(['status' => 6]);
                
                // Simpan verifikasi kelayakan otomatis
                $pendaftaranUjikom = PendaftaranUjikom::where('pendaftaran_id', $pendaftaran->id)->first();
                if ($pendaftaranUjikom) {
                    \App\Models\KelayankanVerifikasi::create([
                        'pendaftaran_id' => $pendaftaran->id,
                        'asesor_id' => $pendaftaranUjikom->asesor_id,
                        'status' => 1, // Layak
                        'catatan' => 'Auto approved untuk testing',
                        'verified_at' => now(),
                    ]);
                }
                $updated++;
            }

            // Auto approve oleh admin - update ke status 8 (Menunggu Pembayaran)
            $pendaftaranApproval = Pendaftaran::where('status', 6)->get();
            foreach ($pendaftaranApproval as $pendaftaran) {
                $pendaftaran->update([
                    'status' => 8,
                    'kelayakan_status' => 1,
                    'kelayakan_verified_at' => now(),
                ]);

                // Buat pembayaran
                Pembayaran::create([
                    'user_id' => $pendaftaran->user_id,
                    'jadwal_id' => $pendaftaran->jadwal_id,
                    'status' => 1, // Belum Bayar
                    'keterangan' => 'Pembayaran Ujikom - Auto Created',
                ]);
                $updated++;
            }

            DB::commit();

            return redirect()->back()->with('success', "Berhasil auto approve {$updated} verifikasi kelayakan dan membuat pembayaran!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error auto approve kelayakan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * AUTO VERIFY pembayaran untuk testing
     */
    public function autoVerifyPembayaran()
    {
        try {
            // Update pembayaran dengan status 1 (Belum Bayar) menjadi status 4 (Dikonfirmasi)
            $updated = Pembayaran::where('status', 1)->update([
                'status' => 4,
                'keterangan' => 'Auto verified untuk testing'
            ]);

            // Update pendaftaran dari status 8 ke status 9 (Menunggu Ujian)
            $updatedPendaftaran = Pendaftaran::where('status', 8)->update(['status' => 9]);

            if ($updated === 0) {
                return redirect()->back()->with('info', 'Tidak ada pembayaran yang perlu diverifikasi.');
            }

            return redirect()->back()->with('success', "Berhasil! {$updated} pembayaran diverifikasi dan {$updatedPendaftaran} pendaftaran siap untuk ujian.");
        } catch (\Exception $e) {
            Log::error('Error auto verify pembayaran: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update status jadwal menjadi Ujian Berlangsung
     */
    public function startJadwal()
    {
        try {
            // Get jadwal yang akan distart
            $jadwals = Jadwal::where('status', 1)
                ->whereHas('pendaftaran', function($query) {
                    $query->whereHas('pendaftaranUjikom');
                })
                ->get();

            if ($jadwals->isEmpty()) {
                return redirect()->back()->with('info', 'Tidak ada jadwal yang perlu distart.');
            }

            $updated = 0;
            foreach ($jadwals as $jadwal) {
                // Update jadwal ke status 3 (Ujian Berlangsung)
                $jadwal->update(['status' => 3]);
                
                // Update status Pendaftaran dari 9 (Menunggu Ujian) ke 10 (Ujian Berlangsung)
                Pendaftaran::where('jadwal_id', $jadwal->id)
                    ->where('status', 9)
                    ->update(['status' => 10]);
                
                // Update semua PendaftaranUjikom di jadwal ini langsung ke status 2 (Ujikom Berlangsung)
                PendaftaranUjikom::where('jadwal_id', $jadwal->id)
                    ->whereIn('status', [1, 6]) // Dari Belum Ujikom atau Menunggu Konfirmasi
                    ->update(['status' => 2]); // Ke Ujikom Berlangsung
                
                $updated++;
            }

            return redirect()->back()->with('success', "Berhasil! {$updated} jadwal dimulai. Semua asesi di jadwal tersebut sekarang dapat mengisi formulir.");
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
            // Get jadwal yang akan diselesaikan
            $jadwals = Jadwal::where('status', 3)->get();

            if ($jadwals->isEmpty()) {
                return redirect()->back()->with('info', 'Tidak ada jadwal yang berlangsung.');
            }

            $updated = 0;
            foreach ($jadwals as $jadwal) {
                // Update jadwal ke status 4 (Selesai)
                $jadwal->update(['status' => 4]);
                
                // Update semua PendaftaranUjikom di jadwal ini ke status selesai jika masih berlangsung
                PendaftaranUjikom::where('jadwal_id', $jadwal->id)
                    ->where('status', 2) // Masih Ujikom Berlangsung
                    ->update(['status' => 3]); // Ke Ujikom Selesai
                
                $updated++;
            }

            // Update status pendaftaran terkait menjadi selesai
            Pendaftaran::whereHas('jadwal', function($query) {
                $query->where('status', 4);
            })->update(['status' => 6]);

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
                ->with(['pendaftaran.user', 'pendaftaran.skema', 'asesor'])
                ->get();

            if ($pendaftaranUjikomSelesai->isEmpty()) {
                return redirect()->back()->with('info', 'Tidak ada ujikom yang sudah selesai untuk diupload sertifikatnya.');
            }

            $totalUploaded = 0;
            $totalAsesiLulus = 0;

            foreach ($pendaftaranUjikomSelesai as $ujikom) {
                $pendaftaran = $ujikom->pendaftaran;
                $user = $pendaftaran->user;
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

                    Log::info("Sertifikat dibuat untuk asesi: {$user->name}, Nomor: {$sertifikat->nomor_sertifikat}");
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
     * Fix stuck payments - Auto approve "Pendaftaran Pertama" yang stuck di status 2
     */
    public function fixStuckPayments()
    {
        try {
            DB::beginTransaction();

            // Find pembayaran "Pendaftaran Pertama" yang stuck di status 2 (Menunggu Verifikasi)
            $stuckPayments = Pembayaran::where('status', 2)
                ->where('keterangan', 'Pendaftaran Pertama')
                ->whereNull('bukti_pembayaran')
                ->with(['jadwal', 'user'])
                ->get();

            if ($stuckPayments->isEmpty()) {
                DB::rollBack();
                return redirect()->back()->with('info', 'Tidak ada pembayaran yang stuck.');
            }

            $fixed = 0;
            $failed = [];

            foreach ($stuckPayments as $payment) {
                try {
                    // Update status pembayaran ke 4 (Dikonfirmasi)
                    $payment->update([
                        'status' => 4,
                        'keterangan' => 'Pendaftaran Pertama - Auto Fixed'
                    ]);

                    // Create pendaftaran jika belum ada
                    $existingPendaftaran = Pendaftaran::where('user_id', $payment->user_id)
                        ->where('jadwal_id', $payment->jadwal_id)
                        ->first();

                    if (!$existingPendaftaran) {
                        Pendaftaran::create([
                            'user_id' => $payment->user_id,
                            'jadwal_id' => $payment->jadwal_id,
                            'skema_id' => $payment->jadwal->skema_id,
                            'tuk_id' => $payment->jadwal->tuk_id,
                            'status' => 1 // Menunggu Verifikasi Kaprodi
                        ]);
                    }

                    $fixed++;
                } catch (\Exception $e) {
                    $failed[] = "Payment ID {$payment->id}: " . $e->getMessage();
                    Log::error("Failed to fix payment {$payment->id}: " . $e->getMessage());
                }
            }

            DB::commit();

            $message = "Berhasil fix {$fixed} pembayaran stuck.";
            if (!empty($failed)) {
                $message .= " Gagal: " . count($failed) . " pembayaran. Cek log untuk detail.";
                return redirect()->back()->with('warning', $message);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error fixing stuck payments: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Fix stuck distributions - Redistribute pendaftaran dengan status 7 (Asesor Tidak Dapat Hadir)
     */
    public function fixStuckDistributions()
    {
        try {
            DB::beginTransaction();

            // Find pendaftaran yang stuck di status 7 (Asesor Tidak Dapat Hadir)
            $stuckPendaftaran = Pendaftaran::where('status', 7)
                ->with(['jadwal', 'user', 'skema'])
                ->get();

            if ($stuckPendaftaran->isEmpty()) {
                DB::rollBack();
                return redirect()->back()->with('info', 'Tidak ada pendaftaran yang stuck di status 7.');
            }

            $redistributed = 0;
            $failed = [];

            foreach ($stuckPendaftaran as $pendaftaran) {
                try {
                    $jadwalId = $pendaftaran->jadwal_id;
                    $jadwal = $pendaftaran->jadwal;

                    if (!$jadwal) {
                        $failed[] = "Pendaftaran ID {$pendaftaran->id}: Jadwal tidak ditemukan";
                        continue;
                    }

                    // Cari asesor yang pernah menolak pendaftaran ini
                    $rejectedAsesorIds = DB::table('asesor_rejection_histories')
                        ->where('pendaftaran_id', $pendaftaran->id)
                        ->pluck('asesor_id')
                        ->toArray();

                    // Cari asesor alternatif untuk skema ini yang:
                    // 1. Punya sertifikasi untuk skema ini
                    // 2. Belum pernah menolak pendaftaran ini
                    // 3. Status aktif
                    // 4. Workload paling rendah untuk jadwal ini
                    $alternativeAsesor = User::where('user_type', 'asesor')
                        ->whereHas('skemas', function($query) use ($pendaftaran) {
                            $query->where('skema_id', $pendaftaran->skema_id);
                        })
                        ->whereNotIn('id', $rejectedAsesorIds)
                        ->whereNotExists(function($query) use ($jadwalId) {
                            // Exclude asesor yang sudah confirmed untuk jadwal ini
                            $query->select(DB::raw(1))
                                ->from('pendaftaran_ujikom')
                                ->whereColumn('pendaftaran_ujikom.asesor_id', 'users.id')
                                ->where('pendaftaran_ujikom.jadwal_id', $jadwalId)
                                ->where('pendaftaran_ujikom.asesor_confirmed', true);
                        })
                        ->withCount(['pendaftaranUjikom as workload_count' => function($query) use ($jadwalId) {
                            // Hitung workload untuk jadwal ini
                            $query->where('jadwal_id', $jadwalId);
                        }])
                        ->orderBy('workload_count', 'asc') // Pilih asesor dengan workload paling sedikit
                        ->first();

                    if ($alternativeAsesor) {
                        // Create assignment baru dengan asesor pengganti
                        PendaftaranUjikom::create([
                            'pendaftaran_id' => $pendaftaran->id,
                            'jadwal_id' => $jadwalId,
                            'asesi_id' => $pendaftaran->user_id,
                            'asesor_id' => $alternativeAsesor->id,
                            'status' => 6, // Menunggu Konfirmasi Asesor
                            'asesor_confirmed' => false,
                        ]);

                        // Update status pendaftaran kembali ke status 4 (Menunggu Ujian)
                        $pendaftaran->update([
                            'status' => 4,
                            'keterangan' => "Redistribusi otomatis ke asesor {$alternativeAsesor->name}"
                        ]);

                        Log::info("✅ Redistribusi berhasil: Pendaftaran ID {$pendaftaran->id} (Asesi: {$pendaftaran->user->name}) ditugaskan ke asesor {$alternativeAsesor->name} (ID: {$alternativeAsesor->id}) untuk jadwal ID {$jadwalId}");

                        // Send email to new asesor
                        try {
                            $this->emailService->sendKonfirmasiKehadiranEmail(
                                $alternativeAsesor->email,
                                $alternativeAsesor->name,
                                $jadwal,
                                1 // Jumlah asesi
                            );
                            Log::info("📧 Email konfirmasi kehadiran dikirim ke {$alternativeAsesor->email}");
                        } catch (\Exception $e) {
                            Log::warning("⚠️ Email redistribusi gagal dikirim ke {$alternativeAsesor->email}: " . $e->getMessage());
                        }

                        $redistributed++;
                    } else {
                        // Tidak ada asesor alternatif tersedia
                        $failed[] = "Pendaftaran ID {$pendaftaran->id} (Asesi: {$pendaftaran->user->name}): Tidak ada asesor pengganti yang tersedia";
                        Log::warning("❌ Tidak ada asesor alternatif yang tersedia untuk pendaftaran ID {$pendaftaran->id} (Skema: {$pendaftaran->skema->nama}, Jadwal ID: {$jadwalId})");
                    }

                } catch (\Exception $e) {
                    $failed[] = "Pendaftaran ID {$pendaftaran->id}: " . $e->getMessage();
                    Log::error("Failed to redistribute pendaftaran {$pendaftaran->id}: " . $e->getMessage());
                }
            }

            DB::commit();

            $message = "Berhasil redistribusi {$redistributed} dari {$stuckPendaftaran->count()} pendaftaran yang stuck.";
            if (!empty($failed)) {
                $message .= "<br><strong>Gagal:</strong><br>" . implode('<br>', $failed);
                return redirect()->back()->with('warning', $message);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error fixing stuck distributions: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
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

