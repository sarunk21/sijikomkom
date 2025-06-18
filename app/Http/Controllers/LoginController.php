<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('components.pages.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            if (Auth::user()->user_type == 'admin') {
                return redirect()->route('dashboard.admin');
            }
            if (Auth::user()->user_type == 'asesi') {
                return redirect()->route('dashboard.asesi');
            }
            if (Auth::user()->user_type == 'asesor') {
                return redirect()->route('dashboard.asesor');
            }
            if (Auth::user()->user_type == 'kaprodi') {
                return redirect()->route('dashboard.kaprodi');
            }
            if (Auth::user()->user_type == 'pimpinan') {
                return redirect()->route('dashboard.pimpinan');
            }

            Auth::logout();
            return redirect()->route('login')->with('error', 'Tipe pengguna tidak valid');
        }

        return redirect()->route('login')->with('error', 'Email atau password salah');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
