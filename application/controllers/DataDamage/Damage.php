<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Damage extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModelDamage');
    }

    public function index()
    {
        $menu = 'data_omnimbus';
        $submenu = 'data_damage';
        $menuItems = get_menu_items($menu, $submenu);
        $data = [
            'title' => 'Damage',
            'subtitle' => 'Data Damage',
            'isi' => 'Damage/view',
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems,
        ];

        $this->load->view('layout/wrapper', $data, false);
    }

    public function ajax_summary()
{
    $list = $this->ModelDamage->get_summary_datatables();
    $data = [];

    // Mapping singkatan -> deskripsi
    $damage_map = [
        'DMO' => 'Damage Due to Handling',
        'DMC' => 'Damage Customer Return',
        'DMQ' => 'Damage Due to Quality',
        'DDR' => 'Damage During Receiving',
        'DMP' => 'Damage Due to Expire',
        'DPI' => 'Damage Due to Promotion Item',
    ];

    $no = isset($_POST['start']) ? (int)$_POST['start'] : 0;
    foreach ($list as $row) {
        $no++;
        $dataRow   = [];
        $dataRow[] = $no;
        // ubah singkatan jadi deskripsi
        $dataRow[] = ($damage_map[$row->kategory_damage] ?? $row->kategory_damage) . 
             ' (' . $row->kategory_damage . ')';
        $dataRow[] = date('d-m-Y', strtotime($row->created_date));
        $dataRow[] = '<button class="btn btn-sm btn-primary btn-view-detail" 
                        data-kategory="'.$row->kategory_damage.'" 
                        data-tanggal="'.$row->created_date.'">View</button>';

        $data[] = $dataRow;
    }

    $output = [
        "draw"            => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
        "recordsTotal"    => $this->ModelDamage->count_summary_all(),
        "recordsFiltered" => $this->ModelDamage->count_summary_filtered(),
        "data"            => $data,
    ];

    echo json_encode($output);
}


    public function detail()
{
    $kategory = $this->input->get('kategory');
    $tanggal  = $this->input->get('tanggal');

    if (!$kategory || !$tanggal) show_404();

    $menu = 'data_omnimbus';
    $submenu = 'data_damage';
    $menuItems = get_menu_items($menu, $submenu);
    $data = [
        'title' => 'Detail Damage',
        'subtitle' => 'Data Detail Damage',
        'isi' => 'Damage/detail',
        'menu' => $menu,
        'submenu' => $submenu,
        'menuItems' => $menuItems,
        'kategory' => $kategory,       // ⬅️ penting!
        'tanggal' => $tanggal          // ⬅️ penting!
    ];

    $this->load->view('layout/wrapper', $data, false);
}


    public function ajax_detail()
{
    $kategory = $this->input->post('kategory_damage');
    $tanggal  = $this->input->post('tanggal');

    if (!$kategory || !$tanggal) {
        echo json_encode([
            "draw" => (int) ($this->input->post('draw') ?? 1),
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => [],
        ]);
        return;
    }

    $list = $this->ModelDamage->get_detail_datatables($kategory, $tanggal);
    $data = [];
    $no = (int) ($this->input->post('start') ?? 0);

    foreach ($list as $damage) {
        $no++;
        $row = [];
        $row[] = $no;
        $row[] = $damage->sku;
        $row[] = $damage->qty_damage;
        $row[] = number_format($damage->amount_damage, 0, ',', '.');
        $row[] = date('d-m-Y H:i', strtotime($damage->created_at));
        $data[] = $row;
    }

    echo json_encode([
        "draw" => (int) ($this->input->post('draw') ?? 1),
        "recordsTotal" => $this->ModelDamage->count_detail_all($kategory, $tanggal),
        "recordsFiltered" => $this->ModelDamage->count_detail_filtered($kategory, $tanggal),
        "data" => $data,
    ]);
}

public function importExcel()
{
    $category = $this->input->post('kategory');
    if (empty($_FILES['file_excel']['tmp_name'])) {
        $this->session->set_flashdata('error', 'File Excel tidak ditemukan.');
        redirect(site_url('dataomnimbus/data_damage'));
    }

    $file = $_FILES['file_excel']['tmp_name'];

    try {
        $objExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    } catch (Exception $e) {
        $this->session->set_flashdata('error', 'File Excel tidak valid: ' . $e->getMessage());
        redirect(site_url('dataomnimbus/data_damage'));
    }

    $sheet = $objExcel->getActiveSheet()->toArray(null, true, true, true);

    if (count($sheet) <= 1) {
        $this->session->set_flashdata('error', 'File kosong atau tidak valid.');
        redirect(site_url('dataomnimbus/data_damage'));
    }

    $data = [];
    foreach ($sheet as $index => $row) {
        if ($index == 1) continue; // Skip header

        // Lewati jika semua kolom kosong
        if (empty($row['A'])&& empty($row['G']) && empty($row['H'])) {
            continue;
        }

        // Validasi manual qty dan amount (wajib angka)
        if (!is_numeric($row['H']) || !is_numeric($row['G'])) {
            continue; // Lewati baris jika tidak valid
        }

        $data[] = [
            'sku' => trim($row['A']),
            'kategory_damage' => $category,
            'qty_damage' => (int) $row['H'],
            'amount_damage' => (float) $row['G'],
            // created_at akan otomatis pakai default curdate()
        ];
    }

    if (!empty($data)) {
        $this->ModelDamage->insert($data); // pakai insert_batch biar efisien
        $this->session->set_flashdata('success', 'Data berhasil diimport.');
    } else {
        $this->session->set_flashdata('error', 'Tidak ada data valid yang diimport.');
    }

    redirect(site_url('dataomnimbus/data_damage'));
}


}
