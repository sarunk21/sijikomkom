<?php

namespace App\Http\Controllers\Tuk;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use App\Models\Pendaftaran;
use App\Models\Jadwal;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        $user = Auth::user();
        $lists = $this->getMenuListKepalaTuk('dashboard');

        // Get TUK ID from user (assuming user has tuk_id or get from first TUK if kepala_tuk role)
        $tukId = $user->tuk_id ?? null;

        // If no TUK assigned, show all data (fallback)
        $tukFilter = function($query) use ($tukId) {
            if ($tukId) {
                $query->where('tuk_id', $tukId);
            }
        };

        // Total Jadwal di TUK ini
        $totalJadwal = Jadwal::when($tukId, $tukFilter)->count();

        // Jadwal Aktif (status 1 = Aktif)
        $jadwalAktif = Jadwal::when($tukId, $tukFilter)
            ->where('status', 1)
            ->count();

        // Jadwal Hari Ini
        $jadwalHariIni = Jadwal::when($tukId, $tukFilter)
            ->whereDate('tanggal_ujian', today())
            ->whereIn('status', [1, 3]) // Aktif atau Sedang Berlangsung
            ->count();

        // Jadwal Selesai
        $jadwalSelesai = Jadwal::when($tukId, $tukFilter)
            ->where('status', 4) // Selesai
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
                ->count();
            $trenJadwal[] = [
                'bulan' => $bulan->format('M Y'),
                'jumlah' => $count
            ];
        }

        // Distribusi Skema di TUK ini
        $distribusiSkema = Jadwal::when($tukId, $tukFilter)
            ->with('skema')
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
            'pending' => Jadwal::when($tukId, $tukFilter)->where('status', 0)->count(),
            'aktif' => Jadwal::when($tukId, $tukFilter)->where('status', 1)->count(),
            'ditunda' => Jadwal::when($tukId, $tukFilter)->where('status', 2)->count(),
            'sedang_berlangsung' => Jadwal::when($tukId, $tukFilter)->where('status', 3)->count(),
            'selesai' => Jadwal::when($tukId, $tukFilter)->where('status', 4)->count(),
        ];

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
            'statusJadwal'
        ));
    }
}
