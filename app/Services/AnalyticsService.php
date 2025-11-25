<?php

namespace App\Services;

use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\Skema;
use App\Models\Tuk;
use App\Models\Report;
use App\Models\PembayaranAsesor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Mendapatkan trend pendaftaran berdasarkan skema dan periode waktu
     */
    public function getTrendPendaftaran($skemaId = null, $startDate = null, $endDate = null)
    {
        $query = Pendaftaran::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(id) as total_pendaftaran')
        );

        if ($skemaId) {
            $query->where('skema_id', $skemaId);
        }

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $results = $query->groupBy('month')
                        ->orderBy('month')
                        ->get();

        return $results->map(function ($item) {
            return [
                'month' => $item->month,
                'total_pendaftaran' => $item->total_pendaftaran
            ];
        });
    }

    /**
     * Mendapatkan statistik kompetensi berdasarkan skema dan status
     * Menggunakan data dari report.status (1=Kompeten, 2=Tidak Kompeten)
     */
    public function getStatistikKompetensi($startDate = null, $endDate = null)
    {
        $query = Report::select(
            'pendaftaran.skema_id',
            'skema.nama as skema_nama',
            'skema.kode as skema_kode',
            'report.status',
            DB::raw('COUNT(report.id) as jumlah')
        )
        ->join('pendaftaran', 'report.pendaftaran_id', '=', 'pendaftaran.id')
        ->join('skema', 'pendaftaran.skema_id', '=', 'skema.id');

        if ($startDate) {
            $query->where('report.created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('report.created_at', '<=', $endDate);
        }

        $results = $query->groupBy('pendaftaran.skema_id', 'skema.nama', 'skema.kode', 'report.status')
                        ->get();

        $response = [];
        $skemaInfo = [];

        foreach ($results as $result) {
            if (!isset($response[$result->skema_id])) {
                $response[$result->skema_id] = [];
                $skemaInfo[$result->skema_id] = [
                    'nama' => $result->skema_nama,
                    'kode' => $result->skema_kode
                ];
            }
            // Map status: 1=Kompeten, 2=Tidak Kompeten
            // Gunakan key 5 untuk Kompeten dan 4 untuk Tidak Kompeten agar kompatibel dengan controller
            $statusKey = $result->status == 1 ? 5 : 4;
            $response[$result->skema_id][$statusKey] = $result->jumlah;
        }

        // Add skema info to response for better labeling
        foreach ($response as $skemaId => $statuses) {
            $response[$skemaId]['_skema_info'] = $skemaInfo[$skemaId] ?? null;
        }

        return $response;
    }

    /**
     * Mendapatkan segmentasi demografi berdasarkan jenis kelamin, pendidikan, dan pekerjaan
     * Hanya menghitung user yang pernah mendaftar ujikom (asesi)
     */
    public function getSegmentasiDemografi()
    {
        // Ambil user_id yang pernah mendaftar
        $userIdsYangMendaftar = Pendaftaran::distinct()->pluck('user_id');

        // Segmentasi berdasarkan jenis kelamin (hanya asesi yang pernah mendaftar)
        $genderData = User::select('jenis_kelamin', DB::raw('COUNT(id) as jumlah'))
                           ->whereIn('id', $userIdsYangMendaftar)
                           ->whereNotNull('jenis_kelamin') // Filter data kosong
                           ->where('jenis_kelamin', '!=', '') // Filter string kosong
                           ->groupBy('jenis_kelamin')
                           ->get();

        // Mapping jenis kelamin ke label yang benar
        $genderCounts = [];
        foreach ($genderData as $item) {
            $label = match($item->jenis_kelamin) {
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
                default => 'Lainnya'
            };
            $genderCounts[$label] = $item->jumlah;
        }

        // Segmentasi berdasarkan pendidikan (hanya asesi yang pernah mendaftar)
        $pendidikanCounts = User::select('pendidikan', DB::raw('COUNT(id) as jumlah'))
                               ->whereIn('id', $userIdsYangMendaftar)
                               ->whereNotNull('pendidikan')
                               ->where('pendidikan', '!=', '')
                               ->groupBy('pendidikan')
                               ->get()
                               ->pluck('jumlah', 'pendidikan')
                               ->toArray();

        // Segmentasi berdasarkan pekerjaan (hanya asesi yang pernah mendaftar)
        $pekerjaanCounts = User::select('pekerjaan', DB::raw('COUNT(id) as jumlah'))
                              ->whereIn('id', $userIdsYangMendaftar)
                              ->whereNotNull('pekerjaan')
                              ->where('pekerjaan', '!=', '')
                              ->groupBy('pekerjaan')
                              ->get()
                              ->pluck('jumlah', 'pekerjaan')
                              ->toArray();

        return [
            'jenis_kelamin' => $genderCounts,
            'pendidikan' => $pendidikanCounts,
            'pekerjaan' => $pekerjaanCounts,
        ];
    }

    /**
     * Mendapatkan workload asesor berdasarkan jumlah laporan dan pembayaran
     */
    public function getWorkloadAsesor($startDate = null, $endDate = null)
    {
        try {
            // Query untuk laporan per asesor (join via pendaftaran -> pendaftaran_ujikom)
            $laporanQuery = Report::select(
                'pendaftaran_ujikom.asesor_id',
                'users.name as asesor_name',
                DB::raw('COUNT(report.id) as jumlah_laporan')
            )
            ->join('pendaftaran', 'report.pendaftaran_id', '=', 'pendaftaran.id')
            ->join('pendaftaran_ujikom', 'pendaftaran.id', '=', 'pendaftaran_ujikom.pendaftaran_id')
            ->join('users', 'pendaftaran_ujikom.asesor_id', '=', 'users.id');

            if ($startDate) {
                $laporanQuery->where('report.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $laporanQuery->where('report.created_at', '<=', $endDate);
            }

            $laporanCounts = $laporanQuery->groupBy('pendaftaran_ujikom.asesor_id', 'users.name')
                                         ->get()
                                         ->keyBy('asesor_id');

            // Query untuk pembayaran asesor
            $pembayaranQuery = PembayaranAsesor::select(
                'asesor_id',
                DB::raw('COUNT(id) as jumlah_pembayaran')
            );

            if ($startDate) {
                $pembayaranQuery->where('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $pembayaranQuery->where('created_at', '<=', $endDate);
            }

            $pembayaranCounts = $pembayaranQuery->groupBy('asesor_id')
                                               ->get()
                                               ->keyBy('asesor_id');

            // Gabungkan hasil
            $response = [];
            $allAsesorIds = $laporanCounts->keys()->merge($pembayaranCounts->keys())->unique();

            foreach ($allAsesorIds as $asesorId) {
                $laporan = $laporanCounts->get($asesorId);
                $pembayaran = $pembayaranCounts->get($asesorId);

                $response[] = [
                    'asesor_name' => $laporan ? $laporan->asesor_name : null,
                    'jumlah_laporan' => $laporan ? $laporan->jumlah_laporan : 0,
                    'jumlah_pembayaran' => $pembayaran ? $pembayaran->jumlah_pembayaran : 0
                ];
            }

            return $response;

        } catch (\Exception $e) {
            // Log error dan return array kosong
            \Log::error("Error in getWorkloadAsesor: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return [];
        }
    }

    /**
     * Mendapatkan ringkasan data untuk dashboard utama
     */
    public function getDashboardSummary()
    {
        try {
            $totalPendaftaran = Pendaftaran::count();
            $totalSkema = Skema::count();
            $totalAsesor = User::where('user_type', 'asesor')->count(); // Fix: gunakan User, bukan Tuk
            $pendaftaranBulanIni = Pendaftaran::whereMonth('created_at', Carbon::now()->month)
                                             ->whereYear('created_at', Carbon::now()->year)
                                             ->count();

            return [
                'total_pendaftaran' => $totalPendaftaran,
                'total_skema' => $totalSkema,
                'total_asesor' => $totalAsesor,
                'pendaftaran_bulan_ini' => $pendaftaranBulanIni
            ];
        } catch (\Exception $e) {
            \Log::error("Error in getDashboardSummary: " . $e->getMessage());
            return [
                'total_pendaftaran' => 0,
                'total_skema' => 0,
                'total_asesor' => 0,
                'pendaftaran_bulan_ini' => 0
            ];
        }
    }

    /**
     * Mendapatkan informasi struktur tabel database untuk debugging
     */
    public function getDebugTables()
    {
        try {
            $tables = [
                'users' => ['id', 'jenis_kelamin', 'pendidikan', 'pekerjaan', 'tanggal_lahir'],
                'skema' => ['id', 'nama', 'kode'],
                'pendaftaran' => ['id', 'jadwal_id', 'user_id', 'skema_id', 'status', 'created_at'],
                'tuk' => ['id', 'name'],
                'report' => ['id', 'tuk_id', 'created_at'],
                'pembayaran_asesor' => ['id', 'asesor_id', 'bukti_pembayaran', 'status', 'created_at']
            ];

            return [
                'tables' => $tables,
                'message' => 'Struktur tabel database'
            ];
        } catch (\Exception $e) {
            \Log::error("Error in getDebugTables: " . $e->getMessage());
            return [
                'error' => 'Error mengambil info tabel: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mendapatkan tren peminat skema dari waktu ke waktu
     */
    public function getTrenPeminatSkema($startDate = null, $endDate = null)
    {
        try {
            $skemas = Skema::all();
            $result = [];

            foreach ($skemas as $skema) {
                $query = Pendaftaran::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period'),
                    DB::raw('COUNT(id) as registrations')
                )
                ->where('skema_id', $skema->id);

                if ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->where('created_at', '<=', $endDate);
                }

                $trend = $query->groupBy('period')
                              ->orderBy('period')
                              ->get();

                $result[] = [
                    'skema_id' => $skema->id,
                    'skema_name' => $skema->nama,
                    'trend' => $trend->map(function ($item) {
                        return [
                            'period' => $item->period,
                            'registrations' => $item->registrations
                        ];
                    })
                ];
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error("Error in getTrenPeminatSkema: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper method untuk handle null values
     */
    private function handleNullValues($array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = $key === null ? 'Tidak Diketahui' : (string) $key;
            $result[$newKey] = $value;
        }
        return $result;
    }
}
