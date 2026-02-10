<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ModelDashboardPettycash extends CI_Model {

     public function get_total_per_bulan_tahun_ini() {
        $tahun = date('Y');
        $sql = "SELECT MONTHNAME(date) AS bulan, SUM(amount) AS total_amount
                FROM claim_pettycash
                WHERE YEAR(date) = ?
                GROUP BY MONTH(date)";
        return $this->db->query($sql, [$tahun])->result();
    }

    public function get_total_per_coa($start_date, $end_date) {
        $sql = "SELECT coa, SUM(amount) AS total_amount
                FROM claim_pettycash
                WHERE date BETWEEN ? AND ?
                GROUP BY coa";
        return $this->db->query($sql, [$start_date, $end_date])->result();
    }

    // Untuk DataTables server-side list detail
    private function _get_datatables_query($start_date, $end_date) {
        $this->db->from('claim_pettycash');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);

        $columns = ['id', 'date', 'coa', 'desc_use', 'amount', 'status_claim', 'created_at'];
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        if (!empty($search_value)) {
            $this->db->group_start();
            foreach ($columns as $i => $col) {
                if ($i === 0) {
                    $this->db->like($col, $_POST['search']['value']);
                } else {
                    $this->db->or_like($col, $_POST['search']['value']);
                }
            }
            $this->db->group_end();
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($columns[$_POST['order'][0]['column']], $_POST['order'][0]['dir']);
        } else {
            $this->db->order_by('date', 'desc');
        }
    }

    public function get_datatables($start_date, $end_date) {
        $this->_get_datatables_query($start_date, $end_date);
        $length = isset($_POST['length']) ? (int)$_POST['length'] : -1;
        $start  = isset($_POST['start']) ? (int)$_POST['start'] : 0;

        if ($length != -1)
            $this->db->limit($length, $start);
        return $this->db->get()->result();
    }

    public function count_filtered($start_date, $end_date) {
        $this->_get_datatables_query($start_date, $end_date);
        return $this->db->count_all_results();
    }

    public function count_all($start_date, $end_date) {
        $this->db->from('claim_pettycash');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        return $this->db->count_all_results();
    }

}

/* End of file ModelDashboardPettycash.php */
