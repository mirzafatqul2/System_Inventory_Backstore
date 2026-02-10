<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stockceklist extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModelStockceklist');
    }

    public function index()
    {
        $menu = 'data_omnimbus';
        $submenu = 'data_ceklist';
        $menuItems = get_menu_items($menu, $submenu);
        $data = [
            'title' => 'Stock Checklist',
            'subtitle' => 'Summary Stock Checklist',
            'isi' => 'Stockceklist/view', // views/Stockceklist/view.php
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems,
        ];

        $this->load->view('layout/wrapper', $data, false);
    }

    public function ajax_summary()
    {
        $list = $this->ModelStockceklist->get_summary_datatables();
        $data = [];
        $no = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        foreach ($list as $row) {
            $no++;
            $dataRow = [];
            $dataRow[] = $no;
            $dataRow[] = htmlspecialchars($row->assignment);
            $dataRow[] = date('d-m-Y', strtotime($row->created_date));
            $dataRow[] = '<button class="btn btn-sm btn-primary btn-view-detail"
                            data-assignment="'.htmlspecialchars($row->assignment).'"
                            data-tanggal="'.$row->created_date.'">View</button>';
            $data[] = $dataRow;
        }

        echo json_encode([
            "draw" => (int) ($_POST['draw'] ?? 1),
            "recordsTotal" => $this->ModelStockceklist->count_summary_all(),
            "recordsFiltered" => $this->ModelStockceklist->count_summary_filtered(),
            "data" => $data,
        ]);
    }

    public function detail()
    {
        $assignment = $this->input->get('assignment');
        $tanggal    = $this->input->get('tanggal');
        if (!$assignment || !$tanggal) show_404();

        $menu = 'data_omnimbus';
        $submenu = 'data_ceklist';
        $menuItems = get_menu_items($menu, $submenu);
        $data = [
            'title' => 'Detail Stock Checklist',
            'subtitle' => 'Detail Stock Checklist',
            'isi' => 'Stockceklist/detail', // views/Stockceklist/detail.php
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems,
            'assignment' => $assignment,
            'tanggal' => $tanggal,
        ];

        $this->load->view('layout/wrapper', $data, false);
    }

    public function ajax_detail()
    {
        $assignment = $this->input->post('assignment');
        $tanggal    = $this->input->post('tanggal');
        if (!$assignment || !$tanggal) {
            echo json_encode([
                "draw" => (int) ($_POST['draw'] ?? 1),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ]);
            return;
        }

        $list = $this->ModelStockceklist->get_detail_datatables($assignment, $tanggal);
        $data = [];
        $no = (int) ($_POST['start'] ?? 0);

        foreach ($list as $row) {
            $no++;
            $dataRow = [];
            $dataRow[] = $no;
            $dataRow[] = htmlspecialchars($row->sku);
            $dataRow[] = number_format($row->variance);
            $dataRow[] = number_format($row->amount, 0, ',', '.');
            $dataRow[] = date('d-m-Y H:i', strtotime($row->created_at));
            $data[] = $dataRow;
        }

        echo json_encode([
            "draw" => (int) ($_POST['draw'] ?? 1),
            "recordsTotal" => $this->ModelStockceklist->count_detail_all($assignment, $tanggal),
            "recordsFiltered" => $this->ModelStockceklist->count_detail_filtered($assignment, $tanggal),
            "data" => $data,
        ]);
    }
    public function importExcel()
{
    $assignment = $this->input->post('assignment');
    if (empty($_FILES['file_excel']['tmp_name'])) {
        $this->session->set_flashdata('error', 'File Excel tidak ditemukan.');
        redirect(site_url('dataomnimbus/data_ceklist'));
    }

    $file = $_FILES['file_excel']['tmp_name'];

    try {
        $objExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    } catch (Exception $e) {
        $this->session->set_flashdata('error', 'File Excel tidak valid: ' . $e->getMessage());
        redirect(site_url('dataomnimbus/data_ceklist'));
    }

    $sheet = $objExcel->getActiveSheet()->toArray(null, true, true, true);

    if (count($sheet) <= 1) {
        $this->session->set_flashdata('error', 'File kosong atau tidak valid.');
        redirect(site_url('dataomnimbus/data_ceklist'));
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
            'assignment' => $assignment,
            'variance' => (int) $row['H'],
            'amount' => (float) $row['G'],
            // created_at akan otomatis pakai default curdate()
        ];
    }

    if (!empty($data)) {
        $this->ModelStockceklist->insert($data); // pakai insert_batch biar efisien
        $this->session->set_flashdata('success', 'Data berhasil diimport.');
    } else {
        $this->session->set_flashdata('error', 'Tidak ada data valid yang diimport.');
    }

    redirect(site_url('dataomnimbus/data_ceklist'));
}
}

/* End of file Stockceklist.php */
