<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;

class CheckSecondRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // NEW FLOW: Cek apakah user memiliki pendaftaran yang sedang diproses
        // PENTING: Cek semua pendaftaran aktif, tidak hanya satu
        $activeRegistrations = Pendaftaran::where('user_id', $user->id)
            ->whereIn('status', [1, 5, 6, 8, 9, 10]) // Status yang masih dalam proses (updated flow)
            ->get();

        if ($activeRegistrations->isNotEmpty()) {
            // Prioritaskan yang perlu pembayaran (status 8)
            $needsPayment = $activeRegistrations->where('status', 8)->first();
            $activeRegistration = $needsPayment ?? $activeRegistrations->first();

            // Jika ada pendaftaran aktif, cek statusnya
            $statusMessages = [
                1 => 'Pendaftaran Anda sedang menunggu distribusi asesor. Silakan isi APL 1 dan APL 2.',
                5 => 'Pendaftaran Anda sedang menunggu verifikasi dokumen administratif.',
                6 => 'Pendaftaran Anda sedang menunggu verifikasi kelayakan dari asesor.',
                8 => 'Pendaftaran Anda sudah disetujui. Silakan selesaikan pembayaran.',
                9 => 'Anda sudah terdaftar dan menunggu ujian dimulai.',
                10 => 'Ujian sedang berlangsung.',
            ];

            $message = $statusMessages[$activeRegistration->status] ?? 'Anda memiliki pendaftaran yang sedang diproses.';

            // Jika status 8 (menunggu pembayaran), redirect ke halaman pembayaran
            if ($activeRegistration->status == 8) {
                // Cek apakah pembayaran sudah belum bayar/pending
                $pendingPayment = Pembayaran::where('user_id', $user->id)
                    ->where('jadwal_id', $activeRegistration->jadwal_id)
                    ->whereIn('status', [1, 2])
                    ->first();

                if ($pendingPayment) {
                    return redirect()->route('asesi.informasi-pembayaran.index')
                        ->with('warning', 'Pendaftaran Anda sudah disetujui! Silakan selesaikan pembayaran terlebih dahulu.');
                }
            }

            // Untuk status lain, redirect ke sertifikasi (agar user bisa lihat progressnya)
            return redirect()->route('asesi.sertifikasi.index')
                ->with('info', $message);
        }

        // Cek pembayaran pending (untuk backward compatibility)
        $pendingPayment = Pembayaran::where('user_id', $user->id)
            ->whereIn('status', [1, 2])
            ->first();

        if ($pendingPayment) {
            return redirect()->route('asesi.informasi-pembayaran.index')
                ->with('warning', 'Anda memiliki pembayaran yang belum diselesaikan. Silakan selesaikan pembayaran terlebih dahulu.');
        }

        return $next($request);
    }
}
