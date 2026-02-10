<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelDamage extends CI_Model {

    var $table = 'data_damage';

    // ===== Summary =====
    private function _get_summary_query()
    {
        $this->db->select('kategory_damage, DATE(created_at) as created_date, COUNT(*) as total_items, SUM(amount_damage) as total_amount');
        $this->db->from($this->table);
        $this->db->group_by(['kategory_damage', 'DATE(created_at)']);

        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        if (!empty($search_value)) {
            $this->db->group_start();
            $this->db->like('kategory_damage', $search_value);
            $this->db->or_like('DATE(created_at)', $search_value);
            $this->db->group_end();
        }
        $this->db->order_by('created_date', 'DESC'); 
    }

    public function get_summary_datatables()
    {
        $this->_get_summary_query();
        $length = isset($_POST['length']) ? (int)$_POST['length'] : -1;
        $start  = isset($_POST['start']) ? (int)$_POST['start'] : 0;
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
        $this->db->from("(SELECT 1 FROM $this->table GROUP BY kategory_damage, DATE(created_at)) as summary");
        return $this->db->count_all_results();
    }

    // ===== Detail =====
    private function _get_detail_query($kategory, $tanggal)
    {
        $this->db->from($this->table);
        $this->db->where('kategory_damage', $kategory);
        $this->db->where('DATE(created_at)', $tanggal);

        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        if (!empty($search_value)) {
            $this->db->group_start();
            $this->db->like('sku', $search_value);
            $this->db->or_like('amount_damage', $search_value);
            $this->db->group_end();
        }
    }

    public function get_detail_datatables($kategory, $tanggal)
    {
        $this->_get_detail_query($kategory, $tanggal);
        $length = isset($_POST['length']) ? (int)$_POST['length'] : -1;
        $start  = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        if ($length != -1) $this->db->limit($length, $start);
        return $this->db->get()->result();
    }

    public function count_detail_filtered($kategory, $tanggal)
    {
        $this->_get_detail_query($kategory, $tanggal);
        return $this->db->count_all_results();
    }

    public function count_detail_all($kategory, $tanggal)
    {
        $this->db->from($this->table);
        $this->db->where('kategory_damage', $kategory);
        $this->db->where('DATE(created_at)', $tanggal);
        return $this->db->count_all_results();
    }
    public function insert($data)
    {
        $this->db->insert_batch($this->table, $data);
    }
}
