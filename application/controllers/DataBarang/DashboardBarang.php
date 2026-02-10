<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardBarang extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModelDashboardBarang');
    }

    public function index()
    {
        $menu = 'data_barang';
        $submenu = 'dashboard_barang';
        $menuItems = get_menu_items($menu, $submenu);

        $kpi = $this->ModelDashboardBarang->get_kpi_box_summary();
        $boxPerDepartemen = $this->ModelDashboardBarang->get_box_per_departemen();
        $amountPerDepartemen = $this->ModelDashboardBarang->get_total_amount_per_departemen();
        $trenRefill = $this->ModelDashboardBarang->get_tren_refill_per_bulan();
        $skuStokKritis = $this->ModelDashboardBarang->get_sku_stok_kritis();

        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $refillYesterday = $this->ModelDashboardBarang->get_refill_by_date($yesterday);

        $data = [
            'title' => 'Dashboard Barang',
            'subtitle' => 'Business Intelligence Inventory',
            'isi' => 'Barang/Dashboard/view',
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems,
            'kpi' => $kpi,
            'boxPerDepartemen' => $boxPerDepartemen,
            'amountPerDepartemen' => $amountPerDepartemen,
            'trenRefill' => $trenRefill,
            'skuStokKritis' => $skuStokKritis,
            'refillYesterday' => $refillYesterday,
        ];

        $this->load->view('layout/wrapper', $data, false);
    }
}
