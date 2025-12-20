<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Pendaftaran;
use App\Models\Report;
use App\Models\PendaftaranUjikom;
use App\Models\AsesorRejectionHistory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index(Request $request)
    {
        $user = Auth::user();

        // Filter parameters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $skemaId = $request->input('skema_id');

        // ========== KPI METRICS ==========

        // Total Asesi yang Dinilai (lifetime)
        $totalAsesiDinilai = PendaftaranUjikom::where('asesor_id', $user->id)
            ->where('status', 5) // Status 5 = Kompeten (selesai dinilai)
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('updated_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('updated_at', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->whereHas('pendaftaran', function($sq) use ($skemaId) {
                    $sq->where('skema_id', $skemaId);
                });
            })
            ->count();

        // Total Asesi Bulan Ini
        $asesiBulanIni = PendaftaranUjikom::where('asesor_id', $user->id)
            ->where('status', 5)
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // Hitung persentase perubahan dari bulan lalu
        $asesiBulanLalu = PendaftaranUjikom::where('asesor_id', $user->id)
            ->where('status', 5)
            ->whereMonth('updated_at', now()->subMonth()->month)
            ->whereYear('updated_at', now()->subMonth()->year)
            ->count();
        $perubahanAsesi = $asesiBulanLalu > 0
            ? round((($asesiBulanIni - $asesiBulanLalu) / $asesiBulanLalu) * 100)
            : ($asesiBulanIni > 0 ? 100 : 0);

        // Tingkat Kelulusan (Pass Rate) - persentase asesi yang kompeten
        $totalKompeten = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
            $query->where('asesor_id', $user->id);
        })
        ->where('status', 1)
        ->when($startDate, function($q) use ($startDate) {
            return $q->whereDate('created_at', '>=', $startDate);
        })
        ->when($endDate, function($q) use ($endDate) {
            return $q->whereDate('created_at', '<=', $endDate);
        })
        ->when($skemaId, function($q) use ($skemaId) {
            return $q->where('skema_id', $skemaId);
        })
        ->count();

        $totalReport = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
            $query->where('asesor_id', $user->id);
        })
        ->when($startDate, function($q) use ($startDate) {
            return $q->whereDate('created_at', '>=', $startDate);
        })
        ->when($endDate, function($q) use ($endDate) {
            return $q->whereDate('created_at', '<=', $endDate);
        })
        ->when($skemaId, function($q) use ($skemaId) {
            return $q->where('skema_id', $skemaId);
        })
        ->count();

        $tingkatKelulusan = $totalReport > 0 ? round(($totalKompeten / $totalReport) * 100) : 0;

        // Jadwal Aktif/Upcoming (yang perlu dikonfirmasi + yang sedang berjalan)
        $jadwalAktif = PendaftaranUjikom::where('asesor_id', $user->id)
            ->where(function($q) {
                $q->where('asesor_confirmed', false) // Belum konfirmasi
                  ->orWhereHas('jadwal', function($sq) {
                      $sq->where('status', 3); // Atau sedang berlangsung
                  });
            })
            ->distinct('jadwal_id')
            ->count('jadwal_id');

        // Total Skema yang Dikuasai
        $totalSkema = \DB::table('asesor_skema')
            ->where('asesor_id', $user->id)
            ->count();

        // Total Jadwal Selesai (jadwal yang sudah dinilai dan selesai)
        $totalJadwalSelesai = PendaftaranUjikom::where('asesor_id', $user->id)
            ->where('status', 5) // Status 5 = Kompeten (selesai dinilai)
            ->distinct('jadwal_id')
            ->count('jadwal_id');

        // Rata-rata Asesi per Jadwal (berapa rata-rata asesi yang dinilai per jadwal)
        $avgAsesiPerJadwal = $totalJadwalSelesai > 0
            ? round($totalAsesiDinilai / $totalJadwalSelesai, 1)
            : 0;

        // Avg Waktu Penilaian (rata-rata berapa lama asesor menilai per asesi)
        // Hitung dari created_at pendaftaran_ujikom sampai updated_at report
        $avgWaktuPenilaian = \DB::table('report')
            ->join('pendaftaran', 'report.pendaftaran_id', '=', 'pendaftaran.id')
            ->join('pendaftaran_ujikom', 'pendaftaran.id', '=', 'pendaftaran_ujikom.pendaftaran_id')
            ->where('pendaftaran_ujikom.asesor_id', $user->id)
            ->whereNotNull('report.updated_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, pendaftaran_ujikom.created_at, report.updated_at)) as avg_hours')
            ->value('avg_hours');
        $avgWaktuPenilaian = $avgWaktuPenilaian ? round($avgWaktuPenilaian, 1) : 0;

        // ========== ANALYTICS DATA ==========

        // Trend Penilaian (12 bulan terakhir) - kompeten vs tidak kompeten
        $trendPenilaian = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);

            $kompeten = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
                $query->where('asesor_id', $user->id);
            })
            ->where('status', 1)
            ->whereMonth('created_at', $bulan->month)
            ->whereYear('created_at', $bulan->year)
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();

            $tidakKompeten = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
                $query->where('asesor_id', $user->id);
            })
            ->where('status', 0)
            ->whereMonth('created_at', $bulan->month)
            ->whereYear('created_at', $bulan->year)
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();

            $trendPenilaian[] = [
                'bulan' => $bulan->format('M Y'),
                'kompeten' => $kompeten,
                'tidak_kompeten' => $tidakKompeten,
                'total' => $kompeten + $tidakKompeten,
                'persentase_lulus' => ($kompeten + $tidakKompeten) > 0
                    ? round(($kompeten / ($kompeten + $tidakKompeten)) * 100, 1)
                    : 0
            ];
        }

        // Distribusi per Skema (skema mana yang paling banyak dinilai)
        $distribusiSkema = \DB::table('pendaftaran_ujikom')
            ->join('pendaftaran', 'pendaftaran_ujikom.pendaftaran_id', '=', 'pendaftaran.id')
            ->join('skema', 'pendaftaran.skema_id', '=', 'skema.id')
            ->where('pendaftaran_ujikom.asesor_id', $user->id)
            ->where('pendaftaran_ujikom.status', 5) // Status 5 = Kompeten (selesai dinilai)
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('pendaftaran_ujikom.updated_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('pendaftaran_ujikom.updated_at', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('pendaftaran.skema_id', $skemaId);
            })
            ->select('skema.nama', \DB::raw('COUNT(*) as jumlah'))
            ->groupBy('skema.nama')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        // Workload Analysis (beban kerja per bulan)
        $workloadAnalysis = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $count = PendaftaranUjikom::where('asesor_id', $user->id)
                ->whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->when($skemaId, function($q) use ($skemaId) {
                    return $q->whereHas('pendaftaran', function($sq) use ($skemaId) {
                        $sq->where('skema_id', $skemaId);
                    });
                })
                ->count();
            $workloadAnalysis[] = [
                'bulan' => $bulan->format('M'),
                'jumlah' => $count
            ];
        }

        // Performance Summary (statistik keseluruhan)
        $performanceSummary = [
            'total_kompeten' => $totalKompeten,
            'total_tidak_kompeten' => $totalReport - $totalKompeten,
            'pass_rate' => $tingkatKelulusan,
            'total_penilaian' => $totalReport
        ];

        // Pending confirmations untuk alert
        $pendingConfirmations = PendaftaranUjikom::where('asesor_id', $user->id)
            ->where('asesor_confirmed', false)
            ->whereHas('jadwal', function($query) {
                $query->where('status', 1)
                    ->where('tanggal_ujian', '>', now());
            })
            ->with(['jadwal', 'jadwal.skema', 'jadwal.tuk'])
            ->get()
            ->groupBy('jadwal_id')
            ->map(function($items) {
                $first = $items->first();
                return [
                    'jadwal_id' => $first->jadwal_id,
                    'jadwal' => $first->jadwal,
                    'jumlah_asesi' => $items->count(),
                ];
            })
            ->values();

        // Upcoming Jadwal (jadwal mendatang yang sudah confirmed)
        $upcomingJadwal = PendaftaranUjikom::where('asesor_id', $user->id)
            ->where('asesor_confirmed', true)
            ->whereHas('jadwal', function($query) {
                $query->where('status', 1)
                    ->where('tanggal_ujian', '>=', now());
            })
            ->with(['jadwal.skema', 'jadwal.tuk'])
            ->get()
            ->groupBy('jadwal_id')
            ->map(function($items) {
                $first = $items->first();
                return [
                    'jadwal' => $first->jadwal,
                    'jumlah_asesi' => $items->count(),
                ];
            })
            ->sortBy('jadwal.tanggal_ujian')
            ->take(5)
            ->values();

        $lists = $this->getMenuListAsesor('dashboard');

        // Get skema yang dikuasai asesor untuk filter dropdown
        $skemas = \App\Models\Skema::whereHas('asesors', function($query) use ($user) {
            $query->where('asesor_id', $user->id);
        })->orderBy('nama', 'asc')->get();

        return view('components.pages.asesor.dashboard', compact(
            'lists',
            'totalAsesiDinilai',
            'asesiBulanIni',
            'perubahanAsesi',
            'tingkatKelulusan',
            'jadwalAktif',
            'totalSkema',
            'totalJadwalSelesai',
            'avgAsesiPerJadwal',
            'avgWaktuPenilaian',
            'trendPenilaian',
            'distribusiSkema',
            'workloadAnalysis',
            'performanceSummary',
            'pendingConfirmations',
            'upcomingJadwal',
            // Filter data
            'skemas',
            'startDate',
            'endDate',
            'skemaId'
        ));
    }

    public function confirmJadwal(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'status' => 'required|in:confirmed,rejected',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $jadwalId = $request->jadwal_id;

        // Ambil semua pendaftaran ujikom untuk jadwal ini yang assigned ke asesor ini
        $pendaftaranUjikomList = PendaftaranUjikom::where('jadwal_id', $jadwalId)
            ->where('asesor_id', $user->id)
            ->where('asesor_confirmed', false)
            ->with(['jadwal', 'asesi'])
            ->get();

        if ($pendaftaranUjikomList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada jadwal yang perlu dikonfirmasi atau sudah dikonfirmasi sebelumnya.'
            ], 404);
        }

        // Cek apakah jadwal sudah dimulai
        $jadwal = $pendaftaranUjikomList->first()->jadwal;
        if ($jadwal->tanggal_ujian <= now()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat konfirmasi karena ujian sudah dimulai atau sudah lewat.'
            ], 400);
        }

        // Update confirmation status
        if ($request->status === 'confirmed') {
            // Konfirmasi semua asesi untuk jadwal ini
            foreach ($pendaftaranUjikomList as $pendaftaranUjikom) {
                $pendaftaranUjikom->asesor_confirmed = true;
                $pendaftaranUjikom->asesor_confirmed_at = now();
                $pendaftaranUjikom->asesor_notes = $request->notes;
                $pendaftaranUjikom->save();
            }

            \Log::info("Asesor {$user->name} (ID: {$user->id}) konfirmasi hadir untuk jadwal ID {$jadwalId} dengan {$pendaftaranUjikomList->count()} asesi");

            return response()->json([
                'success' => true,
                'message' => "Konfirmasi berhasil untuk {$pendaftaranUjikomList->count()} asesi pada jadwal ini."
            ]);
        } else {
            // Rejected - simpan history, ubah status pendaftaran, dan hapus assignment
            foreach ($pendaftaranUjikomList as $pendaftaranUjikom) {
                // Simpan ke rejection history
                AsesorRejectionHistory::create([
                    'pendaftaran_id' => $pendaftaranUjikom->pendaftaran_id,
                    'jadwal_id' => $pendaftaranUjikom->jadwal_id,
                    'asesor_id' => $user->id,
                    'notes' => $request->notes ?? 'Tidak dapat hadir'
                ]);

                // Update status pendaftaran menjadi status 7 (Asesor Tidak Dapat Hadir)
                $pendaftaran = Pendaftaran::find($pendaftaranUjikom->pendaftaran_id);
                if ($pendaftaran) {
                    $pendaftaran->status = 7;
                    $pendaftaran->keterangan = $request->notes ?? 'Asesor tidak dapat hadir';
                    $pendaftaran->save();
                }

                // Hapus assignment
                $pendaftaranUjikom->delete();
            }

            // Log untuk audit
            \Log::info("Asesor {$user->name} (ID: {$user->id}) menolak jadwal ID {$jadwalId} dengan {$pendaftaranUjikomList->count()} asesi. Alasan: " . ($request->notes ?? 'Tidak dapat hadir'));

            // Trigger redistribusi otomatis untuk jadwal ini
            $this->autoRedistributeAsesor($jadwalId, $pendaftaranUjikomList);

            return response()->json([
                'success' => true,
                'message' => 'Penolakan berhasil diproses. Sistem akan mencari asesor pengganti untuk jadwal ini.'
            ]);
        }
    }

    /**
     * Auto redistribute asesor untuk pendaftaran yang ditolak
     */
    private function autoRedistributeAsesor($jadwalId, $pendaftaranUjikomList)
    {
        try {
            $rejectedAsesorId = Auth::id();
            $jadwal = Jadwal::with(['skema', 'tuk'])->find($jadwalId);

            if (!$jadwal) {
                \Log::error("Jadwal ID {$jadwalId} tidak ditemukan untuk redistribusi");
                return;
            }

            foreach ($pendaftaranUjikomList as $pendaftaranUjikom) {
                $pendaftaran = Pendaftaran::with('skema')->find($pendaftaranUjikom->pendaftaran_id);

                if (!$pendaftaran) {
                    \Log::warning("Pendaftaran ID {$pendaftaranUjikom->pendaftaran_id} tidak ditemukan");
                    continue;
                }

                // Cari asesor alternatif untuk skema ini yang:
                // 1. Punya sertifikasi untuk skema ini
                // 2. Belum pernah menolak pendaftaran ini
                // 3. Belum assigned untuk jadwal ini (atau confirmed)
                $alternativeAsesor = \App\Models\User::where('user_type', 'asesor')
                    ->whereHas('skemas', function($query) use ($pendaftaran) {
                        $query->where('skema_id', $pendaftaran->skema_id);
                    })
                    ->whereNotIn('id', function($query) use ($pendaftaran) {
                        // Exclude asesor yang sudah pernah menolak pendaftaran ini
                        $query->select('asesor_id')
                            ->from('asesor_rejection_histories')
                            ->where('pendaftaran_id', $pendaftaran->id);
                    })
                    ->where('id', '!=', $rejectedAsesorId) // Exclude asesor yang baru menolak
                    ->whereNotExists(function($query) use ($jadwalId) {
                        // Exclude asesor yang sudah confirmed untuk jadwal ini
                        $query->select(\DB::raw(1))
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
                    $pendaftaran->status = 4;
                    $pendaftaran->keterangan = "Menunggu konfirmasi asesor pengganti: {$alternativeAsesor->name}";
                    $pendaftaran->save();

                    \Log::info("✅ Redistribusi berhasil: Pendaftaran ID {$pendaftaran->id} (Asesi: {$pendaftaran->user->name}) ditugaskan ke asesor {$alternativeAsesor->name} (ID: {$alternativeAsesor->id}) untuk jadwal ID {$jadwalId}");

                    // Send email to new asesor
                    try {
                        $emailService = app(\App\Services\EmailService::class);
                        $emailService->sendKonfirmasiKehadiranEmail(
                            $alternativeAsesor->email,
                            $alternativeAsesor->name,
                            $jadwal,
                            1 // Jumlah asesi
                        );
                        \Log::info("📧 Email konfirmasi kehadiran dikirim ke {$alternativeAsesor->email}");
                    } catch (\Exception $e) {
                        \Log::warning("⚠️ Email redistribusi gagal dikirim ke {$alternativeAsesor->email}: " . $e->getMessage());
                    }
                } else {
                    // Tidak ada asesor alternatif tersedia
                    \Log::warning("❌ Tidak ada asesor alternatif yang tersedia untuk pendaftaran ID {$pendaftaran->id} (Skema: {$pendaftaran->skema->nama}, Jadwal ID: {$jadwalId}). Status tetap 7.");

                    // Update keterangan pendaftaran
                    $pendaftaran->keterangan = 'Tidak ada asesor pengganti yang tersedia. Silakan hubungi admin.';
                    $pendaftaran->save();
                }
            }

            \Log::info("🔄 Proses redistribusi selesai untuk jadwal ID {$jadwalId}. Total {$pendaftaranUjikomList->count()} asesi diproses.");

        } catch (\Exception $e) {
            \Log::error("❌ Error saat auto-redistribusi untuk jadwal ID {$jadwalId}: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
        }
    }

    private function getStatusText($status)
    {
        $statusMap = [
            1 => 'Menunggu Verifikasi Kaprodi',
            2 => 'Tidak Lolos Verifikasi Kaprodi',
            3 => 'Menunggu Verifikasi Admin',
            4 => 'Menunggu Ujian',
            5 => 'Ujian Berlangsung',
            6 => 'Selesai',
            7 => 'Asesor Tidak Dapat Hadir',
        ];

        return $statusMap[$status] ?? 'Tidak Diketahui';
    }
}
