<?php

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Admin\SkemaController;
use App\Http\Controllers\Admin\TUKController;

use Illuminate\Support\Facades\Route;

function getMenuList($activeMenu = 'dashboard') {
    $menus = [
        [
            'title' => 'Dashboard',
            'url' => 'dashboard.admin',
            'key' => 'dashboard',
        ],
        [
            'title' => 'Skema',
            'url' => 'skema.index',
            'key' => 'skema'
        ],
        [
            'title' => 'TUK',
            'url' => 'tuk.index',
            'key' => 'tuk'
        ],
        [
            'title' => 'Jadwal',
            'url' => null,
            'key' => 'jadwal'
        ],
        [
            'title' => 'User',
            'url' => null,
            'key' => 'user'
        ],
        [
            'title' => 'Pembayaran Pendaftaran',
            'url' => null,
            'key' => 'pembayaran-pendaftaran'
        ],
        [
            'title' => 'Pembayaran Asesor',
            'url' => null,
            'key' => 'pembayaran-asesor'
        ],
        [
            'title' => 'Report',
            'url' => null,
            'key' => 'report'
        ],
        [
            'title' => 'APL 2',
            'url' => null,
            'key' => 'apl-2'
        ],
        [
            'title' => 'Profile',
            'url' => null,
            'key' => 'profile'
        ]
    ];

    return collect($menus)->map(function($menu) use ($activeMenu) {
        $menu['active'] = $menu['key'] === $activeMenu;
        return $menu;
    })->all();
}

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.admin');

Route::get('/login', function () {
    return view('components.pages.login');
})->name('login');

Route::resource('skema', SkemaController::class);
Route::resource('tuk', TUKController::class);
