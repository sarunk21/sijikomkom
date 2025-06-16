<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserTypeValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userType = Auth::user()->user_type;
        $currentRoute = $request->route()->getName();

        // Jika user sudah berada di halaman yang sesuai, lanjutkan request
        if (
            ($userType === 'admin' && (str_starts_with($currentRoute, 'admin.') || $currentRoute === 'dashboard.admin')) ||
            ($userType === 'asesor' && (str_starts_with($currentRoute, 'asesor.') || $currentRoute === 'dashboard.asesor')) ||
            ($userType === 'kaprodi' && (str_starts_with($currentRoute, 'kaprodi.') || $currentRoute === 'dashboard.kaprodi')) ||
            ($userType === 'pimpinan' && (str_starts_with($currentRoute, 'pimpinan.') || $currentRoute === 'dashboard.pimpinan'))
        ) {
            return $next($request);
        }

        // Redirect ke halaman yang sesuai jika user belum berada di halaman yang benar
        if ($userType === 'admin') {
            return redirect()->route('dashboard.admin');
        }
        if ($userType === 'asesor') {
            return redirect()->route('dashboard.asesor');
        }
        if ($userType === 'kaprodi') {
            return redirect()->route('dashboard.kaprodi');
        }
        if ($userType === 'pimpinan') {
            return redirect()->route('dashboard.pimpinan');
        }

        // Jika user type tidak valid, logout dan redirect ke login
        Auth::logout();
        return redirect()->route('login')->with('error', 'Tipe pengguna tidak valid');
    }
}
