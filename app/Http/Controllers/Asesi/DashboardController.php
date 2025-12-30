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

    public function index(Request $request)
    {
        $user = Auth::user();

        // Filter parameters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $skemaId = $request->input('skema_id');

        // ==========================================
        // 1. KEY PERFORMANCE INDICATORS (KPIs)
        // ==========================================

        // Total pendaftaran asesi
        $totalPendaftaran = Pendaftaran::where('user_id', $user->id)
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();

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
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();

        // Pembayaran pending
        $pembayaranPending = Pembayaran::where('user_id', $user->id)
            ->where('status', 2) // Menunggu Verifikasi
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('created_at', '<=', $endDate);
            })
            ->count();

        // Total Skema yang Diikuti
        $totalSkema = Pendaftaran::where('user_id', $user->id)
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->distinct('skema_id')
            ->count('skema_id');

        // Ujian yang Sudah Selesai (berdasarkan Report - karena Report hanya dibuat setelah ujian selesai)
        $ujianSelesai = Report::where('user_id', $user->id)
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();

        // ==========================================
        // 2. SUCCESS METRICS
        // ==========================================

        // Status sertifikasi (berdasarkan report)
        $totalReport = Report::where('user_id', $user->id)
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();
        $totalKompeten = Report::where('user_id', $user->id)
            ->where('status', 1)
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();
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
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
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
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
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
                $tanggalUjian = null;
                $hariLagi = null;

                if ($pendaftaran->jadwal && $pendaftaran->jadwal->tanggal_ujian) {
                    $tanggalUjian = \Carbon\Carbon::parse($pendaftaran->jadwal->tanggal_ujian)->format('d M Y H:i');
                    $hariLagi = now()->diffInDays(\Carbon\Carbon::parse($pendaftaran->jadwal->tanggal_ujian), false);
                }

                return [
                    'tanggal_ujian' => $tanggalUjian ?? '-',
                    'skema' => $pendaftaran->skema->nama ?? 'Unknown',
                    'tuk' => $pendaftaran->jadwal->tuk->name ?? 'Unknown',
                    'status' => $this->getStatusText($pendaftaran->status),
                    'hari_lagi' => $hariLagi
                ];
            });

        // ==========================================
        // 8. RIWAYAT SERTIFIKASI
        // ==========================================

        $riwayatSertifikasi = Report::where('user_id', $user->id)
            ->with(['skema', 'jadwal'])
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
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
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('created_at', '<=', $endDate);
            })
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
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
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();

        $bulanLalu = Pendaftaran::where('user_id', $user->id)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->when($skemaId, function($q) use ($skemaId) {
                return $q->where('skema_id', $skemaId);
            })
            ->count();

        $growthRate = $bulanLalu > 0
            ? round((($bulanIni - $bulanLalu) / $bulanLalu) * 100, 1)
            : ($bulanIni > 0 ? 100 : 0);

        $lists = $this->getMenuListAsesi('dashboard');

        // Get all skema for filter dropdown
        $skemas = \App\Models\Skema::orderBy('nama', 'asc')->get();

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
            'aktivitas',
            // Filter data
            'skemas',
            'startDate',
            'endDate',
            'skemaId'
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
