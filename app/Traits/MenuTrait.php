<?php

namespace App\Traits;

trait MenuTrait
{
    public function getMenuList($activeMenu = 'dashboard')
    {
        $menus = [
            [
                'title' => 'Dashboard',
                'url' => 'dashboard.admin',
                'key' => 'dashboard',
            ],
            [
                'title' => 'Skema',
                'url' => 'admin.skema.index',
                'key' => 'skema'
            ],
            [
                'title' => 'TUK',
                'url' => 'admin.tuk.index',
                'key' => 'tuk'
            ],
            [
                'title' => 'Jadwal',
                'url' => 'admin.jadwal.index',
                'key' => 'jadwal'
            ],
            [
                'title' => 'User',
                'url' => 'admin.user.index',
                'key' => 'user'
            ],
            [
                'title' => 'Pembayaran',
                'url' => 'admin.pembayaran.index',
                'key' => 'pembayaran'
            ],
            [
                'title' => 'Pendaftaran',
                'url' => 'admin.pendaftaran.index',
                'key' => 'pendaftaran'
            ],
            [
                'title' => 'Pembayaran Asesor',
                'url' => 'admin.pembayaran-asesor.index',
                'key' => 'pembayaran-asesor'
            ],
            [
                'title' => 'Report',
                'url' => 'admin.report.index',
                'key' => 'report'
            ],
            [
                'title' => 'APL 2',
                'url' => 'admin.apl-2.index',
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
}
