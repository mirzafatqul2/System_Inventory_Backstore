<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelPettycash extends CI_Model {

    private $table = 'claim_pettycash';
    private $column_order = [
        'claim_pettycash.id',
        'claim_pettycash.date',
        'master_coa.desc_coa',
        'claim_pettycash.desc_use',
        'claim_pettycash.amount',
        'claim_pettycash.status_claim',
        'claim_pettycash.created_at'
    ];
    private $column_search = [
        'claim_pettycash.date',
        'master_coa.desc_coa',
        'claim_pettycash.desc_use',
        'claim_pettycash.amount',
        'claim_pettycash.status_claim',
        'claim_pettycash.created_at'
    ];
    private $order = ['claim_pettycash.date' => 'desc'];

    private function _get_datatables_query()
    {
        $this->db->select('claim_pettycash.*, master_coa.desc_coa');
        $this->db->from($this->table);
        $this->db->join('master_coa', 'claim_pettycash.coa = master_coa.coa', 'left');

        if (!empty($_POST['search']['value'])) {
            $this->db->group_start();
            foreach ($this->column_search as $index => $item) {
                if ($index === 0) {
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
            }
            $this->db->group_end();
        }

        if (!empty($_POST['order'])) {
            $colIndex = (int) $_POST['order'][0]['column'];
            $dir = $_POST['order'][0]['dir'];
            if (isset($this->column_order[$colIndex])) {
                $this->db->order_by($this->column_order[$colIndex], $dir);
            }
        } else {
            $this->db->order_by(key($this->order), $this->order[key($this->order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1) {
            $this->db->limit((int)$_POST['length'], (int)$_POST['start']);
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

    public function add($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function getById($id)
    {
        $this->db->select('claim_pettycash.*, master_coa.desc_coa');
        $this->db->from($this->table);
        $this->db->join('master_coa', 'claim_pettycash.coa = master_coa.coa', 'left');
        $this->db->where('claim_pettycash.id', $id);
        return $this->db->get()->row();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function getAll()
    {
        $this->db->select('claim_pettycash.*, master_coa.desc_coa');
        $this->db->from($this->table);
        $this->db->join('master_coa', 'claim_pettycash.coa = master_coa.coa', 'left');
        $this->db->order_by('claim_pettycash.date', 'DESC');
        return $this->db->get()->result();
    }
}
