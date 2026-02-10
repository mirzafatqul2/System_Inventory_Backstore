<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelMasterKeepstock extends CI_Model
{
    // Tabel utama
    var $table = 'master_keepstock'; 

    // Kolom untuk datatables per item
    var $column_order = ['mk.brownbox', 'mk.sku', 'ld.departement', 'lb.description', 'lb.numb_rack', 'mk.qty', 'lb.price'];
    var $column_search = ['mk.brownbox', 'mk.sku', 'ld.departement', 'lb.description'];
    var $order = ['mk.brownbox' => 'asc'];

    // ------------------------------------
    // ✅ Query Datatables per item
    private function _get_datatables_query()
    {
        $this->db->select('
            mk.id_keepstock,
            mk.brownbox,
            mk.sku,
            mk.qty,
            ld.departement,
            lb.description,
            lb.numb_rack,
            lb.price
        ');
        $this->db->from($this->table . ' mk');
        $this->db->join('list_departement ld', 'ld.id_departement = mk.departement', 'left');
        $this->db->join('list_barang lb', 'lb.sku = mk.sku', 'left');

        // pencarian
        if (!empty($_POST['search']['value'])) {
            $search_value = $_POST['search']['value'];
            $this->db->group_start();
            foreach ($this->column_search as $i => $item) {
                if ($i === 0) {
                    $this->db->like($item, $search_value);
                } else {
                    $this->db->or_like($item, $search_value);
                }
            }
            $this->db->group_end();
        }

        // urutan
        if (isset($_POST['order'])) {
            $order_col_idx = (int)$_POST['order'][0]['column'];
            $order_dir = $_POST['order'][0]['dir'];
            if (isset($this->column_order[$order_col_idx])) {
                $this->db->order_by($this->column_order[$order_col_idx], $order_dir);
            }
        } else {
            $this->db->order_by(key($this->order), $this->order[key($this->order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
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
        $this->db->from($this->table . ' mk');
        $this->db->join('list_departement ld', 'ld.id_departement = mk.departement', 'left');
        $this->db->join('list_barang lb', 'lb.sku = mk.sku', 'left');
        return $this->db->count_all_results();
    }

    // ------------------------------------
    // ✅ Datatables grouping per brownbox
    public function get_datatables_group()
    {
        $this->db->select('mk.brownbox, ld.departement');
        $this->db->from($this->table.' mk');
        $this->db->join('list_departement ld','ld.id_departement=mk.departement','left');

        // pencarian
        if (!empty($_POST['search']['value'])) {
            $this->db->group_start();
            $this->db->like('mk.brownbox', $_POST['search']['value']);
            $this->db->or_like('ld.departement', $_POST['search']['value']);
            $this->db->group_end();
        }

        $this->db->group_by('mk.brownbox');

        if (isset($_POST['order'])) {
            $this->db->order_by('mk.brownbox', $_POST['order'][0]['dir']);
        } else {
            $this->db->order_by('mk.brownbox', 'asc');
        }

        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $this->db->limit((int)$_POST['length'], (int)$_POST['start']);
        }

        return $this->db->get()->result();
    }

    public function count_all_group()
    {
        $this->db->from($this->table);
        $this->db->group_by('brownbox');
        return $this->db->count_all_results();
    }

    public function count_filtered_group()
    {
        $this->db->select('mk.brownbox');
        $this->db->from($this->table.' mk');
        $this->db->join('list_departement ld','ld.id_departement=mk.departement','left');

        if (!empty($_POST['search']['value'])) {
            $this->db->group_start();
            $this->db->like('mk.brownbox', $_POST['search']['value']);
            $this->db->or_like('ld.departement', $_POST['search']['value']);
            $this->db->group_end();
        }

        $this->db->group_by('mk.brownbox');
        return $this->db->get()->num_rows();
    }

    // ------------------------------------
    // ✅ Ambil semua SKU dalam satu brownbox
    public function get_sku_by_brownbox($brownbox)
    {
        $this->db->select('
            mk.sku,
            mk.qty,
            mk.departement as departemen_id,
            ld.departement,
            lb.description,
            lb.numb_rack,
            lb.price
        ');
        $this->db->from($this->table.' mk');
        $this->db->join('list_departement ld','ld.id_departement=mk.departement','left');
        $this->db->join('list_barang lb','lb.sku=mk.sku','left');
        $this->db->where('mk.brownbox',$brownbox);
        return $this->db->get()->result();
    }

    // ------------------------------------
    // ✅ Validasi brownbox
    public function validasi_brownbox($brownbox)
    {
        $this->db->where('brownbox', $brownbox);
        return $this->db->count_all_results($this->table) > 0;
    }

    // ✅ Validasi SKU (pastikan SKU belum ada di brownbox lain)
    public function validasi_sku($sku, $brownbox)
    {
        $this->db->where('sku', $sku);
        $this->db->where('brownbox !=', $brownbox);
        return $this->db->get($this->table)->row();
    }

    // ------------------------------------
    // ✅ Insert batch
    public function insert($data)
    {
        return $this->db->insert_batch($this->table, $data);
    }

    // ✅ Update: cek total qty untuk validasi
    public function get_total_qty_by_brownbox($brownbox)
    {
        $this->db->select_sum('qty');
        $this->db->where('brownbox', $brownbox);
        $result = $this->db->get($this->table)->row();
        return $result ? (int)$result->qty : 0;
    }
}
