<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use App\Models\Pendaftaran;
use App\Models\Jadwal;
use App\Models\User;
use App\Models\Skema;
use App\Models\Report;
use App\Models\PendaftaranUjikom;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        $lists = $this->getMenuListAdmin('dashboard');

        // ==========================================
        // 1. KEY PERFORMANCE INDICATORS (KPIs)
        // ==========================================

        // Total Pendaftaran
        $totalPendaftaran = Pendaftaran::count();

        // Total Asesi (unique users)
        $totalAsesi = Pendaftaran::distinct('user_id')->count('user_id');

        // Total Skema Sertifikasi
        $totalSkema = Skema::count();

        // Total Asesor Aktif
        $totalAsesor = User::where('user_type', 'asesor')->count();

        // Total Jadwal
        $totalJadwal = Jadwal::count();

        // Jadwal Aktif (status 1 = Aktif)
        $jadwalAktif = Jadwal::where('status', 1)->count();

        // ==========================================
        // 2. SUCCESS METRICS
        // ==========================================

        // Total yang sudah selesai ujian (status 6 = Selesai)
        $totalSelesai = Pendaftaran::where('status', 6)->count();

        // Total Lulus/Kompeten (dari report dengan status 1)
        $totalLulus = Report::where('status', 1)->count();

        // Tingkat Keberhasilan (Pass Rate)
        $passRate = $totalSelesai > 0 ? round(($totalLulus / $totalSelesai) * 100, 1) : 0;

        // ==========================================
        // 3. TREND ANALYSIS (6 Bulan Terakhir)
        // ==========================================

        $trenPendaftaran = [];
        for ($i = 5; $i >= 0; $i--) {
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
        // 4. DISTRIBUSI SKEMA (Top 5)
        // ==========================================

        $distribusiSkema = Pendaftaran::select('skema_id', DB::raw('count(*) as total'))
            ->with('skema')
            ->groupBy('skema_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->skema->nama => $item->total];
            });

        // ==========================================
        // 5. WORKLOAD ASESOR (Top 10)
        // ==========================================

        $workloadAsesor = PendaftaranUjikom::select('asesor_id', DB::raw('count(*) as total_asesi'))
            ->with('asesor')
            ->groupBy('asesor_id')
            ->orderBy('total_asesi', 'desc')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return [
                    'nama' => $item->asesor->name ?? 'Unknown',
                    'total' => $item->total_asesi
                ];
            });

        // ==========================================
        // 6. CONVERSION FUNNEL
        // Perhitungan: Pendaftaran → Verifikasi → Ujian → Lulus
        // ==========================================

        $funnelData = [
            'pendaftaran' => Pendaftaran::count(),
            'diverifikasi' => Pendaftaran::whereIn('status', [3, 4, 5, 6])->count(), // Status >= Diverifikasi
            'ujian_selesai' => Pendaftaran::where('status', 6)->count(),
            'lulus' => Report::where('status', 1)->count(),
        ];

        // Conversion Rate: (Lulus / Total Pendaftaran) * 100
        $conversionRate = $funnelData['pendaftaran'] > 0
            ? round(($funnelData['lulus'] / $funnelData['pendaftaran']) * 100, 1)
            : 0;

        // ==========================================
        // 7. TOP PERFORMING SKEMA
        // Ranking berdasarkan Pass Rate
        // ==========================================

        $topSkema = Report::select('report.skema_id',
                DB::raw('COUNT(*) as total_ujian'),
                DB::raw('SUM(CASE WHEN report.status = 1 THEN 1 ELSE 0 END) as total_lulus'))
            ->with('skema')
            ->groupBy('report.skema_id')
            ->havingRaw('COUNT(*) >= 5') // Minimal 5 ujian untuk valid
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
        // 8. STATISTIK STATUS PENDAFTARAN
        // ==========================================

        $statusStats = [
            'menunggu_verifikasi' => Pendaftaran::where('status', 1)->count(),
            'ditolak' => Pendaftaran::where('status', 2)->count(),
            'diverifikasi' => Pendaftaran::where('status', 3)->count(),
            'menunggu_ujian' => Pendaftaran::where('status', 4)->count(),
            'ujian_berlangsung' => Pendaftaran::where('status', 5)->count(),
            'selesai' => Pendaftaran::where('status', 6)->count(),
            'asesor_tidak_hadir' => Pendaftaran::where('status', 7)->count(),
        ];

        // ==========================================
        // 9. GROWTH METRICS (Perbandingan Bulan Ini vs Bulan Lalu)
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
        // 10. AI INSIGHTS (Simple Rule-Based)
        // ==========================================

        $insights = $this->generateInsights([
            'passRate' => $passRate,
            'growthRate' => $growthRate,
            'workload' => $workloadAsesor,
            'statusStats' => $statusStats,
            'conversionRate' => $conversionRate,
        ]);

        return view('components.pages.admin.dashboard', compact(
            'lists',
            // KPIs
            'totalPendaftaran',
            'totalAsesi',
            'totalSkema',
            'totalAsesor',
            'totalJadwal',
            'jadwalAktif',
            // Success Metrics
            'totalSelesai',
            'totalLulus',
            'passRate',
            // Analytics
            'trenPendaftaran',
            'distribusiSkema',
            'workloadAsesor',
            'funnelData',
            'conversionRate',
            'topSkema',
            'statusStats',
            'growthRate',
            'insights'
        ));
    }

    /**
     * Generate AI Insights based on data
     * Simple rule-based system untuk memberikan rekomendasi
     */
    private function generateInsights($data)
    {
        $insights = [
            'trend' => '',
            'capacity' => '',
            'action' => ''
        ];

        // Trend Analysis
        if ($data['growthRate'] > 20) {
            $insights['trend'] = "📈 Pertumbuhan pendaftaran sangat tinggi ({$data['growthRate']}%). Ini menunjukkan demand yang kuat untuk program sertifikasi.";
        } elseif ($data['growthRate'] > 0) {
            $insights['trend'] = "📊 Pertumbuhan pendaftaran positif ({$data['growthRate']}%). Trend yang baik untuk ekspansi program.";
        } elseif ($data['growthRate'] < -10) {
            $insights['trend'] = "📉 Penurunan pendaftaran signifikan ({$data['growthRate']}%). Perlu evaluasi strategi marketing dan kualitas program.";
        } else {
            $insights['trend'] = "➡️ Pendaftaran relatif stabil. Pertahankan kualitas dan pertimbangkan strategi untuk meningkatkan reach.";
        }

        // Capacity Planning
        if (!$data['workload']->isEmpty()) {
            $maxWorkload = $data['workload']->first()['total'];
            $minWorkload = $data['workload']->last()['total'];
            $gap = $maxWorkload - $minWorkload;

            if ($gap > 50) {
                $insights['capacity'] = "⚠️ Gap workload asesor sangat tinggi ({$gap} asesi). Pertimbangkan redistribusi atau rekrutmen asesor baru.";
            } elseif ($gap > 20) {
                $insights['capacity'] = "📊 Distribusi workload perlu penyesuaian. Gap: {$gap} asesi antara asesor tertinggi dan terendah.";
            } else {
                $insights['capacity'] = "✅ Distribusi workload asesor cukup seimbang. Gap hanya {$gap} asesi.";
            }
        } else {
            $insights['capacity'] = "📊 Belum ada data workload asesor yang cukup untuk analisis.";
        }

        // Action Items
        if ($data['passRate'] < 60) {
            $insights['action'] = "🔴 URGENT: Pass rate rendah ({$data['passRate']}%). Review kualitas training, kesulitan ujian, dan support untuk asesi.";
        } elseif ($data['passRate'] < 75) {
            $insights['action'] = "🟡 Pass rate perlu ditingkatkan ({$data['passRate']}%). Evaluasi materi training dan feedback dari asesi.";
        } elseif ($data['conversionRate'] < 50) {
            $insights['action'] = "⚠️ Conversion rate rendah ({$data['conversionRate']}%). Banyak yang daftar tapi tidak sampai lulus. Check bottleneck di funnel.";
        } else {
            $insights['action'] = "✅ Performance bagus! Pass rate: {$data['passRate']}%, Conversion: {$data['conversionRate']}%. Fokus pada scale dan efisiensi.";
        }

        return $insights;
    }
}
