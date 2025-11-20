<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Models\Skema;
use App\Models\Jadwal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        // Total Pendaftaran
        $totalPendaftaran = Pendaftaran::count();

        // Total Skema
        $totalSkema = Skema::count();

        // Tingkat Keberhasilan - ambil dari tabel report
        $totalKompeten = \App\Models\Report::where('status', 1)->count(); // Status 1 = Kompeten
        $totalTidakKompeten = \App\Models\Report::where('status', 2)->count(); // Status 2 = Tidak Kompeten
        $totalUjikom = $totalKompeten + $totalTidakKompeten;
        $tingkatKeberhasilan = $totalUjikom > 0 ? round(($totalKompeten / $totalUjikom) * 100, 2) : 0;

        // Total Asesor
        $totalAsesor = User::where('user_type', 'asesor')->count();

        // Tren Pendaftaran Skema (6 bulan terakhir)
        $skemaTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $count = Pendaftaran::whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->count();
            $skemaTrend[] = [
                'month' => $bulan->format('M Y'),
                'total_pendaftaran' => $count
            ];
        }

        // Statistik Keberhasilan (Kompeten vs Tidak Kompeten)
        $statistikKeberhasilan = [
            'Kompeten' => $totalKompeten,
            'Tidak Kompeten' => $totalTidakKompeten
        ];

        // Segmentasi Demografi - Jenis Kelamin
        $segmentasiJenisKelamin = User::whereIn('id', function($query) {
                $query->select('user_id')->from('pendaftaran')->distinct();
            })
            ->select('jenis_kelamin', DB::raw('COUNT(id) as jumlah'))
            ->whereNotNull('jenis_kelamin')
            ->where('jenis_kelamin', '!=', '')
            ->groupBy('jenis_kelamin')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->jenis_kelamin => $item->jumlah];
            });

        // Workload Asesor (Top 10 Asesor dengan ujikom terbanyak)
        $workloadAsesor = DB::table('pendaftaran_ujikom')
            ->join('users', 'pendaftaran_ujikom.asesor_id', '=', 'users.id')
            ->select('users.name as asesor_name', DB::raw('COUNT(pendaftaran_ujikom.id) as jumlah_ujikom'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('jumlah_ujikom', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'asesor_name' => $item->asesor_name,
                    'jumlah_ujikom' => $item->jumlah_ujikom
                ];
            });

        // Tren Peminat Skema (Top 5 skema dengan pendaftaran terbanyak dalam 6 bulan terakhir)
        $trenPeminatSkema = Skema::select('skema.id', 'skema.nama')
            ->join('pendaftaran', 'skema.id', '=', 'pendaftaran.skema_id')
            ->where('pendaftaran.created_at', '>=', now()->subMonths(6))
            ->groupBy('skema.id', 'skema.nama')
            ->selectRaw('COUNT(pendaftaran.id) as total_pendaftaran')
            ->orderBy('total_pendaftaran', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'skema_nama' => $item->nama,
                    'total_pendaftaran' => $item->total_pendaftaran
                ];
            });

        $lists = $this->getMenuListPimpinan('dashboard');

        return view('components.pages.pimpinan.dashboard', compact(
            'lists',
            'totalPendaftaran',
            'totalSkema',
            'tingkatKeberhasilan',
            'totalAsesor',
            'skemaTrend',
            'statistikKeberhasilan',
            'segmentasiJenisKelamin',
            'workloadAsesor',
            'trenPeminatSkema'
        ));
    }
}
