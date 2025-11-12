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
        } else if (Route::is('dashboard.tuk')) {
            $lists = $this->getMenuListKepalaTuk('dashboard');
            return view('components.pages.tuk.dashboard', compact('lists'));
        }
        // Note: Kaprodi and Pimpinan now use dedicated DashboardControllers
    }
}
