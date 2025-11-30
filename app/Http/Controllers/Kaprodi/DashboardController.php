<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\Skema;
use App\Models\Jadwal;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        $lists = $this->getMenuListKaprodi('dashboard');

        // ==========================================
        // 1. KEY PERFORMANCE INDICATORS (KPIs)
        // ==========================================

        // Total Pendaftaran
        $totalPendaftaran = Pendaftaran::count();

        // Total Asesi Unik
        $totalAsesi = Pendaftaran::distinct('user_id')->count('user_id');

        // Total Skema
        $totalSkema = Skema::count();

        // Total Asesor
        $totalAsesor = User::where('user_type', 'asesor')->count();

        // Pendaftaran Menunggu Verifikasi (Status 1)
        $menungguVerifikasi = Pendaftaran::where('status', 1)->count();

        // Tingkat Persetujuan (Approval Rate)
        $totalDiverifikasi = Pendaftaran::whereIn('status', [3, 4, 5, 6])->count();
        $approvalRate = $totalPendaftaran > 0
            ? round(($totalDiverifikasi / $totalPendaftaran) * 100, 1)
            : 0;

        // Tingkat Keberhasilan (Pass Rate)
        $totalKompeten = Report::where('status', 1)->count();
        $totalTidakKompeten = Report::where('status', 2)->count();
        $totalUjikom = $totalKompeten + $totalTidakKompeten;
        $passRate = $totalUjikom > 0
            ? round(($totalKompeten / $totalUjikom) * 100, 1)
            : 0;

        // ==========================================
        // 2. VERIFIKASI PERFORMANCE METRICS
        // ==========================================

        // Statistik Status Pendaftaran
        $statusStats = [
            'menunggu_verifikasi' => Pendaftaran::where('status', 1)->count(),
            'ditolak' => Pendaftaran::where('status', 2)->count(),
            'diverifikasi' => Pendaftaran::where('status', 3)->count(),
            'menunggu_ujian' => Pendaftaran::where('status', 4)->count(),
            'ujian_berlangsung' => Pendaftaran::where('status', 5)->count(),
            'selesai' => Pendaftaran::where('status', 6)->count(),
        ];

        // Rata-rata Waktu Verifikasi (dalam hari) - hanya untuk yang sudah diverifikasi
        $avgVerificationTime = Pendaftaran::whereIn('status', [3, 4, 5, 6])
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days');
        $avgVerificationTime = $avgVerificationTime ? round($avgVerificationTime, 1) : 0;

        // ==========================================
        // 3. TREND ANALYSIS (12 Bulan Terakhir)
        // ==========================================

        $trenPendaftaran = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $count = Pendaftaran::whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->count();
            $trenPendaftaran[] = [
                'bulan' => $bulan->format('M Y'),
                'jumlah' => $count
            ];
        }

        // ==========================================
        // 4. GROWTH METRICS
        // ==========================================

        $bulanIni = Pendaftaran::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $bulanLalu = Pendaftaran::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $growthRate = $bulanLalu > 0
            ? round((($bulanIni - $bulanLalu) / $bulanLalu) * 100, 1)
            : 0;

        // ==========================================
        // 5. DISTRIBUSI SKEMA (Top 5)
        // ==========================================

        $distribusiSkema = Pendaftaran::select('skema_id', DB::raw('count(*) as total'))
            ->with('skema')
            ->groupBy('skema_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->skema->nama ?? 'Unknown' => $item->total];
            });

        // ==========================================
        // 6. TOP PERFORMING SKEMA BY PASS RATE
        // ==========================================

        $topSkema = Report::select('report.skema_id',
                DB::raw('COUNT(*) as total_ujian'),
                DB::raw('SUM(CASE WHEN report.status = 1 THEN 1 ELSE 0 END) as total_lulus'))
            ->with('skema')
            ->groupBy('report.skema_id')
            ->havingRaw('COUNT(*) >= 3') // Minimal 3 ujian untuk valid
            ->get()
            ->map(function ($item) {
                $passRate = $item->total_ujian > 0
                    ? round(($item->total_lulus / $item->total_ujian) * 100, 1)
                    : 0;
                return [
                    'nama' => $item->skema->nama ?? 'Unknown',
                    'total_ujian' => $item->total_ujian,
                    'total_lulus' => $item->total_lulus,
                    'pass_rate' => $passRate
                ];
            })
            ->sortByDesc('pass_rate')
            ->take(5)
            ->values();

        // ==========================================
        // 7. WORKLOAD ASESOR (Top 10)
        // ==========================================

        $workloadAsesor = DB::table('pendaftaran_ujikom')
            ->join('users', 'pendaftaran_ujikom.asesor_id', '=', 'users.id')
            ->select('users.name as nama', DB::raw('COUNT(pendaftaran_ujikom.id) as total'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'nama' => $item->nama,
                    'total' => $item->total
                ];
            });

        // ==========================================
        // 8. SEGMENTASI DEMOGRAFI
        // ==========================================

        $segmentasiJenisKelamin = User::whereIn('id', function($query) {
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
        // 9. VERIFIKASI TREND (Status Over Time)
        // ==========================================

        $verifikasiTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $diverifikasi = Pendaftaran::whereMonth('updated_at', $bulan->month)
                ->whereYear('updated_at', $bulan->year)
                ->whereIn('status', [3, 4, 5, 6])
                ->count();
            $ditolak = Pendaftaran::whereMonth('updated_at', $bulan->month)
                ->whereYear('updated_at', $bulan->year)
                ->where('status', 2)
                ->count();

            $verifikasiTrend[] = [
                'bulan' => $bulan->format('M Y'),
                'diverifikasi' => $diverifikasi,
                'ditolak' => $ditolak
            ];
        }

        // ==========================================
        // 10. AI INSIGHTS (Rule-Based)
        // ==========================================

        $insights = $this->generateInsights([
            'menungguVerifikasi' => $menungguVerifikasi,
            'approvalRate' => $approvalRate,
            'passRate' => $passRate,
            'growthRate' => $growthRate,
            'avgVerificationTime' => $avgVerificationTime,
            'workload' => $workloadAsesor,
            'statusStats' => $statusStats
        ]);

        return view('components.pages.kaprodi.dashboard', compact(
            'lists',
            // KPIs
            'totalPendaftaran',
            'totalAsesi',
            'totalSkema',
            'totalAsesor',
            'menungguVerifikasi',
            'approvalRate',
            'passRate',
            // Performance Metrics
            'statusStats',
            'avgVerificationTime',
            // Analytics
            'trenPendaftaran',
            'growthRate',
            'distribusiSkema',
            'topSkema',
            'workloadAsesor',
            'segmentasiJenisKelamin',
            'verifikasiTrend',
            'insights'
        ));
    }

    /**
     * Generate AI Insights based on data
     */
    private function generateInsights($data)
    {
        $insights = [
            'verifikasi' => '',
            'performance' => '',
            'action' => ''
        ];

        // Verifikasi Workload Analysis
        if ($data['menungguVerifikasi'] > 20) {
            $insights['verifikasi'] = "⚠️ PERHATIAN: Ada {$data['menungguVerifikasi']} pendaftaran menunggu verifikasi. Perlu ditindaklanjuti segera untuk menjaga kepuasan peserta.";
        } elseif ($data['menungguVerifikasi'] > 10) {
            $insights['verifikasi'] = "📊 {$data['menungguVerifikasi']} pendaftaran menunggu verifikasi. Workload masih dalam batas wajar.";
        } else {
            $insights['verifikasi'] = "✅ Proses verifikasi berjalan lancar. Hanya {$data['menungguVerifikasi']} pendaftaran dalam antrian.";
        }

        // Performance Analysis
        if ($data['approvalRate'] >= 80) {
            $insights['performance'] = "✅ Tingkat persetujuan sangat baik ({$data['approvalRate']}%). Menunjukkan kualitas pendaftaran yang baik dan proses verifikasi yang efektif.";
        } elseif ($data['approvalRate'] >= 60) {
            $insights['performance'] = "📊 Tingkat persetujuan cukup baik ({$data['approvalRate']}%). Ada ruang untuk perbaikan kualitas pendaftaran.";
        } else {
            $insights['performance'] = "⚠️ Tingkat persetujuan rendah ({$data['approvalRate']}%). Perlu evaluasi persyaratan dan panduan pendaftaran.";
        }

        // Action Items
        if ($data['avgVerificationTime'] > 7) {
            $insights['action'] = "🔴 Rata-rata waktu verifikasi {$data['avgVerificationTime']} hari terlalu lama. Target maksimal 3-5 hari. Pertimbangkan optimasi proses atau penambahan reviewer.";
        } elseif ($data['avgVerificationTime'] > 5) {
            $insights['action'] = "🟡 Rata-rata waktu verifikasi {$data['avgVerificationTime']} hari. Masih acceptable tapi bisa dipercepat untuk meningkatkan kepuasan peserta.";
        } elseif ($data['passRate'] < 60) {
            $insights['action'] = "⚠️ Pass rate {$data['passRate']}% perlu perhatian. Evaluasi kualitas training dan dukungan untuk asesi sebelum ujian.";
        } elseif ($data['growthRate'] < -15) {
            $insights['action'] = "📉 Penurunan pendaftaran {$data['growthRate']}% signifikan. Perlu evaluasi strategi promosi dan outreach ke mahasiswa.";
        } else {
            $insights['action'] = "✅ Semua metrik dalam kondisi baik. Pass rate: {$data['passRate']}%, Approval rate: {$data['approvalRate']}%. Fokus pada peningkatan efisiensi dan skalabilitas.";
        }

        return $insights;
    }
}
