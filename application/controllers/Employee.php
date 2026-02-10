<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Employee extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_DataKaryawan');
    }

    private function get_jabatan($id)
    {
        switch ($id) {
            case '1': return 'Supervisor';
            case '2': return 'Assistant Supervisor';
            case '3': return 'Store Boy';
            case '4': return 'Kasir';
            case '5': return 'Promotor';
            default:  return '<span class="badge bg-danger">ERROR</span>';
        }
    }

    public function index()
    {
        $menu = 'data_karyawan';
        $submenu = '';
        $menuItems = get_menu_items($menu, $submenu); // Pastikan fungsi ini ada atau kamu ganti sesuai kebutuhan

        $data = [
            'title'     => 'Data Karyawan',
            'subtitle'  => 'Data Karyawan',
            'isi'       => 'Employee/view',
            'menu'      => $menu,
            'submenu'   => $submenu,
            'menuItems' => $menuItems,
        ];
        $this->load->view('layout/wrapper', $data);
    }

    public function ajax_list()
    {
        $list = $this->M_DataKaryawan->get_datatables();
        $data = [];
        $no = isset($_POST['start']) ? (int)$_POST['start'] : 0;

        foreach ($list as $employee) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $employee->nama_karyawan;
            $row[] = $employee->username; // Username = NIK
            $row[] = $this->get_jabatan($employee->jabatan);
            $row[] = $employee->telepon;

            $statusBtn = ($employee->status == 1)
                ? '<button class="btn btn-success btn-sm btn-status" data-id="'.$employee->id_karyawan.'" data-status="1"><i class="fa fa-check"></i> ACTIVE</button>'
                : '<button class="btn btn-danger btn-sm btn-status" data-id="'.$employee->id_karyawan.'" data-status="0"><i class="fa fa-times"></i> UNACTIVE</button>';
            $row[] = $statusBtn;

            $gambar = !empty($employee->foto) ? $employee->foto : 'default.png';
            $foto = '<img src="'.base_url('assets/img/'.$gambar).'" class="img-thumbnail" width="80">';
            $row[] = $foto;

            $aksi  = '<button class="btn btn-warning btn-sm btn-edit" data-id="'.$employee->id_karyawan.'"><i class="fas fa-edit"></i></button> ';
            $aksi .= '<button class="btn btn-danger btn-sm btn-delete" data-id="'.$employee->id_karyawan.'"><i class="fas fa-trash"></i></button>';
            $row[] = $aksi;

            $data[] = $row;
        }

        echo json_encode([
            "draw" => intval($_POST['draw'] ?? 1),
            "recordsTotal" => $this->M_DataKaryawan->count_all(),
            "recordsFiltered" => $this->M_DataKaryawan->count_filtered(),
            "data" => $data,
        ]);
    }

    public function AddData()
{
    $nik            = $this->input->post('nik');
    $nama_karyawan  = $this->input->post('nama_karyawan');
    $password       = $this->input->post('password');
    $jabatan        = $this->input->post('jabatan');
    $telepon        = $this->input->post('telepon');

    if (empty($nik) || empty($nama_karyawan) || empty($password) || empty($jabatan) || empty($telepon)) {
        $this->session->set_flashdata('error', 'Semua field wajib diisi.');
        redirect('employee');
    }

    if ($this->M_DataKaryawan->cek_nik($nik)) {
        $this->session->set_flashdata('error', 'NIK sudah terdaftar!');
        redirect('employee');
    }

    $foto = '';
if (!empty($_FILES['gambar']['name'])) {
    $config['upload_path']   = str_replace('\\', '/', FCPATH) . 'assets/img/';
    $config['allowed_types'] = 'jpg|jpeg|png|PNG|JPG|JPEG';
    $config['max_size']      = 2048;
    $config['file_name']     = time() . '_' . $_FILES['gambar']['name'];

    $this->load->library('upload');
    $this->upload->initialize($config); // inisialisasi ulang config upload

    if (!$this->upload->do_upload('gambar')) {
        $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
        redirect('employee');
    }

    $foto = $this->upload->data('file_name');
}

    $data = [
        'nama_karyawan' => $nama_karyawan,
        'username'      => $nik,
        'password'      => password_hash($password, PASSWORD_DEFAULT),
        'jabatan'       => $jabatan,
        'telepon'       => $telepon,
        'foto'          => $foto,
        'status'        => 1,
    ];

    $insert = $this->M_DataKaryawan->AddData($data);
    if($insert){
        $this->session->set_flashdata('success', 'Berhasil Menambahkan Data Karyawan.');
    }else{
        $this->session->set_flashdata('error', 'Gagal Menambahkan Data Karyawan.');
    }
    redirect('employee');
}

public function UpdateData()
{
    $id            = $this->input->post('id_karyawan');
    $nama_karyawan = $this->input->post('nama_karyawan');
    $jabatan       = $this->input->post('jabatan');
    $telepon       = $this->input->post('telepon');
    $password      = $this->input->post('password');

    if (empty($id) || empty($nama_karyawan) || empty($jabatan) || empty($telepon)) {
        $this->session->set_flashdata('error', 'Semua field wajib diisi.');
        redirect('employee');
    }

    $foto = '';
    if (!empty($_FILES['gambar']['name'])) {
        $config['upload_path']   = FCPATH . 'assets/img/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size']      = 2048;
        $config['file_name']     = time().'_'.$_FILES['gambar']['name'];
         $this->load->library('upload');
    $this->upload->initialize($config); 

        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('gambar')) {
            $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
            redirect('employee');
        }
        $foto = $this->upload->data('file_name');
    }

    $data = [
        'nama_karyawan' => $nama_karyawan,
        'jabatan'       => $jabatan,
        'telepon'       => $telepon,
    ];

    if (!empty($foto)) {
        $data['foto'] = $foto;
    }

    if (!empty($password)) {
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $update = $this->M_DataKaryawan->UpdateData($id, $data);
    $this->session->set_flashdata($update ? 'success' : 'error', $update ? 'Data karyawan berhasil diperbarui.' : 'Gagal memperbarui data.');
    redirect('employee');
}

    public function DeleteData($id)
    {
        $delete = $this->M_DataKaryawan->DeleteData($id);
    if($delete){
        $this->session->set_flashdata('success', 'Berhasil Menghapus Data Karyawan.');
    }else{
        $this->session->set_flashdata('error', 'Gagal Menghapus Data Karyawan.');
    }
    redirect('employee');
    }

   public function updateStatus()
{
    $id = $this->input->post('id');
    $status = $this->input->post('status');

    if ($id === null || $status === null) {
        echo json_encode(['status' => false, 'msg' => 'Data tidak lengkap']);
        return;
    }

    $new_status = ($status == 1) ? 0 : 1;

    $this->M_DataKaryawan->updateStatus($id, $new_status);

    echo json_encode(['status' => true, 'new_status' => $new_status]);
}


    public function GetById($id)
    {
        $data = $this->M_DataKaryawan->get_by_id($id);
        echo json_encode($data);
    }
}
