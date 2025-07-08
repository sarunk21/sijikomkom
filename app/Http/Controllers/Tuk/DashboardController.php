<?php

namespace App\Http\Controllers\Tuk;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\Jadwal;
use App\Models\Report;
use App\Models\Tuk;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        $user = Auth::user();

        // Ambil TUK yang dikelola oleh user ini
        $tuk = Tuk::where('user_id', $user->id)->first();

                $lists = $this->getMenuListKepalaTuk('dashboard');

        if (!$tuk) {
            // Jika tidak ada TUK, return data kosong
            $totalAsesi = 0;
            $kapasitasTuk = 0;
            $jadwalHariIni = 0;
            $pendapatanBulanan = 0;
            $trenKunjungan = [];
            $distribusiSkema = [];
            $jadwalMingguan = [];
            $laporanBulanan = [
                'totalUjikom' => 0,
                'lulus' => 0,
                'tidakLulus' => 0,
                'persentaseLulus' => 0
            ];

            return view('components.pages.tuk.dashboard', compact('lists', 'totalAsesi', 'kapasitasTuk', 'jadwalHariIni', 'pendapatanBulanan', 'trenKunjungan', 'distribusiSkema', 'jadwalMingguan', 'laporanBulanan'));
        }

        // Total asesi di TUK ini
        $totalAsesi = Pendaftaran::where('tuk_id', $tuk->id)->count();

        // Kapasitas TUK (berdasarkan jadwal yang aktif)
        $jadwalAktif = Jadwal::where('tuk_id', $tuk->id)
            ->where('status', 1)
            ->sum('kuota');
        $pendaftaranAktif = Pendaftaran::where('tuk_id', $tuk->id)
            ->whereIn('status', [4, 5, 6])
            ->count();
        $kapasitasTuk = $jadwalAktif > 0 ? round(($pendaftaranAktif / $jadwalAktif) * 100) : 0;

        // Jadwal hari ini
        $jadwalHariIni = Pendaftaran::where('tuk_id', $tuk->id)
            ->whereHas('jadwal', function($query) {
                $query->whereDate('tanggal_ujian', today());
            })
            ->count();

        // Pendapatan bulanan (hitung berdasarkan jumlah pembayaran yang dikonfirmasi)
        $pendapatanBulanan = Pembayaran::whereHas('jadwal', function($query) use ($tuk) {
            $query->where('tuk_id', $tuk->id);
        })
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('status', 4) // Dikonfirmasi
        ->count() * 1000000; // Asumsi 1 juta per pendaftaran

        // Tren kunjungan (6 bulan terakhir)
        $trenKunjungan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $count = Pendaftaran::where('tuk_id', $tuk->id)
                ->whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->count();
            $trenKunjungan[] = [
                'bulan' => $bulan->format('M'),
                'jumlah' => $count
            ];
        }

        // Distribusi skema
        $distribusiSkema = Pendaftaran::where('tuk_id', $tuk->id)
            ->with('skema')
            ->get()
            ->groupBy('skema.nama')
            ->map(function($group) {
                return $group->count();
            });

        // Jadwal mingguan
        $jadwalMingguan = [];
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        foreach ($hari as $index => $namaHari) {
            $tanggal = now()->startOfWeek()->addDays($index);
            $jumlah = Pendaftaran::where('tuk_id', $tuk->id)
                ->whereHas('jadwal', function($query) use ($tanggal) {
                    $query->whereDate('tanggal_ujian', $tanggal);
                })
                ->count();

            $status = $tanggal->isPast() ? 'Selesai' : 'Menunggu';
            if ($tanggal->isToday()) {
                $status = 'Sedang Berlangsung';
            }

            $jadwalMingguan[] = [
                'hari' => $namaHari,
                'jumlah' => $jumlah,
                'status' => $status
            ];
        }

        // Laporan bulanan
        $totalUjikomBulan = Pendaftaran::where('tuk_id', $tuk->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lulusBulan = Report::whereHas('pendaftaran', function($query) use ($tuk) {
            $query->where('tuk_id', $tuk->id);
        })
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('status', 1)
        ->count();

        $tidakLulusBulan = Report::whereHas('pendaftaran', function($query) use ($tuk) {
            $query->where('tuk_id', $tuk->id);
        })
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('status', 2)
        ->count();

        $persentaseLulus = $totalUjikomBulan > 0 ? round(($lulusBulan / $totalUjikomBulan) * 100) : 0;

        $laporanBulanan = [
            'totalUjikom' => $totalUjikomBulan,
            'lulus' => $lulusBulan,
            'tidakLulus' => $tidakLulusBulan,
            'persentaseLulus' => $persentaseLulus
        ];

        return view('components.pages.tuk.dashboard', compact(
            'lists',
            'totalAsesi',
            'kapasitasTuk',
            'jadwalHariIni',
            'pendapatanBulanan',
            'trenKunjungan',
            'distribusiSkema',
            'jadwalMingguan',
            'laporanBulanan'
        ));
    }
}
