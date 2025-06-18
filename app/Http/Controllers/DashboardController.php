<?php

namespace App\Http\Controllers;

use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    use MenuTrait;

    public function index()
    {
        if (Route::is('dashboard.admin')) {
            $lists = $this->getMenuListAdmin('dashboard');
            return view('components.pages.admin.dashboard', compact('lists'));
        } else if (Route::is('dashboard.asesi')) {
            $lists = $this->getMenuListAsesi('dashboard');
            return view('components.pages.asesi.dashboard', compact('lists'));
        } else if (Route::is('dashboard.asesor')) {
            $lists = $this->getMenuListAsesor('dashboard');
            return view('components.pages.asesor.dashboard', compact('lists'));
        } else if (Route::is('dashboard.kaprodi')) {
            $lists = $this->getMenuListKaprodi('dashboard');
            return view('components.pages.kaprodi.dashboard', compact('lists'));
        } else if (Route::is('dashboard.pimpinan')) {
            $lists = $this->getMenuListPimpinan('dashboard');
            return view('components.pages.pimpinan.dashboard', compact('lists'));
        }
    }
}
