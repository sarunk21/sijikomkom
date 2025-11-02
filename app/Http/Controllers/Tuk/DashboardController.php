<?php

namespace App\Http\Controllers\Tuk;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use App\Models\Pendaftaran;
use App\Models\Jadwal;
use App\Models\Report;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        $user = Auth::user();

        $lists = $this->getMenuListKepalaTuk('dashboard');

        // CATATAN: Saat ini tidak ada relasi User -> TUK (user.tuk_id)
        // Dashboard menampilkan data SEMUA TUK
        // Untuk filter berdasarkan TUK tertentu, perlu tambahkan kolom tuk_id di tabel users
        // Contoh: $tukId = $user->tuk_id; lalu filter semua query dengan ->where('tuk_id', $tukId)

        if (!$user) {
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

        $totalAsesi = Pendaftaran::count();

        $jadwalAktif = Jadwal::where('status', 1)
            ->sum('kuota');
        $pendaftaranAktif = Pendaftaran::whereIn('status', [4, 5, 6])
            ->count();
        $kapasitasTuk = $jadwalAktif > 0 ? round(($pendaftaranAktif / $jadwalAktif) * 100) : 0;

        // Jadwal hari ini
        $jadwalHariIni = Pendaftaran::whereIn('status', [4, 5, 6])
            ->whereHas('jadwal', function ($query) {
                $query->whereDate('tanggal_ujian', today());
            })
            ->count();

        // Pendapatan bulanan (hitung berdasarkan jumlah pembayaran yang dikonfirmasi)
        // Status Pembayaran: 1=Belum Bayar, 2=Menunggu Verifikasi, 3=Ditolak, 4=Dikonfirmasi
        $pendapatanBulanan = Pembayaran::where('status', 4) // Dikonfirmasi
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count() * config('payment.tuk_per_pendaftaran', 1000000); // Ambil dari config

        // Tren kunjungan (6 bulan terakhir)
        $trenKunjungan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $count = Pendaftaran::whereIn('status', [4, 5, 6])
                ->whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->count();
            $trenKunjungan[] = [
                'bulan' => $bulan->format('M'),
                'jumlah' => $count
            ];
        }

        // Distribusi skema
        $distribusiSkema = Pendaftaran::whereIn('status', [4, 5, 6])
            ->with('skema')
            ->get()
            ->groupBy('skema.nama')
            ->map(function ($group) {
                return $group->count();
            });

        // Jadwal mingguan
        $jadwalMingguan = [];
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        foreach ($hari as $index => $namaHari) {
            $tanggal = now()->startOfWeek()->addDays($index);
            $jumlah = Pendaftaran::whereIn('status', [4, 5, 6])
                ->whereHas('jadwal', function ($query) use ($tanggal) {
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
        $totalUjikomBulan = Pendaftaran::whereIn('status', [4, 5, 6])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Status Report: 1=Kompeten/Lulus, 2=Tidak Kompeten/Tidak Lulus
        $lulusBulan = Report::where('status', 1) // Kompeten
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $tidakLulusBulan = Report::where('status', 2) // Tidak Kompeten
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
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
