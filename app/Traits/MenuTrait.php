<?php

namespace App\Traits;

trait MenuTrait
{
    public function getMenuListAdmin($activeMenu = 'dashboard', $activeSubMenu = null)
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
                'url' => null,
                'key' => 'pembayaran',
                'children' => [
                    [
                        'title' => 'Pembayaran Asesi',
                        'url' => 'admin.pembayaran-asesi.index',
                        'key' => 'pembayaran-asesi'
                    ],
                    [
                        'title' => 'Pembayaran Asesor',
                        'url' => 'admin.pembayaran-asesor.index',
                        'key' => 'pembayaran-asesor'
                    ]
                ]
            ],
            [
                'title' => 'Pendaftaran',
                'url' => 'admin.pendaftaran.index',
                'key' => 'pendaftaran'
            ],
            [
                'title' => 'Report Hasil Ujikom',
                'url' => null,
                'key' => 'report',
                'children' => [
                    [
                        'title' => 'Report Hasil Ujikom',
                        'url' => null,
                        'key' => 'report-ujikom'
                    ],
                    [
                        'title' => 'Upload Sertifikat Bertanda Tangan',
                        'url' => null,
                        'key' => 'upload-sertifikat'
                    ]
                ]
            ],
            // [
            //     'title' => 'APL 2',
            //     'url' => 'admin.apl-2.index',
            //     'key' => 'apl-2'
            // ],
            [
                'title' => 'Profile',
                'url' => 'admin.profile.index',
                'key' => 'profile'
            ]
        ];

        return collect($menus)->map(function ($menu) use ($activeMenu, $activeSubMenu) {
            // Default inactive
            $menu['active'] = $menu['key'] === $activeMenu;

            if (isset($menu['children'])) {
                $menu['children'] = collect($menu['children'])->map(function ($child) use ($activeMenu, $activeSubMenu, &$menu) {
                    $isChildActive = $child['key'] === $activeMenu || $child['key'] === $activeSubMenu;
                    $child['active'] = $isChildActive;

                    // Set parent menu active if any child is active
                    if ($isChildActive) {
                        $menu['active'] = true;
                    }

                    return $child;
                })->all();
            }

            return $menu;
        })->all();
    }
}
