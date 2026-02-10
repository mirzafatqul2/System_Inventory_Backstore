<?php


defined('BASEPATH') or exit('No direct script access allowed');

class ListDepartement extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModelListDepartement');
    }


    public function index()
    {
        $menu = 'data_barang';
        $submenu = 'list_departement';
        $menuItems = get_menu_items($menu, $submenu);

        $data = array(
            'title' => 'List Departement',
            'subtitle' => 'Data List Departement',
            'isi' => 'Barang/ListDepartement/view',
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems,
            'listDepartement' => $this->ModelListDepartement->get_all(),

        );

        $this->load->view('layout/wrapper', $data, false);
    }

    public function addData()
    {
        $departement = $this->input->post('departement');
        $data = [
            'departement' => $departement
        ];
        $cek = $this->db->get_where('list_departement', ['departement' => $departement])->row();
        if ($cek) {
            $this->session->set_flashdata('error', "Data Departement $departement Sudah Ada.");
            redirect('databarang/list_departement');
        } else {
            $this->ModelListDepartement->insert($data);
            $this->session->set_flashdata('success', "Data Departement $departement Berhasil Ditambahkan!");
            redirect('databarang/list_departement');
        }
    }

    public function getDataById()
    {
        $idDepartement = $this->input->get('id');
        $data = $this->ModelListDepartement->get_by_id($idDepartement);
        echo json_encode($data);
    }

    public function updateData()
    {
        $id = $this->input->post('id');
        $newDept = $this->input->post('edit_departement');
        $data = [
            'departement' => $newDept,
        ];

        // Cek apakah nama departemen yang baru sudah ada (dan bukan milik ID yang sama)
        $cek = $this->db->get_where('list_departement', [
            'departement' => $newDept,
            'id_departement !=' => $id  // Agar tidak menganggap dirinya sendiri sebagai duplikat
        ])->row();

        if ($cek) {
            $this->session->set_flashdata('error', "Data Departement $newDept Sudah Ada.");
        } else {
            $this->ModelListDepartement->update($id, $data);
            $this->session->set_flashdata('success', "Data Departement $newDept Berhasil diubah!");
        }

        redirect('databarang/list_departement');
    }


    public function deleteData($id)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'ID tidak ditemukan!');
            redirect('databarang/list_departement');
        }

        $getDataByID = $this->ModelListDepartement->get_by_id($id);

        if (!$getDataByID) {
            $this->session->set_flashdata('error', 'Data Departement tidak ditemukan!');
            redirect('databarang/list_departement');
        }

        $departement = $getDataByID->departement;
        $deleted = $this->ModelListDepartement->delete($id);

        if ($deleted) {
            $this->session->set_flashdata('success', "Data Departement $departement berhasil dihapus!");
        } else {
            $this->session->set_flashdata('error', "Gagal menghapus Data Departement $departement.");
        }

        redirect('databarang/list_departement');
    }
}

/* End of file ListDepartement.php */
