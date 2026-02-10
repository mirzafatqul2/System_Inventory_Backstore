<?php

if (!function_exists('get_menu_items')) {
    function get_menu_items($activeMenu = '', $activeSubmenu = '')
    {
        return [
            'dashboard' => [
                'label' => 'Dashboard Utama',
                'icon' => 'fas fa-tachometer-alt',
                'url' => 'DashboardUtama',
                'active' => $activeMenu == 'dashboard',
            ],
            'data_sales' => [
                'label' => 'Data Sales',
                'icon' => 'fas fa-cash-register',
                'url' => 'data_sales',
                'active' => $activeMenu == 'data_sales',
                'submenu' => [
                    'dashboard_sales' => ['label' => 'Dashboard Sales', 'url' => 'datasales/salesdashboard', 'active' => $activeSubmenu == 'sales_dashboard'],
                    'sales_target' => ['label' => 'Sales Target', 'url' => 'datasales/salestarget', 'active' => $activeSubmenu == 'sales_target'],
                    'sales_achievement' => ['label' => 'Sales Achievement', 'url' => 'datasales/salesachievement', 'active' => $activeSubmenu == 'sales_achievement']
                ],
            ],
            'data_barang' => [
                'label' => 'Data Barang',
                'icon' => 'fas fa-boxes',
                'url' => 'data_barang',
                'active' => $activeMenu == 'data_barang',
                'submenu' => [
                    'dashboard_barang' => ['label' => 'Dashboard Barang', 'url' => 'databarang/dashboard_barang', 'active' => $activeSubmenu == 'dashboard_barang'],
                    'data_barang' => ['label' => 'List Barang', 'url' => 'databarang/listbarang', 'active' => $activeSubmenu == 'list_barang'],
                    'master_keepstock' => ['label' => 'Master KeepStock', 'url' => 'databarang/master_keepstock', 'active' => $activeSubmenu == 'master_keepstock'],
                    'data_refill' => ['label' => 'Refill KeepStock', 'url' => 'databarang/data_refill', 'active' => $activeSubmenu == 'data_refill'],
                 ]
            ],
            'data_omnimbus' => [
                'label' => 'Data Omnimbus',
                'icon' => 'fas fa-tasks',
                'url' => 'data_omnimbus',
                'active' => $activeMenu == 'data_omnimbus',
                'submenu' => [
                    'dashboard_omnimbus' => ['label' => 'Dashboard Omnimbus', 'url' => 'dataomnimbus/dashboard_omnimbus', 'active' => $activeSubmenu == 'dashboard_omnimbus'],
                    'data_damage' => ['label' => 'Damage', 'url' => 'dataomnimbus/data_damage', 'active' => $activeSubmenu == 'data_damage'],
                    'data_ceklist' => ['label' => 'Stock Ceklist', 'url' => 'dataomnimbus/data_ceklist', 'active' => $activeSubmenu == 'data_ceklist'],
                    'datang_barang' => ['label' => 'Datang Barang (SKU IB)', 'url'=>'dataomnimbus/datang_barang', 'active' => $activeSubmenu =='datang_barang']
                ]
            ],
            'data_pettycash' => [
                'label' => 'Data Petty Cash',
                'icon' => 'fas fa-money-bill-wave',
                'url' => 'data_pettycash',
                'active' => $activeMenu == 'data_pettycash',
                'submenu' => [
                    'dashboard_pettycash' => ['label' => 'Dashboard Petty Cash', 'url' => 'pettycash/dashboard_pettycash', 'active' => $activeSubmenu == 'dashboard_pettycash'],
                    'claim_pettycash' => ['label' => 'Claim Petty Cash', 'url' => 'pettycash/claim_pettycash', 'active' => $activeSubmenu == 'claim_pettycash'],
                ]
            ],
            'data_karyawan' => [
                'label' => 'Data Karyawan',
                'icon' => 'fas fa-user-alt',
                'url' => 'Employee',
                'active' => $activeMenu == 'data_karyawan',
            ],
            'logout' => [
                'label' => 'Log Out',
                'icon' => 'fas fa-sign-out-alt',
                'url' => 'auth/logout_user',
                'active' => false,
            ]
        ];
    }
}
