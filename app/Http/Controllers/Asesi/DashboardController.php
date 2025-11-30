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

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        $user = Auth::user();

        // ==========================================
        // 1. KEY PERFORMANCE INDICATORS (KPIs)
        // ==========================================

        // Total pendaftaran asesi
        $totalPendaftaran = Pendaftaran::where('user_id', $user->id)->count();

        // Jadwal ujikom yang akan datang
        $jadwalUjikom = Pendaftaran::where('user_id', $user->id)
            ->whereHas('jadwal', function($query) {
                $query->where('tanggal_ujian', '>=', now())
                      ->where('status', 1); // Aktif
            })
            ->count();

        // Total Sertifikat (Kompeten)
        $totalSertifikat = Report::where('user_id', $user->id)
            ->where('status', 1) // Kompeten
            ->count();

        // Pembayaran pending
        $pembayaranPending = Pembayaran::where('user_id', $user->id)
            ->where('status', 2) // Menunggu Verifikasi
            ->count();

        // Total Skema yang Diikuti
        $totalSkema = Pendaftaran::where('user_id', $user->id)
            ->distinct('skema_id')
            ->count('skema_id');

        // Ujian yang Sudah Selesai
        $ujianSelesai = Pendaftaran::where('user_id', $user->id)
            ->where('status', 6) // Selesai
            ->count();

        // ==========================================
        // 2. SUCCESS METRICS
        // ==========================================

        // Status sertifikasi (berdasarkan report)
        $totalReport = Report::where('user_id', $user->id)->count();
        $totalKompeten = Report::where('user_id', $user->id)->where('status', 1)->count();
        $statusSertifikasi = $totalReport > 0 ? round(($totalKompeten / $totalReport) * 100, 1) : 0;

        // Tingkat Completion (Selesai / Total Pendaftaran)
        $completionRate = $totalPendaftaran > 0
            ? round(($ujianSelesai / $totalPendaftaran) * 100, 1)
            : 0;


        // ==========================================
        // 4. PERFORMANCE BY SKEMA
        // ==========================================

        $performanceSkema = Report::where('user_id', $user->id)
            ->with('skema')
            ->get()
            ->groupBy('skema_id')
            ->map(function($reports, $skemaId) {
                $total = $reports->count();
                $kompeten = $reports->where('status', 1)->count();
                $passRate = $total > 0 ? round(($kompeten / $total) * 100, 1) : 0;

                return [
                    'nama' => $reports->first()->skema->nama ?? 'Unknown',
                    'total_ujian' => $total,
                    'kompeten' => $kompeten,
                    'tidak_kompeten' => $total - $kompeten,
                    'pass_rate' => $passRate
                ];
            })
            ->sortByDesc('pass_rate')
            ->values();

        // ==========================================
        // 5. STATUS PIPELINE
        // ==========================================

        $statusPendaftaran = Pendaftaran::where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function($item) {
                $statusText = $this->getStatusText($item->status);
                return [$statusText => $item->jumlah];
            });


        // ==========================================
        // 7. JADWAL MENDATANG (Detail)
        // ==========================================

        $jadwalMendatang = Pendaftaran::where('user_id', $user->id)
            ->whereHas('jadwal', function($query) {
                $query->where('tanggal_ujian', '>=', now())
                      ->where('status', 1);
            })
            ->with(['jadwal.skema', 'jadwal.tuk', 'skema'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($pendaftaran) {
                return [
                    'tanggal_ujian' => $pendaftaran->jadwal ? $pendaftaran->jadwal->tanggal_ujian->format('d M Y H:i') : '-',
                    'skema' => $pendaftaran->skema->nama ?? 'Unknown',
                    'tuk' => $pendaftaran->jadwal->tuk->name ?? 'Unknown',
                    'status' => $this->getStatusText($pendaftaran->status),
                    'hari_lagi' => $pendaftaran->jadwal ? now()->diffInDays($pendaftaran->jadwal->tanggal_ujian, false) : null
                ];
            });

        // ==========================================
        // 8. RIWAYAT SERTIFIKASI
        // ==========================================

        $riwayatSertifikasi = Report::where('user_id', $user->id)
            ->with(['skema', 'jadwal'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($report) {
                return [
                    'tanggal' => $report->created_at->format('d M Y'),
                    'skema' => $report->skema->nama ?? 'Unknown',
                    'status' => $report->status == 1 ? 'Kompeten' : 'Tidak Kompeten',
                    'status_badge' => $report->status == 1 ? 'success' : 'danger'
                ];
            });

        // ==========================================
        // 9. AKTIVITAS TERBARU
        // ==========================================

        $aktivitas = Pendaftaran::where('user_id', $user->id)
            ->with(['jadwal', 'skema'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($pendaftaran) {
                $statusBadge = 'secondary';
                if ($pendaftaran->status == 6) $statusBadge = 'success';
                elseif (in_array($pendaftaran->status, [1, 3, 4])) $statusBadge = 'warning';
                elseif (in_array($pendaftaran->status, [2, 7])) $statusBadge = 'danger';
                elseif ($pendaftaran->status == 5) $statusBadge = 'info';

                return [
                    'tanggal' => $pendaftaran->created_at->format('d M Y H:i'),
                    'aktivitas' => 'Pendaftaran Ujikom',
                    'status' => $this->getStatusText($pendaftaran->status),
                    'status_badge' => $statusBadge,
                    'keterangan' => 'Skema: ' . ($pendaftaran->skema->nama ?? 'Tidak diketahui')
                ];
            });

        // ==========================================
        // 10. GROWTH METRICS
        // ==========================================

        $bulanIni = Pendaftaran::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $bulanLalu = Pendaftaran::where('user_id', $user->id)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $growthRate = $bulanLalu > 0
            ? round((($bulanIni - $bulanLalu) / $bulanLalu) * 100, 1)
            : ($bulanIni > 0 ? 100 : 0);

        $lists = $this->getMenuListAsesi('dashboard');

        return view('components.pages.asesi.dashboard', compact(
            'lists',
            // KPIs
            'totalPendaftaran',
            'jadwalUjikom',
            'totalSertifikat',
            'pembayaranPending',
            'totalSkema',
            'ujianSelesai',
            // Success Metrics
            'statusSertifikasi',
            'completionRate',
            // Growth
            'growthRate',
            // Performance
            'performanceSkema',
            // Pipeline
            'statusPendaftaran',
            // Upcoming
            'jadwalMendatang',
            // History
            'riwayatSertifikasi',
            // Activities
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
