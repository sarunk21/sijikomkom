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
                        'url' => 'admin.report.index',
                        'key' => 'report-ujikom'
                    ],
                    [
                        'title' => 'Upload Sertifikat Bertanda Tangan',
                        'url' => 'admin.upload-sertifikat.index',
                        'key' => 'upload-sertifikat'
                    ]
                ]
            ],
            [
                'title' => 'APL 2',
                'url' => 'admin.apl-2.index',
                'key' => 'apl-2'
            ],
            [
                'title' => 'Profile',
                'url' => 'profile.index',
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

    public function getMenuListAsesi($activeMenu = 'dashboard', $activeSubMenu = null)
    {
        $menus = [
            [
                'title' => 'Dashboard',
                'url' => 'dashboard.asesi',
                'key' => 'dashboard'
            ],
            [
                'title' => 'Daftar Ujikom',
                'url' => 'asesi.daftar-ujikom.index',
                'key' => 'daftar-ujikom'
            ],
            [
                'title' => 'Sertifikasi',
                'url' => 'asesi.sertifikasi.index',
                'key' => 'sertifikasi'
            ],
            [
                'title' => 'Pembayaran',
                'url' => 'asesi.informasi-pembayaran.index',
                'key' => 'informasi-pembayaran'
            ],
            [
                'title' => 'Ujikom',
                'url' => 'asesi.ujikom.index',
                'key' => 'ujikom'
            ],
            [
                'title' => 'Upload Sertifikat',
                'url' => 'asesi.upload-sertifikat.index',
                'key' => 'upload-sertifikat'
            ],
            [
                'title' => 'Profil',
                'url' => 'profile.index',
                'key' => 'profil-asesi'
            ],
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

    public function getMenuListAsesor($activeMenu = 'dashboard', $activeSubMenu = null)
    {
        $menus = [
            [
                'title' => 'Dashboard',
                'url' => 'dashboard.asesor',
                'key' => 'dashboard'
            ],
            [
                'title' => 'Verifikasi Peserta',
                'url' => 'asesor.verifikasi-peserta.index',
                'key' => 'verifikasi-peserta'
            ],
            [
                'title' => 'Pembayaran Jasa',
                'url' => 'asesor.pembayaran-jasa.index',
                'key' => 'pembayaran-jasa'
            ],
            [
                'title' => 'Hasil Ujikom',
                'url' => 'asesor.hasil-ujikom.index',
                'key' => 'hasil-ujikom'
            ],
            [
                'title' => 'Profil',
                'url' => 'asesor.profil-asesor.index',
                'key' => 'profil-asesor'
            ],
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

    public function getMenuListKaprodi($activeMenu = 'dashboard', $activeSubMenu = null)
    {
        $menus = [
            [
                'title' => 'Dashboard',
                'url' => 'dashboard.kaprodi',
                'key' => 'dashboard'
            ],
            [
                'title' => 'Report',
                'url' => 'kaprodi.report-hasil-uji.index',
                'key' => 'report-hasil-uji'
            ],
            [
                'title' => 'Verifikasi Pendaftaran',
                'url' => 'kaprodi.verifikasi-pendaftaran.index',
                'key' => 'verifikasi-pendaftaran'
            ],
            [
                'title' => 'Profil',
                'url' => 'profile.index',
                'key' => 'profil-kaprodi'
            ],
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

    public function getMenuListPimpinan($activeMenu = 'dashboard', $activeSubMenu = null)
    {
        $menus = [
            [
                'title' => 'Dashboard',
                'url' => 'dashboard.pimpinan',
                'key' => 'dashboard'
            ],
            [
                'title' => 'Report',
                'url' => 'pimpinan.report-pimpinan.index',
                'key' => 'report-pimpinan'
            ],
            [
                'title' => 'Laporan IKU',
                'url' => 'pimpinan.laporan-iku.index',
                'key' => 'laporan-iku'
            ],
            [
                'title' => 'Profil',
                'url' => 'profile.index',
                'key' => 'profil-pimpinan'
            ],
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
