<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\Skema;
use App\Models\Jadwal;
use App\Models\Report;
use App\Models\PendaftaranUjikom;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        // ==========================================
        // 1. EXECUTIVE KPIs (High-Level Overview)
        // ==========================================

        // Total Pendaftaran (All-time)
        $totalPendaftaran = Pendaftaran::count();

        // Total Asesi Unik
        $totalAsesi = Pendaftaran::distinct('user_id')->count('user_id');

        // Total Skema Aktif
        $totalSkema = Skema::count();

        // Total Asesor Aktif
        $totalAsesor = User::where('user_type', 'asesor')->count();

        // Total Jadwal (All-time)
        $totalJadwal = Jadwal::count();

        // Total TUK
        $totalTuk = DB::table('tuk')->count();

        // Tingkat Keberhasilan Keseluruhan (Pass Rate)
        $totalKompeten = Report::where('status', 1)->count();
        $totalTidakKompeten = Report::where('status', 0)->count();
        $totalUjikom = $totalKompeten + $totalTidakKompeten;
        $passRate = $totalUjikom > 0
            ? round(($totalKompeten / $totalUjikom) * 100, 1)
            : 0;

        // Utilisasi Kapasitas (% jadwal yang terisi)
        $jadwalTerisi = Jadwal::whereHas('pendaftaran')->count();
        $utilisasiKapasitas = $totalJadwal > 0
            ? round(($jadwalTerisi / $totalJadwal) * 100, 1)
            : 0;

        // ==========================================
        // 2. GROWTH & TREND METRICS
        // ==========================================

        // Pendaftaran Bulan Ini vs Bulan Lalu
        $pendaftaranBulanIni = Pendaftaran::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $pendaftaranBulanLalu = Pendaftaran::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $growthRate = $pendaftaranBulanLalu > 0
            ? round((($pendaftaranBulanIni - $pendaftaranBulanLalu) / $pendaftaranBulanLalu) * 100, 1)
            : ($pendaftaranBulanIni > 0 ? 100 : 0);

        // Trend Pendaftaran (12 bulan terakhir)
        $trendPendaftaran = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $count = Pendaftaran::whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->count();
            $trendPendaftaran[] = [
                'bulan' => $bulan->format('M Y'),
                'jumlah' => $count
            ];
        }

        // ==========================================
        // 3. PERFORMANCE ANALYTICS
        // ==========================================

        // Pass Rate Trend (12 bulan terakhir)
        $passRateTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $kompeten = Report::whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->where('status', 1)
                ->count();
            $tidakKompeten = Report::whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->where('status', 0)
                ->count();
            $total = $kompeten + $tidakKompeten;
            $rate = $total > 0 ? round(($kompeten / $total) * 100, 1) : 0;

            $passRateTrend[] = [
                'bulan' => $bulan->format('M Y'),
                'pass_rate' => $rate,
                'kompeten' => $kompeten,
                'tidak_kompeten' => $tidakKompeten
            ];
        }

        // Top Performing Skema by Pass Rate
        $topSkemaByPassRate = Report::select('report.skema_id',
                DB::raw('COUNT(*) as total_ujian'),
                DB::raw('SUM(CASE WHEN report.status = 1 THEN 1 ELSE 0 END) as total_kompeten'))
            ->with('skema')
            ->groupBy('report.skema_id')
            ->havingRaw('COUNT(*) >= 3') // Minimal 3 ujian
            ->get()
            ->map(function ($item) {
                $passRate = $item->total_ujian > 0
                    ? round(($item->total_kompeten / $item->total_ujian) * 100, 1)
                    : 0;
                return [
                    'nama' => $item->skema->nama ?? 'Unknown',
                    'total_ujian' => $item->total_ujian,
                    'pass_rate' => $passRate
                ];
            })
            ->sortByDesc('pass_rate')
            ->take(5)
            ->values();

        // ==========================================
        // 4. SKEMA ANALYTICS
        // ==========================================

        // Distribusi Pendaftaran per Skema (Top 5)
        $distribusiSkema = Pendaftaran::select('skema_id', DB::raw('count(*) as total'))
            ->with('skema')
            ->groupBy('skema_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'nama' => $item->skema->nama ?? 'Unknown',
                    'total' => $item->total
                ];
            });

        // Skema Growth (12 bulan terakhir untuk top 3 skema)
        $topSkemaIds = $distribusiSkema->take(3)->pluck('nama')->toArray();
        $skemaGrowthTrend = [];

        foreach ($topSkemaIds as $skemaNama) {
            $skemaData = [];
            $skema = Skema::where('nama', $skemaNama)->first();

            if ($skema) {
                for ($i = 11; $i >= 0; $i--) {
                    $bulan = now()->subMonths($i);
                    $count = Pendaftaran::where('skema_id', $skema->id)
                        ->whereMonth('created_at', $bulan->month)
                        ->whereYear('created_at', $bulan->year)
                        ->count();
                    $skemaData[] = $count;
                }
                $skemaGrowthTrend[] = [
                    'nama' => $skemaNama,
                    'data' => $skemaData
                ];
            }
        }

        // ==========================================
        // 5. ASESOR ANALYTICS
        // ==========================================

        // Top 10 Asesor by Workload
        $topAsesor = DB::table('pendaftaran_ujikom')
            ->join('users', 'pendaftaran_ujikom.asesor_id', '=', 'users.id')
            ->select('users.name as nama', DB::raw('COUNT(pendaftaran_ujikom.id) as total_asesi'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_asesi', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'nama' => $item->nama,
                    'total' => $item->total_asesi
                ];
            });

        // Workload Distribution (berapa asesor yang handle berapa asesi)
        $workloadDistribution = DB::table('pendaftaran_ujikom')
            ->select('asesor_id', DB::raw('COUNT(*) as total_asesi'))
            ->groupBy('asesor_id')
            ->get()
            ->groupBy(function($item) {
                if ($item->total_asesi <= 10) return '1-10 Asesi';
                if ($item->total_asesi <= 20) return '11-20 Asesi';
                if ($item->total_asesi <= 30) return '21-30 Asesi';
                if ($item->total_asesi <= 40) return '31-40 Asesi';
                return '40+ Asesi';
            })
            ->map(function($group) {
                return $group->count();
            })
            ->sortKeys();

        // ==========================================
        // 6. OPERATIONAL EFFICIENCY METRICS
        // ==========================================

        // Rata-rata Waktu dari Pendaftaran ke Ujian (dalam hari)
        $avgTimeToExam = Pendaftaran::whereIn('pendaftaran.status', [4, 5, 6])
            ->whereNotNull('pendaftaran.jadwal_id')
            ->join('jadwal', 'pendaftaran.jadwal_id', '=', 'jadwal.id')
            ->whereRaw('jadwal.tanggal_ujian > pendaftaran.created_at')
            ->selectRaw('AVG(DATEDIFF(jadwal.tanggal_ujian, pendaftaran.created_at)) as avg_days')
            ->value('avg_days');
        $avgTimeToExam = $avgTimeToExam ? round($avgTimeToExam, 1) : 0;

        // Status Pipeline Distribution
        $statusPipeline = [
            'Menunggu Verifikasi' => Pendaftaran::where('status', 1)->count(),
            'Ditolak' => Pendaftaran::where('status', 2)->count(),
            'Menunggu Verifikasi Admin' => Pendaftaran::where('status', 3)->count(),
            'Menunggu Ujian' => Pendaftaran::where('status', 4)->count(),
            'Ujian Berlangsung' => Pendaftaran::where('status', 5)->count(),
            'Selesai' => Pendaftaran::where('status', 6)->count(),
        ];

        // ==========================================
        // 7. DEMOGRAPHIC ANALYTICS
        // ==========================================

        // Segmentasi Jenis Kelamin
        $segmentasiGender = User::whereIn('id', function($query) {
                $query->select('user_id')->from('pendaftaran')->distinct();
            })
            ->select('jenis_kelamin', DB::raw('COUNT(id) as jumlah'))
            ->whereNotNull('jenis_kelamin')
            ->where('jenis_kelamin', '!=', '')
            ->groupBy('jenis_kelamin')
            ->get()
            ->mapWithKeys(function($item) {
                $label = $item->jenis_kelamin == 'L' ? 'Laki-laki' :
                        ($item->jenis_kelamin == 'P' ? 'Perempuan' : $item->jenis_kelamin);
                return [$label => $item->jumlah];
            });

        // ==========================================
        // 8. EXECUTIVE INSIGHTS (Rule-Based AI)
        // ==========================================

        $insights = $this->generateExecutiveInsights([
            'passRate' => $passRate,
            'growthRate' => $growthRate,
            'utilisasiKapasitas' => $utilisasiKapasitas,
            'avgTimeToExam' => $avgTimeToExam,
            'totalPendaftaran' => $totalPendaftaran,
            'pendaftaranBulanIni' => $pendaftaranBulanIni,
            'workloadDistribution' => $workloadDistribution,
            'statusPipeline' => $statusPipeline
        ]);

        $lists = $this->getMenuListPimpinan('dashboard');

        return view('components.pages.pimpinan.dashboard', compact(
            'lists',
            // Executive KPIs
            'totalPendaftaran',
            'totalAsesi',
            'totalSkema',
            'totalAsesor',
            'totalJadwal',
            'totalTuk',
            'passRate',
            'utilisasiKapasitas',
            // Growth Metrics
            'pendaftaranBulanIni',
            'growthRate',
            'trendPendaftaran',
            // Performance Analytics
            'passRateTrend',
            'topSkemaByPassRate',
            // Skema Analytics
            'distribusiSkema',
            'skemaGrowthTrend',
            // Asesor Analytics
            'topAsesor',
            'workloadDistribution',
            // Operational Metrics
            'avgTimeToExam',
            'statusPipeline',
            // Demographics
            'segmentasiGender',
            // AI Insights
            'insights'
        ));
    }

    /**
     * Generate Executive Insights based on comprehensive data analysis
     */
    private function generateExecutiveInsights($data)
    {
        $insights = [];

        // Insight 1: Pass Rate Analysis
        if ($data['passRate'] >= 90) {
            $insights[] = [
                'type' => 'success',
                'icon' => 'fa-trophy',
                'title' => 'Excellent Pass Rate',
                'message' => "Tingkat kelulusan {$data['passRate']}% menunjukkan kualitas program sertifikasi yang sangat baik."
            ];
        } elseif ($data['passRate'] >= 75) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'fa-check-circle',
                'title' => 'Good Pass Rate',
                'message' => "Tingkat kelulusan {$data['passRate']}% berada di kategori baik. Pertahankan kualitas ini."
            ];
        } else {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'fa-exclamation-triangle',
                'title' => 'Pass Rate Perlu Perhatian',
                'message' => "Tingkat kelulusan {$data['passRate']}% perlu ditingkatkan. Review kurikulum dan metode asesmen."
            ];
        }

        // Insight 2: Growth Analysis
        if ($data['growthRate'] > 20) {
            $insights[] = [
                'type' => 'success',
                'icon' => 'fa-chart-line',
                'title' => 'Pertumbuhan Signifikan',
                'message' => "Pendaftaran tumbuh {$data['growthRate']}% bulan ini. Momentum positif yang luar biasa!"
            ];
        } elseif ($data['growthRate'] < -10) {
            $insights[] = [
                'type' => 'danger',
                'icon' => 'fa-arrow-down',
                'title' => 'Penurunan Pendaftaran',
                'message' => "Pendaftaran turun {$data['growthRate']}%. Perlu strategi marketing dan engagement lebih aktif."
            ];
        }

        // Insight 3: Capacity Utilization
        if ($data['utilisasiKapasitas'] < 60) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'fa-calendar-times',
                'title' => 'Utilisasi Kapasitas Rendah',
                'message' => "Hanya {$data['utilisasiKapasitas']}% jadwal terisi. Optimalkan penjadwalan dan promosi."
            ];
        } elseif ($data['utilisasiKapasitas'] > 90) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'fa-calendar-check',
                'title' => 'Kapasitas Hampir Penuh',
                'message' => "Utilisasi {$data['utilisasiKapasitas']}%. Pertimbangkan penambahan jadwal atau TUK."
            ];
        }

        // Insight 4: Operational Efficiency
        if ($data['avgTimeToExam'] > 30) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'fa-clock',
                'title' => 'Proses Terlalu Lama',
                'message' => "Rata-rata waktu pendaftaran ke ujian {$data['avgTimeToExam']} hari. Percepat proses verifikasi."
            ];
        }

        // Insight 5: Workload Balance
        $highWorkload = $data['workloadDistribution']['40+ Asesi'] ?? 0;
        if ($highWorkload > 0) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'fa-balance-scale',
                'title' => 'Distribusi Beban Kerja',
                'message' => "{$highWorkload} asesor menangani lebih dari 40 asesi. Pertimbangkan redistribusi untuk kualitas penilaian."
            ];
        }

        // Insight 6: Pipeline Health
        $menungguVerifikasi = $data['statusPipeline']['Menunggu Verifikasi'] ?? 0;
        if ($menungguVerifikasi > 10) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'fa-hourglass-half',
                'title' => 'Backlog Verifikasi',
                'message' => "{$menungguVerifikasi} pendaftaran menunggu verifikasi. Percepat proses approval untuk kepuasan asesi."
            ];
        }

        return $insights;
    }
}
