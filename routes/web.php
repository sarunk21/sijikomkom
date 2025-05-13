<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $lists = [
        [
            'title' => 'Dashboard',
            'url' => '/dashboard',
            'active' => true
        ],
        [
            'title' => 'Skema',
            'url' => '/skema',
            'active' => false
        ],
        [
            'title' => 'TUK',
            'url' => '/tuk',
            'active' => false
        ],
        [
            'title' => 'Jadwal',
            'url' => '/jadwal',
            'active' => false
        ],
        [
            'title' => 'User',
            'url' => '/user',
            'active' => false
        ],
        [
            'title' => 'Pembayaran Pendaftaran',
            'url' => '/pembayaran-pendaftaran',
            'active' => false
        ],
        [
            'title' => 'Pembayaran Asesor',
            'url' => '/pembayaran-asesor',
            'active' => false
        ],
        [
            'title' => 'Report',
            'url' => '/report',
            'active' => false
        ],
        [
            'title' => 'APL 2',
            'url' => '/apl-2',
            'active' => false
        ],
        [
            'title' => 'Profile',
            'url' => '/profile',
            'active' => false
        ]
    ];

    return view('components.pages.dashboard-admin', compact('lists'));
});
