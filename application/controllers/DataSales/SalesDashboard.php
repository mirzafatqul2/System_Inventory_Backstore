<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SalesDashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModelSalesDashboard');

        // Optional: Batasi hanya untuk AJAX
        if (!$this->input->is_ajax_request() && $this->router->method !== 'index') {
            show_error('No direct script access allowed', 403);
        }
    }

    public function index()
    {
        $menu = 'data_sales';
        $submenu = 'sales_dashboard';
        $menuItems = get_menu_items($menu, $submenu); // Asumsikan fungsi helper

        $data = [
            'title' => 'Dashboard Sales',
            'subtitle' => 'Chart Dashboard Sales',
            'isi' => 'Sales/SalesDashboard/view',
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems,
        ];

        $this->load->view('layout/wrapper', $data, false);
    }

    public function get_mtd_data()
    {
        header('Content-Type: application/json');

        $bulan = date('m');
        $tahun = date('Y');

        $mtd_sales = $this->ModelSalesDashboard->get_mtd_sales($bulan, $tahun);
        $mtd_target = $this->ModelSalesDashboard->get_mtd_target($bulan, $tahun);
        $mtd_last_month = $this->ModelSalesDashboard->get_mtd_last_month($bulan, $tahun);

        $achievement = $mtd_target > 0 ? ($mtd_sales / $mtd_target) * 100 : 0;
        $growth = $mtd_last_month > 0 ? (($mtd_sales / $mtd_last_month) * 100 - 100) : 0;

        $result = [
            'mtd_sales' => $mtd_sales,
            'mtd_target' => $mtd_target,
            'achievement' => round($achievement, 2),
            'growth' => round($growth, 2),
        ];

        echo json_encode($result);
    }

    public function get_kpi_data()
    {
        header('Content-Type: application/json');

        $bulan = date('m');
        $tahun = date('Y');

        $growth = $this->ModelSalesDashboard->get_growth_metrics($bulan, $tahun);

        $result = [
            'growth_sales' => round($growth->sales_growth, 2),
            'growth_upt'   => round($growth->upt_growth, 2),
            'growth_atv'   => round($growth->atv_growth, 2),
            'growth_scr'   => round($growth->scr_growth, 2),
        ];

        echo json_encode($result);
    }

    public function get_monthly_sales_chart_data()
    {
        header('Content-Type: application/json');

        $tahun = date('Y');
        $tahun_lalu = $tahun - 1;

        $this_year_data = $this->ModelSalesDashboard->get_monthly_sales($tahun);
        $last_year_data = $this->ModelSalesDashboard->get_monthly_sales_last_year($tahun_lalu);

        $labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $this_year = array_fill(0,12,0);
        $last_year = array_fill(0,12,0);

        foreach($this_year_data as $row){
            $this_year[$row->bulan-1] = (float)$row->total_sales;
        }

        foreach($last_year_data as $row){
            $last_year[$row->bulan-1] = (float)$row->total_sales;
        }

        $result = [
            'labels' => $labels,
            'this_year' => $this_year,
            'last_year' => $last_year
        ];

        echo json_encode($result);
    }

    public function get_daily_sales_chart_data()
    {
        header('Content-Type: application/json');

        $bulan = date('m');
        $tahun = date('Y');
        $tahun_lalu = $tahun - 1;

        $daily_targets = $this->ModelSalesDashboard->get_daily_targets($bulan, $tahun);
        $daily_achievements = $this->ModelSalesDashboard->get_daily_achievements($bulan, $tahun);
        $daily_last_year = $this->ModelSalesDashboard->get_daily_achievements_last_year($bulan, $tahun_lalu);

        $hari_terakhir = date('t');
        $labels = [];
        $targets = [];
        $achievements = [];
        $last_year = [];

        for($i=1;$i<=$hari_terakhir;$i++){
            $labels[] = $i;
            $targets[] = isset($daily_targets[$i]) ? (float)$daily_targets[$i] : 0;
            $achievements[] = isset($daily_achievements[$i]) ? (float)$daily_achievements[$i] : 0;
            $last_year[] = isset($daily_last_year[$i]) ? (float)$daily_last_year[$i] : 0;
        }

        $result = [
            'labels' => $labels,
            'targets' => $targets,
            'achievements' => $achievements,
            'last_year' => $last_year
        ];

        echo json_encode($result);
    }

    // Optional: Untuk ambil label harian (jika ingin grafik custom)
    public function get_daily_labels()
    {
        header('Content-Type: application/json');
        $bulan = date('m');
        $tahun = date('Y');

        $labels = $this->ModelSalesDashboard->get_labels_harian($bulan, $tahun);
        echo json_encode(['labels' => $labels]);
    }
}
