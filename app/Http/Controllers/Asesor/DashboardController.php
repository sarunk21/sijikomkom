<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Pendaftaran;
use App\Models\Report;
use App\Models\PembayaranAsesor;
use App\Models\PendaftaranUjikom;
use App\Models\AsesorRejectionHistory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        $user = Auth::user();

        // Total ujikom yang dihandle asesor (via pendaftaran_ujikom)
        $totalUjikom = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
            $query->where('asesor_id', $user->id);
        })->count();

        // Jadwal hari ini (hanya yang ditangani asesor ini)
        $jadwalHariIni = Pendaftaran::whereHas('pendaftaranUjikom', function($query) use ($user) {
            $query->where('asesor_id', $user->id);
        })
        ->whereHas('jadwal', function($query) {
            $query->whereDate('tanggal_ujian', today());
        })->count();

        // Rata-rata nilai (berdasarkan report kompeten yang ditangani asesor ini)
        $totalKompeten = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
            $query->where('asesor_id', $user->id);
        })->where('status', 1)->count();
        $totalReport = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
            $query->where('asesor_id', $user->id);
        })->count();
        $rataNilai = $totalReport > 0 ? round(($totalKompeten / $totalReport) * 100) : 0;

        // Pembayaran jasa (hitung berdasarkan jumlah ujikom yang selesai)
        $pembayaranJasa = PembayaranAsesor::where('asesor_id', $user->id)
            ->where('status', 3) // Selesai
            ->count() * config('payment.asesor_per_ujikom', 500000); // Ambil dari config

        // Performa ujikom bulanan (6 bulan terakhir) yang ditangani asesor ini
        $performaUjikom = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $count = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
                $query->where('asesor_id', $user->id);
            })
                ->whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->count();
            $performaUjikom[] = [
                'bulan' => $bulan->format('M'),
                'jumlah' => $count
            ];
        }

        // Status penilaian yang ditangani asesor ini
        $statusPenilaian = Report::whereHas('pendaftaran.pendaftaranUjikom', function($query) use ($user) {
                $query->where('asesor_id', $user->id);
            })
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function($item) {
                $statusText = $item->status == 1 ? 'Lulus' : 'Tidak Lulus';
                return [$statusText => $item->jumlah];
            });

        // Jadwal ujikom terdekat yang ditangani asesor ini
        $jadwalTerdekat = Pendaftaran::whereHas('pendaftaranUjikom', function($query) use ($user) {
                $query->where('asesor_id', $user->id);
            })
            ->whereHas('jadwal', function($query) {
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
                    'tanggal' => $pendaftaran->jadwal->tanggal_ujian ?? 'Belum dijadwalkan',
                    'nama' => $pendaftaran->user->name ?? 'Tidak diketahui',
                    'skema' => $pendaftaran->skema->nama ?? 'Tidak diketahui',
                    'tuk' => $pendaftaran->tuk->nama ?? 'Tidak diketahui',
                    'status' => $this->getStatusText($pendaftaran->status)
                ];
            })
            ->values();

        // Pending confirmations - jadwal yang belum dikonfirmasi asesor
        $pendingConfirmations = PendaftaranUjikom::where('asesor_id', $user->id)
            ->where('asesor_confirmed', false)
            ->whereHas('jadwal', function($query) {
                $query->where('tanggal_ujian', '>=', now()); // Only future jadwal
            })
            ->with(['asesi', 'jadwal', 'jadwal.skema', 'jadwal.tuk'])
            ->orderBy('created_at', 'asc')
            ->get();

        $lists = $this->getMenuListAsesor('dashboard');

        return view('components.pages.asesor.dashboard', compact(
            'lists',
            'totalUjikom',
            'jadwalHariIni',
            'rataNilai',
            'pembayaranJasa',
            'performaUjikom',
            'statusPenilaian',
            'jadwalTerdekat',
            'pendingConfirmations'
        ));
    }

    public function confirmJadwal(Request $request)
    {
        $request->validate([
            'pendaftaran_ujikom_id' => 'required|exists:pendaftaran_ujikom,id',
            'status' => 'required|in:confirmed,rejected',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $pendaftaranUjikom = PendaftaranUjikom::findOrFail($request->pendaftaran_ujikom_id);

        // Verify this is assigned to the logged-in asesor
        if ($pendaftaranUjikom->asesor_id != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengkonfirmasi jadwal ini.'
            ], 403);
        }

        // Update confirmation status
        if ($request->status === 'confirmed') {
            $pendaftaranUjikom->asesor_confirmed = true;
            $pendaftaranUjikom->asesor_confirmed_at = now();
            $pendaftaranUjikom->asesor_notes = $request->notes;
            $pendaftaranUjikom->save();

            return response()->json([
                'success' => true,
                'message' => 'Konfirmasi kehadiran berhasil.'
            ]);
        } else {
            // Rejected - simpan history penolakan dan hapus assignment

            // Simpan ke rejection history agar asesor ini tidak dapat assignment yang sama lagi
            AsesorRejectionHistory::create([
                'pendaftaran_id' => $pendaftaranUjikom->pendaftaran_id,
                'jadwal_id' => $pendaftaranUjikom->jadwal_id,
                'asesor_id' => $user->id,
                'notes' => $request->notes ?? 'Tidak dapat hadir'
            ]);

            // Log untuk audit
            \Log::info("Asesor {$user->name} (ID: {$user->id}) menolak jadwal untuk asesi {$pendaftaranUjikom->asesi->name}. Alasan: " . ($request->notes ?? 'Tidak dapat hadir'));

            // Hapus assignment ini agar sistem distribusi bisa assign asesor baru
            $pendaftaranUjikom->delete();

            return response()->json([
                'success' => true,
                'message' => 'Penolakan berhasil diproses. Sistem akan mencari asesor pengganti untuk jadwal ini.'
            ]);
        }
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
