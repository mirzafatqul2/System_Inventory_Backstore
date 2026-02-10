<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RefillKeepstock extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModelRefillKeepstock');
    }

    public function index()
    {
        $menu = 'data_barang';
        $submenu = 'data_refill';
        $menuItems = get_menu_items($menu, $submenu);

        $data = [
            'title' => 'Refill Keepstock',
            'subtitle' => 'Data Refill Stok',
            'isi' => 'Barang/RefillKeepstock/view',
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems,
        ];

        $this->load->view('layout/wrapper', $data, false);
    }

    public function ajax_list()
    {
        $list = $this->ModelRefillKeepstock->get_datatables();
        $data = [];
        $no = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        foreach ($list as $refill) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = date('d-m-Y H:i', strtotime($refill->refill_date));
            $row[] = $refill->brownbox ?: '-';
            $row[] = $refill->sku ?: '-';
            $row[] = number_format($refill->qty_refill, 0, ',', '.');
            $row[] = $refill->refill_by ?: '-';
            $data[] = $row;
        }

        $output = [
            "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
            "recordsTotal" => $this->ModelRefillKeepstock->count_all(),
            "recordsFiltered" => $this->ModelRefillKeepstock->count_filtered(),
            "data" => $data,
        ];

        echo json_encode($output);
    }

    public function add()
{
    $brownbox = strtoupper($this->input->post('brownbox', true));
    $sku = strtoupper($this->input->post('sku', true));
    $qty_refill = $this->input->post('qty_refill');
    $refill_by = $this->session->userdata('username') ?? 'unknown';

    // Validasi manual
    if (empty($brownbox) || empty($sku) || empty($qty_refill)) {
        $this->session->set_flashdata('error', 'Semua field wajib diisi.');
        redirect('databarang/data_refill');
    }

    $data = [
        'refill_date' => date('Y-m-d H:i:s'),
        'brownbox' => $brownbox,
        'sku' => $sku,
        'qty_refill' => (int)$qty_refill,
        'refill_by' => $refill_by,
    ];

    $result = $this->ModelRefillKeepstock->add_refill($data);

    if ($result['status'] === true) {
        $this->session->set_flashdata('success', 'Refill berhasil disimpan.');
    } else {
        $this->session->set_flashdata('error', $result['message'] ?? 'Terjadi kesalahan saat refill.');
    }

    redirect('databarang/data_refill');
}


public function exportExcel()
{
    $refillData = $this->db->order_by('refill_date', 'desc')->get('refill_keepstock')->result();

    // Inisialisasi Spreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header kolom
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Tanggal Refill');
    $sheet->setCellValue('C1', 'Brownbox');
    $sheet->setCellValue('D1', 'SKU');
    $sheet->setCellValue('E1', 'Qty Refill');
    $sheet->setCellValue('F1', 'Refill By');

    // Isi data
    $rowNum = 2;
    $no = 1;
    foreach ($refillData as $row) {
        $sheet->setCellValue('A' . $rowNum, $no++);
        $sheet->setCellValue('B' . $rowNum, date('d-m-Y H:i', strtotime($row->refill_date)));
        $sheet->setCellValue('C' . $rowNum, $row->brownbox);
        $sheet->setCellValue('D' . $rowNum, $row->sku);
        $sheet->setCellValue('E' . $rowNum, $row->qty_refill);
        $sheet->setCellValue('F' . $rowNum, $row->refill_by);
        $rowNum++;
    }

    // Set nama file
    $filename = 'Refill_Keepstock_' . date('Ymd_His') . '.xlsx';

    // Header download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

}
