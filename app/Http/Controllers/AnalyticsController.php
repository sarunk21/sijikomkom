<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Http\Requests\SkemaTrendRequest;
use App\Http\Requests\WorkloadAsesorRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Mendapatkan trend pendaftaran skema berdasarkan periode waktu
     */
    public function skemaTrend(SkemaTrendRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $results = $this->analyticsService->getTrendPendaftaran(
                $validated['skema_id'] ?? null,
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null
            );

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error mengambil data trend: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan statistik kompetensi berdasarkan skema dan status pendaftaran
     */
    public function kompetensiSkema(Request $request): JsonResponse
    {
        try {
            $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : null;
            $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : null;

            $results = $this->analyticsService->getStatistikKompetensi($startDate, $endDate);

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error mengambil statistik kompetensi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan data segmentasi demografi pengguna
     */
    public function segmentasiDemografi(): JsonResponse
    {
        try {
            $results = $this->analyticsService->getSegmentasiDemografi();

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error mengambil segmentasi demografi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan data workload asesor berdasarkan jumlah laporan dan pembayaran
     */
    public function workloadAsesor(WorkloadAsesorRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $results = $this->analyticsService->getWorkloadAsesor(
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null
            );

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error mengambil workload asesor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan ringkasan data untuk dashboard utama
     */
    public function dashboardSummary(): JsonResponse
    {
        try {
            $results = $this->analyticsService->getDashboardSummary();

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error mengambil dashboard summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint untuk debugging struktur tabel database
     */
    public function debugTables(): JsonResponse
    {
        try {
            $results = $this->analyticsService->getDebugTables();

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error mengambil info tabel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Root endpoint untuk mengecek status API
     */
    public function root(): JsonResponse
    {
        return response()->json([
            'message' => 'Sijikomkom Analytics API running',
            'version' => '1.0.0',
            'docs' => '/analytics/docs',
            'authenticated' => auth()->check(),
            'user' => auth()->check() ? [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'user_type' => auth()->user()->user_type
            ] : null,
            'endpoints' => [
                'skema-trend' => '/analytics/skema-trend',
                'kompetensi-skema' => '/analytics/kompetensi-skema',
                'segmentasi-demografi' => '/analytics/segmentasi-demografi',
                'workload-asesor' => '/analytics/workload-asesor',
                'dashboard-summary' => '/analytics/dashboard-summary',
                'debug-tables' => '/analytics/debug-tables'
            ]
        ]);
    }

    /**
     * Endpoint untuk health check
     */
    public function healthCheck(): JsonResponse
    {
        return response()->json([
            'status' => 'healthy',
            'message' => 'API is running normally'
        ]);
    }

    /**
     * Mendapatkan data dashboard untuk frontend (legacy method)
     */
    public function getDashboardData(Request $request): JsonResponse
    {
        try {
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required. Please login first.',
                    'error' => 'UNAUTHENTICATED'
                ], 401);
            }

            // Check if user has proper user_type
            $userType = auth()->user()->user_type;
            if (!in_array($userType, ['pimpinan', 'kaprodi', 'admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. User type: ' . $userType,
                    'error' => 'UNAUTHORIZED'
                ], 403);
            }

            // Ambil filter dari request
            $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : null;
            $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : null;
            $skemaId = $request->query('skema_id') ?: null;

            // Menggabungkan semua data analytics untuk dashboard dengan filter
            // Note: Pastikan parameter sesuai dengan signature method di AnalyticsService
            $skemaTrend = $this->analyticsService->getTrendPendaftaran($skemaId, $startDate, $endDate);
            $kompetensiSkema = $this->analyticsService->getStatistikKompetensi($startDate, $endDate); // Only 2 params
            $segmentasiDemografi = $this->analyticsService->getSegmentasiDemografi(); // No params
            $workloadAsesor = $this->analyticsService->getWorkloadAsesor($startDate, $endDate); // Only 2 params
            $trenPeminatSkema = $this->analyticsService->getTrenPeminatSkema($startDate, $endDate); // Only 2 params
            $dashboardSummary = $this->analyticsService->getDashboardSummary(); // No params

            // Convert Collections to arrays for JSON encoding
            if (is_object($skemaTrend) && method_exists($skemaTrend, 'toArray')) {
                $skemaTrend = $skemaTrend->toArray();
            }
            if (is_object($trenPeminatSkema) && method_exists($trenPeminatSkema, 'toArray')) {
                $trenPeminatSkema = $trenPeminatSkema->toArray();
            }

            // Log data untuk debugging
            \Log::info('Dashboard data loaded', [
                'user_type' => auth()->user()->user_type,
                'skema_trend_count' => is_array($skemaTrend) ? count($skemaTrend) : 0,
                'kompetensi_skema_count' => is_array($kompetensiSkema) ? count($kompetensiSkema) : 0,
                'workload_asesor_count' => is_array($workloadAsesor) ? count($workloadAsesor) : 0,
                'tren_peminat_count' => is_array($trenPeminatSkema) ? count($trenPeminatSkema) : 0
            ]);

            // Calculate tingkat keberhasilan from kompetensiSkema
            $totalKompeten = 0;
            $totalTidakKompeten = 0;

            foreach ($kompetensiSkema as $skemaData) {
                $totalKompeten += $skemaData[5] ?? 0; // status 5 = kompeten
                $totalTidakKompeten += $skemaData[4] ?? 0; // status 4 = tidak kompeten
            }

            $totalUjikom = $totalKompeten + $totalTidakKompeten;
            $tingkatKeberhasilan = $totalUjikom > 0 ? round(($totalKompeten / $totalUjikom) * 100, 2) : 0;

            $data = [
                'skema_trend' => $skemaTrend,
                'kompetensi_skema' => $kompetensiSkema,
                'segmentasi_demografi' => $segmentasiDemografi,
                'workload_asesor' => $workloadAsesor,
                'tren_peminat_skema' => $trenPeminatSkema,
                'dashboard_summary' => array_merge($dashboardSummary, [
                    'tingkat_keberhasilan' => $tingkatKeberhasilan
                ]),
                'filters' => [
                    'start_date' => $startDate ? $startDate->format('Y-m-d') : null,
                    'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
                    'skema_id' => $skemaId
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Data dashboard berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getDashboardData: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data dashboard: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Mendapatkan tren peminat skema dari waktu ke waktu
     */
    public function trenPeminatSkema(Request $request): JsonResponse
    {
        try {
            $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date')) : null;
            $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date')) : null;

            $results = $this->analyticsService->getTrenPeminatSkema($startDate, $endDate);

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error mengambil tren peminat skema: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear cache analytics (legacy method)
     */
    public function clearCache(): JsonResponse
    {
        try {
            // Clear cache jika ada
            Cache::forget('analytics_dashboard_data');
        Cache::forget('analytics_skema_trend');
            Cache::forget('analytics_kompetensi_skema');
        Cache::forget('analytics_segmentasi_demografi');
        Cache::forget('analytics_workload_asesor');
            Cache::forget('analytics_dashboard_summary');
        Cache::forget('analytics_tren_peminat_skema');

        return response()->json([
            'success' => true,
                'message' => 'Cache analytics berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error menghapus cache: ' . $e->getMessage()
            ], 500);
        }
    }
}
