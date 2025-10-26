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

        // Cek apakah user sudah pernah mendaftar sebelumnya
        $previousRegistration = Pendaftaran::where('user_id', $user->id)->first();

        if ($previousRegistration) {
            // Cek status pembayaran terakhir
            $lastPayment = Pembayaran::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastPayment) {
                // Jika pembayaran masih pending atau belum bayar, redirect ke halaman pembayaran
                if (in_array($lastPayment->status, [1, 2])) {
                    return redirect()->route('asesi.informasi-pembayaran.index')
                        ->with('warning', 'Anda memiliki pembayaran yang belum diselesaikan. Silakan selesaikan pembayaran terlebih dahulu.');
                }

                // Jika pembayaran ditolak, tampilkan popup konfirmasi
                if ($lastPayment->status == 3) {
                    session()->flash('show_payment_popup', true);
                    session()->flash('payment_message', 'Pembayaran sebelumnya ditolak. Apakah Anda ingin mendaftar ulang?');
                }
            }
        }

        return $next($request);
    }
}
