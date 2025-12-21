<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PendaftaranUjikom;
use App\Models\KelayankanVerifikasi;
use App\Mail\VerifikasiKelayankanMail;
use App\Mail\KelayankanDitolakMail;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class VerifikasiKelayankanController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of pendaftaran untuk verifikasi kelayakan
     */
    public function index(Request $request)
    {
        $lists = $this->getMenuListAsesor('verifikasi-kelayakan');

        // Ambil pendaftaran yang sudah didistribusikan ke asesor ini
        $query = PendaftaranUjikom::where('asesor_id', Auth::id())
            ->with([
                'pendaftaran',
                'pendaftaran.user',
                'pendaftaran.jadwal',
                'pendaftaran.jadwal.skema',
                'pendaftaran.jadwal.tuk',
                'pendaftaran.skema',
                'pendaftaran.tuk'
            ])
            ->whereHas('pendaftaran', function($q) {
                $q->where('status', 6); // Status 6: Menunggu Verifikasi Kelayakan (after doc verification)
            });

        // Filter by jadwal
        if ($request->filled('jadwal_id')) {
            $query->where('jadwal_id', $request->jadwal_id);
        }

        $pendaftaranList = $query->orderBy('created_at', 'desc')->get();

        // Get jadwal for filter
        $jadwalList = PendaftaranUjikom::where('asesor_id', Auth::id())
            ->with('jadwal', 'jadwal.skema')
            ->whereHas('pendaftaran', function($q) {
                $q->where('status', 6);
            })
            ->get()
            ->pluck('jadwal')
            ->unique('id')
            ->sortBy('tanggal_ujian');

        return view('components.pages.asesor.verifikasi-kelayakan.list', compact('lists', 'pendaftaranList', 'jadwalList'));
    }

    /**
     * Show form untuk verifikasi kelayakan
     */
    public function show($pendaftaranId)
    {
        $lists = $this->getMenuListAsesor('verifikasi-kelayakan');

        // Cek apakah asesor berhak mengakses pendaftaran ini
        $pendaftaranUjikom = PendaftaranUjikom::where('asesor_id', Auth::id())
            ->where('pendaftaran_id', $pendaftaranId)
            ->firstOrFail();

        $pendaftaran = Pendaftaran::with([
            'user',
            'jadwal',
            'jadwal.skema',
            'jadwal.tuk',
            'skema',
            'kelayankanVerifikasi',
            'kelayankanVerifikasi.asesor'
        ])->findOrFail($pendaftaranId);

        // Cek apakah sudah diverifikasi oleh asesor ini
        $existingVerifikasi = KelayankanVerifikasi::where('pendaftaran_id', $pendaftaranId)
            ->where('asesor_id', Auth::id())
            ->first();

        return view('components.pages.asesor.verifikasi-kelayakan.form', compact('lists', 'pendaftaran', 'existingVerifikasi'));
    }

    /**
     * Store verifikasi kelayakan
     */
    public function store(Request $request, $pendaftaranId)
    {
        $request->validate([
            'status' => 'required|in:1,2',
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah asesor berhak
            $pendaftaranUjikom = PendaftaranUjikom::where('asesor_id', Auth::id())
                ->where('pendaftaran_id', $pendaftaranId)
                ->firstOrFail();

            $pendaftaran = Pendaftaran::findOrFail($pendaftaranId);

            // Simpan verifikasi kelayakan
            $verifikasi = KelayankanVerifikasi::updateOrCreate(
                [
                    'pendaftaran_id' => $pendaftaranId,
                    'asesor_id' => Auth::id(),
                ],
                [
                    'status' => $request->status,
                    'catatan' => $request->catatan,
                    'verified_at' => now(),
                ]
            );

            // Update status pendaftaran
            if ($request->status == 1) {
                // Layak - langsung ke pembayaran (no admin approval needed)
                $pendaftaran->update([
                    'status' => 8, // Menunggu Pembayaran
                    'kelayakan_status' => 1, // Layak
                    'kelayakan_catatan' => $request->catatan,
                    'kelayakan_verified_at' => now(),
                    'kelayakan_verified_by' => Auth::id(),
                ]);

                // Create Pembayaran record
                \App\Models\Pembayaran::create([
                    'user_id' => $pendaftaran->user_id,
                    'jadwal_id' => $pendaftaran->jadwal_id,
                    'status' => 1, // Belum Bayar
                ]);

                // Send email notifikasi ke asesi untuk pembayaran
                try {
                    $emailService = new \App\Services\EmailService();
                    $emailService->sendMenungguPembayaranNotification($pendaftaran);
                } catch (\Exception $e) {
                    Log::error('Error sending email: ' . $e->getMessage());
                }

                $message = 'Verifikasi kelayakan berhasil! Pendaftaran dinyatakan LAYAK. Asesi dapat melakukan pembayaran.';
            } else {
                // Tidak Layak
                $pendaftaran->update([
                    'status' => 7, // Tidak Lolos Kelayakan
                    'kelayakan_status' => 2,
                    'kelayakan_catatan' => $request->catatan,
                    'kelayakan_verified_at' => now(),
                    'kelayakan_verified_by' => Auth::id(),
                ]);

                // Hapus PendaftaranUjikom karena tidak layak
                $pendaftaranUjikom->delete();

                // Send email notifikasi ke asesi
                try {
                    Mail::to($pendaftaran->user->email)
                        ->send(new KelayankanDitolakMail($pendaftaran, $request->catatan));
                } catch (\Exception $e) {
                    Log::error('Error sending email: ' . $e->getMessage());
                }

                $message = 'Verifikasi kelayakan berhasil! Pendaftaran dinyatakan TIDAK LAYAK.';
            }

            DB::commit();

            return redirect()->route('asesor.verifikasi-kelayakan.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error verifikasi kelayakan: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

