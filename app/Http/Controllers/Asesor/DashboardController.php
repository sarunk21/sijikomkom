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

        // Jadwal ujikom terdekat - group by jadwal (hanya yang sedang berlangsung)
        $jadwalTerdekat = PendaftaranUjikom::where('asesor_id', $user->id)
            ->whereHas('jadwal', function($query) {
                $query->where('status', 3); // Hanya jadwal yang sedang berlangsung
            })
            ->with(['jadwal', 'jadwal.skema', 'jadwal.tuk', 'asesi'])
            ->get()
            ->groupBy('jadwal_id')
            ->map(function($items) {
                $first = $items->first();
                return [
                    'jadwal_id' => $first->jadwal_id,
                    'tanggal' => $first->jadwal->tanggal_ujian ?? 'Belum dijadwalkan',
                    'skema' => $first->jadwal->skema->nama ?? 'Tidak diketahui',
                    'tuk' => $first->jadwal->tuk->nama ?? 'Tidak diketahui',
                    'jumlah_asesi' => $items->count(),
                    'status' => $first->jadwal->status_text ?? 'Tidak diketahui'
                ];
            })
            ->sortBy('tanggal')
            ->take(5)
            ->values();

        // Pending confirmations - group by jadwal (hanya jadwal yang belum dimulai)
        $pendingConfirmations = PendaftaranUjikom::where('asesor_id', $user->id)
            ->where('asesor_confirmed', false)
            ->whereHas('jadwal', function($query) {
                $query->where('status', 1) // Hanya jadwal aktif yang belum dimulai
                    ->where('tanggal_ujian', '>', now());
            })
            ->with(['jadwal', 'jadwal.skema', 'jadwal.tuk'])
            ->get()
            ->groupBy('jadwal_id')
            ->map(function($items) {
                $first = $items->first();
                return [
                    'jadwal_id' => $first->jadwal_id,
                    'jadwal' => $first->jadwal,
                    'jumlah_asesi' => $items->count(),
                    'pendaftaran_ujikom_ids' => $items->pluck('id')->toArray(),
                    'ditugaskan_sejak' => $items->min('created_at')
                ];
            })
            ->sortBy('jadwal.tanggal_ujian')
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
            'jadwalTerdekat',
            'pendingConfirmations'
        ));
    }

    public function confirmJadwal(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'status' => 'required|in:confirmed,rejected',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $jadwalId = $request->jadwal_id;

        // Ambil semua pendaftaran ujikom untuk jadwal ini yang assigned ke asesor ini
        $pendaftaranUjikomList = PendaftaranUjikom::where('jadwal_id', $jadwalId)
            ->where('asesor_id', $user->id)
            ->where('asesor_confirmed', false)
            ->with(['jadwal', 'asesi'])
            ->get();

        if ($pendaftaranUjikomList->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada jadwal yang perlu dikonfirmasi atau sudah dikonfirmasi sebelumnya.'
            ], 404);
        }

        // Cek apakah jadwal sudah dimulai
        $jadwal = $pendaftaranUjikomList->first()->jadwal;
        if ($jadwal->tanggal_ujian <= now()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat konfirmasi karena ujian sudah dimulai atau sudah lewat.'
            ], 400);
        }

        // Update confirmation status
        if ($request->status === 'confirmed') {
            // Konfirmasi semua asesi untuk jadwal ini
            foreach ($pendaftaranUjikomList as $pendaftaranUjikom) {
                $pendaftaranUjikom->asesor_confirmed = true;
                $pendaftaranUjikom->asesor_confirmed_at = now();
                $pendaftaranUjikom->asesor_notes = $request->notes;
                $pendaftaranUjikom->save();
            }

            \Log::info("Asesor {$user->name} (ID: {$user->id}) konfirmasi hadir untuk jadwal ID {$jadwalId} dengan {$pendaftaranUjikomList->count()} asesi");

            return response()->json([
                'success' => true,
                'message' => "Konfirmasi berhasil untuk {$pendaftaranUjikomList->count()} asesi pada jadwal ini."
            ]);
        } else {
            // Rejected - simpan history dan hapus semua assignment untuk jadwal ini
            foreach ($pendaftaranUjikomList as $pendaftaranUjikom) {
                // Simpan ke rejection history
                AsesorRejectionHistory::create([
                    'pendaftaran_id' => $pendaftaranUjikom->pendaftaran_id,
                    'jadwal_id' => $pendaftaranUjikom->jadwal_id,
                    'asesor_id' => $user->id,
                    'notes' => $request->notes ?? 'Tidak dapat hadir'
                ]);

                // Hapus assignment
                $pendaftaranUjikom->delete();
            }

            // Log untuk audit
            \Log::info("Asesor {$user->name} (ID: {$user->id}) menolak jadwal ID {$jadwalId} dengan {$pendaftaranUjikomList->count()} asesi. Alasan: " . ($request->notes ?? 'Tidak dapat hadir'));

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
