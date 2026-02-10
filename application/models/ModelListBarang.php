<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelListBarang extends CI_Model {

    var $table = 'list_barang';
    var $column_order = ['list_barang.id', 'list_barang.sku', 'list_barang.description', 'list_barang.brownbox', 'list_barang.remark'];
    var $column_search = ['list_barang.sku', 'list_barang.description', 'list_barang.brownbox', 'list_barang.remark'];
    var $order = ['list_barang.sku' => 'asc'];

    private function _get_datatables_query()
    {
        $this->db->from($this->table);

        $search_value = $_POST['search']['value'] ?? '';
        if (!empty($search_value)) {
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

        if (isset($_POST['order'])) {
            $order_col_idx = $_POST['order'][0]['column'];
            $order_dir = $_POST['order'][0]['dir'];
            if (isset($this->column_order[$order_col_idx])) {
                $this->db->order_by($this->column_order[$order_col_idx], $order_dir);
            }
        } else if (!empty($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        $length = (int) ($_POST['length'] ?? -1);
        $start  = (int) ($_POST['start'] ?? 0);

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
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function insert($data)
    {
        $this->db->insert_batch($this->table, $data);
    }
}

/* End of file ModelListBarang.php */
