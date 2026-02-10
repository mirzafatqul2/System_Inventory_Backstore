<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardOmnimbus extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModelDashboardOmnimbus');
    }

    public function index()
    {
        // KPI utama
        $totalShort   = $this->ModelDashboardOmnimbus->get_total_short_value();
        $totalExtra   = $this->ModelDashboardOmnimbus->get_total_extra_value();
        $totalMissing = $this->ModelDashboardOmnimbus->get_total_missing_value();
        $netLoss      = ($totalShort + $totalMissing) - $totalExtra;

        // Breakdown kerugian
        $lossPerAssignment     = $this->ModelDashboardOmnimbus->get_loss_per_assignment();
        $lossPerKategoriDamage = $this->ModelDashboardOmnimbus->get_loss_per_kategory();

        // Tren bulanan: short, extra, missing
        $trendShortExtraMissing = $this->ModelDashboardOmnimbus->get_trend_bulanan_short_extra_missing();

        // Riwayat terbaru
        $recentSC     = $this->ModelDashboardOmnimbus->get_recent_stockcheck(5);
        $recentDamage = $this->ModelDashboardOmnimbus->get_recent_damage(5);

        // Menu navigasi
        $menu = 'data_omnimbus';
        $submenu = 'dashboard_omnimbus';
        $menuItems = get_menu_items($menu, $submenu);

        // Data dikirim ke view
        $data = [
            'title'        => 'Dashboard Omnimbus',
            'subtitle'     => 'Stock Checklist & Damage Loss',
            'isi'          => 'DataOmnimbus/Dashboard/view', // pastikan views/DataOmnimbus/Dashboard/view.php ada
            'menu'         => $menu,
            'submenu'      => $submenu,
            'menuItems'    => $menuItems,

            'totalShort'   => $totalShort,
            'totalExtra'   => $totalExtra,
            'totalMissing' => $totalMissing,
            'netLoss'      => $netLoss,

            'lossPerAssignment'       => $lossPerAssignment,
            'lossPerKategoriDamage'   => $lossPerKategoriDamage,
            'trendShortExtraMissing'  => $trendShortExtraMissing,
            'recentSC'                => $recentSC,
            'recentDamage'            => $recentDamage,
        ];

        $this->load->view('layout/wrapper', $data, false);
    }
}
