<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelStockceklist extends CI_Model {

    var $table = 'stock_checklist';
    var $column_order = ['id', 'assignment', 'sku', 'variance', 'amount', 'created_at'];
    var $column_search = ['assignment', 'sku', 'created_at'];
    var $order = ['created_at' => 'desc'];

    // === Datatables General ===
    private function _get_datatables_query()
    {
        $this->db->from($this->table);

        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
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
        } else {
            $this->db->order_by(key($this->order), $this->order[key($this->order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        $length = (int) ($_POST['length'] ?? -1);
        $start  = (int) ($_POST['start'] ?? 0);

        if ($length != -1)
            $this->db->limit($length, $start);

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

    // === Summary ===
    private function _get_summary_query()
    {
        $this->db->select('assignment, DATE(created_at) as created_date, COUNT(*) as total_items, SUM(amount) as total_amount');
        $this->db->from($this->table);
        $this->db->group_by(['assignment', 'DATE(created_at)']);

        $search_value = $_POST['search']['value'] ?? '';
        if (!empty($search_value)) {
            $this->db->group_start();
            $this->db->like('assignment', $search_value);
            $this->db->or_like('DATE(created_at)', $search_value);
            $this->db->group_end();
        }
        $this->db->order_by('created_date', 'DESC'); 

    }

    public function get_summary_datatables()
    {
        $this->_get_summary_query();
        $length = (int) ($_POST['length'] ?? -1);
        $start  = (int) ($_POST['start'] ?? 0);
        if ($length != -1) $this->db->limit($length, $start);
        return $this->db->get()->result();
    }

    public function count_summary_filtered()
    {
        $this->_get_summary_query();
        return $this->db->count_all_results();
    }

    public function count_summary_all()
    {
        $this->db->from("(SELECT 1 FROM $this->table GROUP BY assignment, DATE(created_at)) as summary");
        return $this->db->count_all_results();
    }

    // === Detail ===
    private function _get_detail_query($assignment, $tanggal)
    {
        $this->db->from($this->table);
        $this->db->where('assignment', $assignment);
        $this->db->where('DATE(created_at)', $tanggal);

        $search_value = $_POST['search']['value'] ?? '';
        if (!empty($search_value)) {
            $this->db->group_start();
            $this->db->like('sku', $search_value);
            $this->db->or_like('amount', $search_value);
            $this->db->group_end();
        }
    }

    public function get_detail_datatables($assignment, $tanggal)
    {
        $this->_get_detail_query($assignment, $tanggal);
        $length = (int) ($_POST['length'] ?? -1);
        $start  = (int) ($_POST['start'] ?? 0);
        if ($length != -1) $this->db->limit($length, $start);
        return $this->db->get()->result();
    }

    public function count_detail_filtered($assignment, $tanggal)
    {
        $this->_get_detail_query($assignment, $tanggal);
        return $this->db->count_all_results();
    }

    public function count_detail_all($assignment, $tanggal)
    {
        $this->db->from($this->table);
        $this->db->where('assignment', $assignment);
        $this->db->where('DATE(created_at)', $tanggal);
        return $this->db->count_all_results();
    }
    public function insert($data)
    {
        $this->db->insert_batch($this->table, $data);
    }
}
