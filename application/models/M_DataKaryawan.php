<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_DataKaryawan extends CI_Model
{
    var $table = 'data_karyawan';
    var $column_order = ['id_karyawan', 'nama_karyawan', 'username', 'jabatan', 'telepon', 'status', 'foto']; 
    var $column_search = ['nama_karyawan', 'username', 'jabatan', 'telepon'];
    var $order = ['id_karyawan' => 'asc'];

    private function _get_datatables_query()
    {
        $this->db->from($this->table);
        $search_value = $_POST['search']['value'] ?? '';
        $i = 0;

        foreach ($this->column_search as $item) {
            if ($search_value) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $search_value);
                } else {
                    $this->db->or_like($item, $search_value);
                }

                if (count($this->column_search) - 1 == $i) {
                    $this->db->group_end();
                }
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by(key($this->order), $this->order[key($this->order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();

        $length = $_POST['length'] ?? -1;
        $start  = $_POST['start'] ?? 0;

        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        return $this->db->get()->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        return $this->db->count_all_results();
    }

    public function count_all()
    {
        return $this->db->count_all($this->table);
    }

    // Cek apakah NIK (username) sudah ada
    public function cek_nik($nik)
    {
        return $this->db->get_where('data_karyawan', ['username' => $nik])->row();
    }

    // Ambil data karyawan berdasarkan ID
    public function get_by_id($id)
    {
        return $this->db->get_where('data_karyawan', ['id_karyawan' => $id])->row();
    }

    // Tambah data baru (return true/false)
    public function AddData($data)
    {
        return $this->db->insert('data_karyawan', $data);
    }

    // Update data berdasarkan ID (return true/false)
    public function UpdateData($id, $data)
    {
        $this->db->where('id_karyawan', $id);
        return $this->db->update('data_karyawan', $data);
    }

    // Hapus data berdasarkan ID (return true/false)
    public function DeleteData($id)
    {
        $this->db->where('id_karyawan', $id);
        return $this->db->delete('data_karyawan');
    }

    // Toggle status aktif/nonaktif (return true/false)
    public function updateStatus($id, $status)
    {
        $this->db->where('id_karyawan', $id);
        return $this->db->update('data_karyawan', ['status' => $status]);
    }
}
