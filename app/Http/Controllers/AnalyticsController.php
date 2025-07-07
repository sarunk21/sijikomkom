<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    private $pythonApiBase = 'http://127.0.0.1:3000';
    private $timeout = 10; // 10 detik

    /**
     * Get analytics data for dashboard
     */
    public function getDashboardData()
    {
        try {
            // Get all analytics data
            $skemaTrend = $this->getSkemaTrend();
            $statistikKeberhasilan = $this->getStatistikKeberhasilan();
            $segmentasiDemografi = $this->getSegmentasiDemografi();
            $workloadAsesor = $this->getWorkloadAsesor();
            $trenPeminatSkema = $this->getTrenPeminatSkema();

            return response()->json([
                'success' => true,
                'data' => [
                    'skema_trend' => $skemaTrend,
                    'statistik_keberhasilan' => $statistikKeberhasilan,
                    'segmentasi_demografi' => $segmentasiDemografi,
                    'workload_asesor' => $workloadAsesor,
                    'tren_peminat_skema' => $trenPeminatSkema
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data analytics: ' . $e->getMessage(),
                'data' => [
                    'skema_trend' => [],
                    'statistik_keberhasilan' => [],
                    'segmentasi_demografi' => [],
                    'workload_asesor' => [],
                    'tren_peminat_skema' => []
                ]
            ], 500);
        }
    }

    /**
     * Get skema trend data
     */
    private function getSkemaTrend()
    {
        $cacheKey = 'analytics_skema_trend';

        return Cache::remember($cacheKey, 300, function () { // Cache 5 menit
            $startDate = now()->subMonths(6)->format('Y-m-d');
            $endDate = now()->format('Y-m-d');

            $response = Http::timeout($this->timeout)
                ->get($this->pythonApiBase . '/analytics/skema-trend', [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            // Return dummy data jika API error
            return [
                [
                    'skema_id' => 1,
                    'skema_name' => 'Skema Manajemen Proyek',
                    'total_registrations' => 150
                ],
                [
                    'skema_id' => 2,
                    'skema_name' => 'Skema IT Networking',
                    'total_registrations' => 95
                ]
            ];
        });
    }

    /**
     * Get statistik keberhasilan data
     */
    private function getStatistikKeberhasilan()
    {
        $cacheKey = 'analytics_statistik_keberhasilan';

        return Cache::remember($cacheKey, 300, function () {
            $response = Http::timeout($this->timeout)
                ->get($this->pythonApiBase . '/analytics/statistik-keberhasilan');

            if ($response->successful()) {
                return $response->json();
            }

            // Return dummy data jika API error
            return [
                [
                    'skema_id' => 1,
                    'skema_name' => 'Skema Manajemen Proyek',
                    'participant_count' => 140,
                    'passed_count' => 120,
                    'pass_rate' => 85.7
                ],
                [
                    'skema_id' => 2,
                    'skema_name' => 'Skema IT Networking',
                    'participant_count' => 90,
                    'passed_count' => 60,
                    'pass_rate' => 66.7
                ]
            ];
        });
    }

    /**
     * Get segmentasi demografi data
     */
    private function getSegmentasiDemografi()
    {
        $cacheKey = 'analytics_segmentasi_demografi';

        return Cache::remember($cacheKey, 300, function () {
            $response = Http::timeout($this->timeout)
                ->get($this->pythonApiBase . '/analytics/segmentasi-demografi');

            if ($response->successful()) {
                return $response->json();
            }

            // Return dummy data jika API error
            return [
                'gender_distribution' => [
                    'male' => 120,
                    'female' => 80,
                    'other' => 5
                ],
                'age_distribution' => [
                    '18-25' => 30,
                    '26-35' => 90,
                    '36-50' => 60,
                    '51+' => 25
                ]
            ];
        });
    }

    /**
     * Get workload asesor data
     */
    private function getWorkloadAsesor()
    {
        $cacheKey = 'analytics_workload_asesor';

        return Cache::remember($cacheKey, 300, function () {
            $response = Http::timeout($this->timeout)
                ->get($this->pythonApiBase . '/analytics/workload-asesor');

            if ($response->successful()) {
                return $response->json();
            }

            // Return dummy data jika API error
            return [
                [
                    'asesor_name' => 'Budi Santoso',
                    'total_reports_handled' => 40
                ],
                [
                    'asesor_name' => 'Sari Dewi',
                    'total_reports_handled' => 35
                ],
                [
                    'asesor_name' => 'Ahmad Hidayat',
                    'total_reports_handled' => 28
                ]
            ];
        });
    }

    /**
     * Get tren peminat skema data
     */
    private function getTrenPeminatSkema()
    {
        $cacheKey = 'analytics_tren_peminat_skema';

        return Cache::remember($cacheKey, 300, function () {
            $response = Http::timeout($this->timeout)
                ->get($this->pythonApiBase . '/analytics/tren-peminat-skema', [
                    'interval' => 'monthly'
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            // Return dummy data jika API error
            return [
                [
                    'skema_id' => 1,
                    'skema_name' => 'Skema Manajemen Proyek',
                    'trend' => [
                        ['period' => '2023-01', 'registrations' => 20],
                        ['period' => '2023-02', 'registrations' => 25],
                        ['period' => '2023-03', 'registrations' => 30],
                        ['period' => '2023-04', 'registrations' => 35],
                        ['period' => '2023-05', 'registrations' => 40],
                        ['period' => '2023-06', 'registrations' => 45]
                    ]
                ],
                [
                    'skema_id' => 2,
                    'skema_name' => 'Skema IT Networking',
                    'trend' => [
                        ['period' => '2023-01', 'registrations' => 15],
                        ['period' => '2023-02', 'registrations' => 18],
                        ['period' => '2023-03', 'registrations' => 22],
                        ['period' => '2023-04', 'registrations' => 25],
                        ['period' => '2023-05', 'registrations' => 28],
                        ['period' => '2023-06', 'registrations' => 32]
                    ]
                ]
            ];
        });
    }

    /**
     * Clear analytics cache
     */
    public function clearCache()
    {
        Cache::forget('analytics_skema_trend');
        Cache::forget('analytics_statistik_keberhasilan');
        Cache::forget('analytics_segmentasi_demografi');
        Cache::forget('analytics_workload_asesor');
        Cache::forget('analytics_tren_peminat_skema');

        return response()->json([
            'success' => true,
            'message' => 'Cache analytics berhasil di-clear'
        ]);
    }
}
