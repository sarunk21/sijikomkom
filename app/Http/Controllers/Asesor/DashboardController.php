<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Pendaftaran;
use App\Models\Report;
use App\Models\PembayaranAsesor;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        $user = Auth::user();

        // Total ujikom yang dihandle asesor
        $totalUjikom = Report::where('user_id', $user->id)->count();

        // Jadwal hari ini
        $jadwalHariIni = Pendaftaran::whereHas('jadwal', function($query) {
            $query->whereDate('tanggal_ujian', today());
        })->count();

        // Rata-rata nilai (berdasarkan report kompeten)
        $totalKompeten = Report::where('user_id', $user->id)->where('status', 1)->count();
        $totalReport = Report::where('user_id', $user->id)->count();
        $rataNilai = $totalReport > 0 ? round(($totalKompeten / $totalReport) * 100) : 0;

        // Pembayaran jasa (hitung berdasarkan jumlah ujikom yang selesai)
        $pembayaranJasa = PembayaranAsesor::where('asesor_id', $user->id)
            ->where('status', 3) // Selesai
            ->count() * 500000; // Asumsi 500rb per ujikom

        // Performa ujikom bulanan (6 bulan terakhir)
        $performaUjikom = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $count = Report::where('user_id', $user->id)
                ->whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->count();
            $performaUjikom[] = [
                'bulan' => $bulan->format('M'),
                'jumlah' => $count
            ];
        }

        // Status penilaian
        $statusPenilaian = Report::where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function($item) {
                $statusText = $item->status == 1 ? 'Lulus' : 'Tidak Lulus';
                return [$statusText => $item->jumlah];
            });

        // Jadwal ujikom terdekat
        $jadwalTerdekat = Pendaftaran::whereHas('jadwal', function($query) {
            $query->where('tanggal_ujian', '>=', now());
        })
        ->with(['user', 'jadwal', 'skema', 'tuk'])
        ->get()
        ->sortBy(function($pendaftaran) {
            return $pendaftaran->jadwal->tanggal_ujian ?? now()->addYears(10);
        })
        ->take(5)
        ->map(function($pendaftaran) {
            return [
                'tanggal' => $pendaftaran->jadwal->tanggal_ujian,
                'nama' => $pendaftaran->user->name,
                'skema' => $pendaftaran->skema->nama ?? 'Tidak diketahui',
                'tuk' => $pendaftaran->tuk->nama ?? 'Tidak diketahui',
                'status' => $this->getStatusText($pendaftaran->status)
            ];
        })
        ->values();

        $lists = $this->getMenuListAsesor('dashboard');

        return view('components.pages.asesor.dashboard', compact(
            'lists',
            'totalUjikom',
            'jadwalHariIni',
            'rataNilai',
            'pembayaranJasa',
            'performaUjikom',
            'statusPenilaian',
            'jadwalTerdekat'
        ));
    }

    private function getStatusText($status)
    {
        $statusMap = [
            1 => 'Menunggu Verifikasi Kaprodi',
            2 => 'Tidak Lolos Verifikasi Kaprodi',
            3 => 'Menunggu Verifikasi Admin',
            4 => 'Menunggu Ujian',
            5 => 'Ujian Berlangsung',
            6 => 'Selesai',
            7 => 'Asesor Tidak Dapat Hadir',
        ];

        return $statusMap[$status] ?? 'Tidak Diketahui';
    }
}
