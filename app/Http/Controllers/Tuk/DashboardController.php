<?php

namespace App\Http\Controllers\Tuk;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use App\Models\Pendaftaran;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index(Request $request)
    {
        $user = Auth::user();
        $lists = $this->getMenuListKepalaTuk('dashboard');

        // Filter parameters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $skemaId = $request->input('skema_id');

        // Get TUK ID from user (assuming user has tuk_id or get from first TUK if kepala_tuk role)
        $tukId = $user->tuk_id ?? null;

        // If no TUK assigned, show all data (fallback)
        $tukFilter = function($query) use ($tukId) {
            if ($tukId) {
                $query->where('tuk_id', $tukId);
            }
        };

        // Total Jadwal di TUK ini
        $totalJadwal = Jadwal::when($tukId, $tukFilter)
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('tanggal_ujian', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('tanggal_ujian', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();

        // Jadwal Aktif (status 1 = Aktif)
        $jadwalAktif = Jadwal::when($tukId, $tukFilter)
            ->where('status', 1)
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('tanggal_ujian', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('tanggal_ujian', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();

        // Jadwal Hari Ini
        $jadwalHariIni = Jadwal::when($tukId, $tukFilter)
            ->whereDate('tanggal_ujian', today())
            ->whereIn('status', [1, 3]) // Aktif atau Sedang Berlangsung
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();

        // Jadwal Selesai
        $jadwalSelesai = Jadwal::when($tukId, $tukFilter)
            ->where('status', 4) // Selesai
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('tanggal_ujian', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('tanggal_ujian', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();

        // Total Asesi/Peserta di TUK ini (dari pendaftaran yang terhubung ke jadwal TUK)
        $totalAsesi = Pendaftaran::whereHas('jadwal', function($query) use ($tukId, $tukFilter) {
            $query->when($tukId, $tukFilter);
        })->count();

        // Tren Jadwal Ujikom (12 bulan terakhir)
        $trenJadwal = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $count = Jadwal::when($tukId, $tukFilter)
                ->whereMonth('tanggal_ujian', $bulan->month)
                ->whereYear('tanggal_ujian', $bulan->year)
                ->when($skemaId, function($q) use ($skemaId) {
                    return $q->where('skema_id', $skemaId);
                })
                ->count();
            $trenJadwal[] = [
                'bulan' => $bulan->format('M Y'),
                'jumlah' => $count
            ];
        }

        // Distribusi Skema di TUK ini
        $distribusiSkema = Jadwal::when($tukId, $tukFilter)
            ->with('skema')
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('tanggal_ujian', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('tanggal_ujian', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->get()
            ->groupBy('skema.nama')
            ->map(function ($group) {
                return $group->count();
            });

        // Jadwal Minggu Ini (per hari)
        $jadwalMingguan = [];
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        foreach ($hari as $index => $namaHari) {
            $tanggal = now()->startOfWeek()->addDays($index);
            $jumlah = Jadwal::when($tukId, $tukFilter)
                ->whereDate('tanggal_ujian', $tanggal)
                ->count();

            $status = $tanggal->isPast() ? 'Selesai' : 'Akan Datang';
            if ($tanggal->isToday()) {
                $status = 'Hari Ini';
            }

            $jadwalMingguan[] = [
                'hari' => $namaHari,
                'tanggal' => $tanggal->format('d M'),
                'jumlah' => $jumlah,
                'status' => $status
            ];
        }

        // Jadwal Mendatang (5 jadwal terdekat)
        $jadwalMendatang = Jadwal::when($tukId, $tukFilter)
            ->with(['skema', 'tuk'])
            ->where('tanggal_ujian', '>=', now())
            ->whereIn('status', [1, 3]) // Aktif atau Sedang Berlangsung
            ->orderBy('tanggal_ujian', 'asc')
            ->take(5)
            ->get();

        // Statistik Jadwal per Status
        $statusJadwal = [
            'pending' => Jadwal::when($tukId, $tukFilter)
                ->where('status', 0)
                ->when($startDate, function($q) use ($startDate) {
                    return $q->whereDate('tanggal_ujian', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    return $q->whereDate('tanggal_ujian', '<=', $endDate);
                })
                ->when($skemaId, function($q) use ($skemaId) {
                    return $q->where('skema_id', $skemaId);
                })
                ->count(),
            'aktif' => Jadwal::when($tukId, $tukFilter)
                ->where('status', 1)
                ->when($startDate, function($q) use ($startDate) {
                    return $q->whereDate('tanggal_ujian', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    return $q->whereDate('tanggal_ujian', '<=', $endDate);
                })
                ->when($skemaId, function($q) use ($skemaId) {
                    return $q->where('skema_id', $skemaId);
                })
                ->count(),
            'ditunda' => Jadwal::when($tukId, $tukFilter)
                ->where('status', 2)
                ->when($startDate, function($q) use ($startDate) {
                    return $q->whereDate('tanggal_ujian', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    return $q->whereDate('tanggal_ujian', '<=', $endDate);
                })
                ->when($skemaId, function($q) use ($skemaId) {
                    return $q->where('skema_id', $skemaId);
                })
                ->count(),
            'sedang_berlangsung' => Jadwal::when($tukId, $tukFilter)
                ->where('status', 3)
                ->when($startDate, function($q) use ($startDate) {
                    return $q->whereDate('tanggal_ujian', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    return $q->whereDate('tanggal_ujian', '<=', $endDate);
                })
                ->when($skemaId, function($q) use ($skemaId) {
                    return $q->where('skema_id', $skemaId);
                })
                ->count(),
            'selesai' => Jadwal::when($tukId, $tukFilter)
                ->where('status', 4)
                ->when($startDate, function($q) use ($startDate) {
                    return $q->whereDate('tanggal_ujian', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    return $q->whereDate('tanggal_ujian', '<=', $endDate);
                })
                ->when($skemaId, function($q) use ($skemaId) {
                    return $q->where('skema_id', $skemaId);
                })
                ->count(),
        ];

        // Get all skema for filter dropdown
        $skemas = \App\Models\Skema::orderBy('nama', 'asc')->get();

        return view('components.pages.tuk.dashboard', compact(
            'lists',
            'totalJadwal',
            'jadwalAktif',
            'jadwalHariIni',
            'jadwalSelesai',
            'totalAsesi',
            'trenJadwal',
            'distribusiSkema',
            'jadwalMingguan',
            'jadwalMendatang',
            'statusJadwal',
            // Filter data
            'skemas',
            'startDate',
            'endDate',
            'skemaId'
        ));
    }
}
