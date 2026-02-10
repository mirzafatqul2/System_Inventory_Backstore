<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DatangBarang extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModelDatangBarang');
    }

    public function index()
    {
        $menu = 'data_omnimbus';
        $submenu = 'datang_barang';
        $menuItems = get_menu_items($menu, $submenu);
        $data = [
            'title' => 'Data Datang Barang',
            'subtitle' => 'Daftar Barang Datang (Inbound)',
            'isi' => 'DataOmnimbus/DatangBarang/view',
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems,
        ];
        $this->load->view('layout/wrapper', $data, false);
    }

    public function ajax_list()
    {
        header('Content-Type: application/json');
        $list = $this->ModelDatangBarang->get_datatables();
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $row) {
            $no++;
            $editBtn = '<button class="btn btn-sm btn-info btnEdit"
                data-id="'.$row->id.'"
                data-tanggal="'.$row->tanggal.'"
                data-surat_jalan="'.htmlspecialchars($row->surat_jalan).'"
                data-ref_no="'.htmlspecialchars($row->ref_no).'"
                data-amount="'.$row->amount.'"
                data-sku_ib="'.$row->sku_ib.'"
                data-ib_pending="'.$row->ib_pending.'"
                data-ctn="'.$row->ctn.'">
                <i class="fa fa-edit"></i></button>';
            $r = [
                $no,
                date('d-m-Y', strtotime($row->tanggal)),
                $row->surat_jalan,
                $row->ref_no,
                number_format($row->amount,0,',','.'),
                $row->sku_ib,
                $row->ib_pending,
                $row->ctn,
                date('d-m-Y H:i', strtotime($row->tanggal)),
                $editBtn
            ];
            $data[] = $r;
        }
        echo json_encode([
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->ModelDatangBarang->count_all(),
            "recordsFiltered" => $this->ModelDatangBarang->count_filtered(),
            "data" => $data,
        ]);
    }

    public function saveData()
{
    $post = $this->input->post();

    // Validasi input lebih aman (hindari gagal karena angka 0)
    foreach (['tanggal', 'surat_jalan', 'ref_no', 'amount', 'sku_ib', 'ib_pending', 'ctn'] as $field) {
        if (!isset($post[$field]) || trim($post[$field]) === '') {
            $this->session->set_flashdata('error', 'Semua field wajib diisi.');
            redirect('dataomnimbus/datang_barang');
        }
    }

    // Siapkan data
    $data = [
        'tanggal' => $post['tanggal'],
        'surat_jalan' => $post['surat_jalan'],
        'ref_no' => $post['ref_no'],
        'amount' => $post['amount'],
        'sku_ib' => $post['sku_ib'],
        'ib_pending' => $post['ib_pending'],
        'ctn' => $post['ctn'],
    ];

    // Insert atau Update
    if (empty($post['id'])) {
        $this->db->insert('datang_barang', $data);
        $msg = $this->db->affected_rows() > 0 ? 'Data berhasil ditambahkan.' : 'Gagal menambahkan data.';
    } else {
        $this->db->where('id', $post['id'])->update('datang_barang', $data);
        $msg = $this->db->affected_rows() > 0 ? 'Data berhasil diupdate.' : 'Gagal mengupdate data.';
    }

    // Set flashdata berdasarkan pesan
    $type = (strpos($msg, 'berhasil') !== false) ? 'success' : 'error';
    $this->session->set_flashdata($type, $msg);

    redirect('dataomnimbus/datang_barang');
}


}
