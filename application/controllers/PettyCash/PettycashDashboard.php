<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PettycashDashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('ModelDashboardPettycash');
    }

    public function index() {
        $menu = 'data_pettycash';
        $submenu = 'dashboard_pettycash';
        $menuItems = get_menu_items($menu, $submenu);

        // Ambil filter periode dari GET, jika tidak ada default ke tahun ini
        $start_date = $this->input->get('start_date') ?: date('Y-01-01');
        $end_date   = $this->input->get('end_date') ?: date('Y-m-d');

        // Data chart kiri: total penggunaan Jan-Des tahun ini
        $bulan_data_db = $this->ModelDashboardPettycash->get_total_per_bulan_tahun_ini();
        $bulan_tetap = [];
        for ($i=1; $i<=12; $i++) {
            $bulan_tetap[date('F', mktime(0,0,0,$i,1))] = 0;
        }
        foreach ($bulan_data_db as $row) {
            $bulan_tetap[$row->bulan] = (float)$row->total_amount;
        }

        // Data chart kanan: pie chart COA sesuai filter
        $per_coa = $this->ModelDashboardPettycash->get_total_per_coa($start_date, $end_date);

        $data = [
            'title' => 'Dashboard Petty Cash',
            'subtitle' => 'Data Penggunaan Petty Cash',
            'isi' => 'Pettycash/Dashboard/view',
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'bulan_labels' => array_keys($bulan_tetap),
            'bulan_data' => array_values($bulan_tetap),
            'per_coa' => $per_coa,
        ];

        $this->load->view('layout/wrapper', $data, FALSE);
    }

    // AJAX server-side DataTables untuk tabel detail sesuai filter
    public function ajax_list() {
        $start_date = $this->input->post('start_date') ?: date('Y-01-01');
        $end_date   = $this->input->post('end_date') ?: date('Y-m-d');

        $list = $this->ModelDashboardPettycash->get_datatables($start_date, $end_date);
        $data = [];
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $no = $start;

        foreach ($list as $row) {
            $no++;
            $data[] = [
                $no,
                $row->date,
                $row->coa,
                $row->desc_use,
                number_format($row->amount),
                $row->status_claim == 1 ? 'Pending' : 'Claimed',
                $row->created_at,
            ];
        }

        echo json_encode([
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            "recordsTotal" => $this->ModelDashboardPettycash->count_all($start_date, $end_date),
            "recordsFiltered" => $this->ModelDashboardPettycash->count_filtered($start_date, $end_date),
            "data" => $data,
        ]);
    }
}
