<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ListBarang extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModelListBarang');
    }

    public function index()
    {
        $menu = 'data_barang';
        $submenu = 'list_barang';
        $menuItems = get_menu_items($menu, $submenu);
        $data = [
            'title' => 'List Barang',
            'subtitle' => 'Data List Barang',
            'isi' => 'Barang/ListBarang/view',
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems,
        ];

        $this->load->view('layout/wrapper', $data, false);
    }

    public function ajax_list()
    {
        $list = $this->ModelListBarang->get_datatables();
        $data = [];
        $no = isset($_POST['start']) ? (int)$_POST['start'] : 0;

        foreach ($list as $row) {
            $no++;

            $remark_text = $row->remark;
            if ($row->remark === 'Brownbox Belum Update') {
                $remark_text = '<span class="badge badge-danger">' . $row->remark . ' </span>';
            }

            $data[] = [
                'no' => $no,
                'sku' => $row->sku ?? '-',
                'description' => $row->description ?? '-',
                'numb_rack' => $row->numb_rack ?? '-',
                'brownbox' => $row->brownbox ?? '-',
                'price' => isset($row->price) ? rupiah($row->price) : 'Rp0',
                'qty' => $row->qty ?? '0',
                'long_sku' => $row->long_sku ?? '-',
                'remark' => $remark_text
            ];
        }

        echo json_encode([
            "draw" => (int)($_POST['draw'] ?? 1),
            "recordsTotal" => $this->ModelListBarang->count_all(),
            "recordsFiltered" => $this->ModelListBarang->count_filtered(),
            "data" => $data
        ]);
    }

    public function importExcel()
    {
        if (empty($_FILES['file_excel']['tmp_name'])) {
            $this->session->set_flashdata('error', 'File Excel tidak ditemukan.');
            redirect(site_url('databarang/list_barang'));
        }

        $file = $_FILES['file_excel']['tmp_name'];

        try {
            $objExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'File Excel tidak valid: ' . $e->getMessage());
            redirect(site_url('databarang/list_barang'));
        }

        $sheet = $objExcel->getActiveSheet()->toArray(null, true, true, true);

        if (count($sheet) <= 1) {
            $this->session->set_flashdata('error', 'File kosong atau tidak valid.');
            redirect(site_url('databarang/list_barang'));
        }

        // Hapus data lama
        $this->db->empty_table('list_barang');

        $data = [];
        foreach ($sheet as $dtbarang => $row) {
            if ($dtbarang == 1) continue; // skip header

            // skip jika semua kolom kosong
            if (empty($row['A']) && empty($row['B']) && empty($row['C']) &&
                empty($row['D']) && empty($row['G']) && empty($row['H']) && empty($row['I'])) {
                continue;
            }

            $remark = '';
            if (!empty($row['G'])) {
                $this->db->where('sku', $row['A']);
                $this->db->where('brownbox', $row['G']);
                $check = $this->db->get('master_keepstock')->num_rows();
                if ($check == 0) {
                    $remark = 'Brownbox Belum Update';
                }
            }

            $data[] = [
                'sku' => $row['A'],
                'numb_rack' => $row['B'],
                'price' => $row['C'],
                'qty' => $row['D'],
                'brownbox' => $row['G'],
                'long_sku' => $row['H'],
                'description' => $row['I'],
                'remark' => $remark
            ];
        }

        if (!empty($data)) {
            $this->ModelListBarang->insert($data);
            $this->session->set_flashdata('success', 'Data berhasil diimport.');
        } else {
            $this->session->set_flashdata('error', 'Tidak ada data valid untuk diimport.');
        }

        redirect(site_url('databarang/list_barang'));
    }

    public function exportExcel()
{
    // Ambil data list_barang yang remark-nya Brownbox Belum Update
    $this->db->select('lb.sku, lb.numb_rack, lb.description, lb.price, lb.brownbox as brownbox_salah, mk.brownbox as brownbox_benar');
    $this->db->from('list_barang lb');
    $this->db->join('master_keepstock mk', 'mk.sku = lb.sku', 'left');
    $this->db->where('lb.remark', 'Brownbox Belum Update');
    $data = $this->db->get()->result();

    // Buat Spreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header kolom
    $sheet->setCellValue('A1', 'SKU');
    $sheet->setCellValue('B1', 'Number Rack');
    $sheet->setCellValue('C1', 'Description');
    $sheet->setCellValue('D1', 'Price');
    $sheet->setCellValue('E1', 'Brownbox Salah');
    $sheet->setCellValue('F1', 'Brownbox Update');
    $sheet->setCellValue('G1', 'Remark');

    // Isi data
    $row = 2;
    foreach ($data as $item) {
        $brownboxSalah = trim((string)$item->brownbox_salah);
        $brownboxBenar = trim((string)$item->brownbox_benar);

        // LOGIKA REMARK
        if (!empty($brownboxSalah) && empty($brownboxBenar)) {
            // Brownbox di list_barang ada, tapi tidak ada di master
            $remark = 'Hapus Brownbox';
        } elseif (empty($brownboxSalah) && !empty($brownboxBenar)) {
            // Brownbox di list_barang tidak ada, tapi di master ada
            $remark = 'Tambahkan Brownbox';
        } elseif (!empty($brownboxSalah) && !empty($brownboxBenar) && $brownboxSalah !== $brownboxBenar) {
            // Brownbox berbeda
            $remark = 'Nomor Brownbox Salah!';
        } else {
            // Sama atau tidak perlu tindakan
            $remark = '';
        }

        // Isi ke Excel
        $sheet->setCellValue('A' . $row, $item->sku);
        $sheet->setCellValue('B' . $row, $item->numb_rack);
        $sheet->setCellValue('C' . $row, $item->description);
        $sheet->setCellValue('D' . $row, $item->price);
        $sheet->setCellValue('E' . $row, $brownboxSalah);
        $sheet->setCellValue('F' . $row, $brownboxBenar);
        $sheet->setCellValue('G' . $row, $remark);

        $row++;
    }

    // Nama file
    $filename = 'Brownbox_Belum_Update_' . date('Ymd_His') . '.xlsx';

    // Header download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

public function checkBrownboxData()
{
    // Cek apakah ada data remark Brownbox Belum Update
    $this->db->where('remark', 'Brownbox Belum Update');
    $count = $this->db->count_all_results('list_barang');

    if ($count > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Data siap di-export']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada data Brownbox yang perlu diupdate']);
    }
}

}

/* End of file ListBarang.php */
