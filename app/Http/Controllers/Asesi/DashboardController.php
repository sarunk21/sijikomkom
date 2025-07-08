<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\Jadwal;
use App\Models\Pembayaran;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        $user = Auth::user();

        // Total pendaftaran asesi
        $totalPendaftaran = Pendaftaran::where('user_id', $user->id)->count();

        // Jadwal ujikom yang akan datang
        $jadwalUjikom = Pendaftaran::where('user_id', $user->id)
            ->whereHas('jadwal', function($query) {
                $query->where('tanggal_ujian', '>=', now());
            })
            ->count();

        // Status sertifikasi (berdasarkan report)
        $report = Report::where('user_id', $user->id)->first();
        $statusSertifikasi = $report ? ($report->status == 1 ? 100 : 0) : 0;

        // Pembayaran pending
        $pembayaranPending = Pembayaran::where('user_id', $user->id)
            ->where('status', 2) // Menunggu Verifikasi
            ->count();

        // Tren pendaftaran (6 bulan terakhir)
        $trenPendaftaran = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $count = Pendaftaran::where('user_id', $user->id)
                ->whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->count();
            $trenPendaftaran[] = [
                'bulan' => $bulan->format('M'),
                'jumlah' => $count
            ];
        }

        // Status pendaftaran
        $statusPendaftaran = Pendaftaran::where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function($item) {
                $statusText = $this->getStatusText($item->status);
                return [$statusText => $item->jumlah];
            });

        // Aktivitas terbaru
        $aktivitas = Pendaftaran::where('user_id', $user->id)
            ->with(['jadwal', 'skema'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($pendaftaran) {
                return [
                    'tanggal' => $pendaftaran->created_at->format('Y-m-d'),
                    'aktivitas' => 'Pendaftaran Ujikom',
                    'status' => $this->getStatusText($pendaftaran->status),
                    'keterangan' => 'Skema: ' . $pendaftaran->skema->nama ?? 'Tidak diketahui'
                ];
            });

        $lists = $this->getMenuListAsesi('dashboard');

        return view('components.pages.asesi.dashboard', compact(
            'lists',
            'totalPendaftaran',
            'jadwalUjikom',
            'statusSertifikasi',
            'pembayaranPending',
            'trenPendaftaran',
            'statusPendaftaran',
            'aktivitas'
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
