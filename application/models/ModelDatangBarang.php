<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelDatangBarang extends CI_Model
{
    var $table = 'datang_barang';
    var $column_order = ['tanggal', 'surat_jalan', 'ref_no', 'amount', 'sku_ib','ib_pending', 'ctn'];
    var $column_search = ['surat_jalan', 'ref_no'];
    var $order = ['tanggal' => 'asc'];

    private function _get_datatables_query()
    {
        $this->db->from($this->table);

        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        if (!empty($search_value)) {
            $this->db->group_start();
            foreach ($this->column_search as $item) {
                $this->db->or_like($item, $search_value);
            }
            $this->db->group_end();
        }

        if (isset($_POST['order'])) {
            $col_idx = (int)$_POST['order'][0]['column'];
            if (isset($this->column_order[$col_idx])) {
                $this->db->order_by($this->column_order[$col_idx], $_POST['order'][0]['dir']);
            }
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        $length = isset($_POST['length']) ? (int)$_POST['length'] : -1;
        $start  = isset($_POST['start']) ? (int)$_POST['start'] : 0;

        if ($length != -1)
            $this->db->limit($length, $start);
        return $this->db->get()->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        return $this->db->count_all_results();
    }

    function count_all()
    {
        return $this->db->count_all($this->table);
    }
}
