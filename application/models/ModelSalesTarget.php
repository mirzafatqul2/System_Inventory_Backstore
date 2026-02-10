<?php

class ModelSalesTarget extends CI_Model
{


    public function get_all()
    {
        return $this->db->order_by('bulan', 'DESC')->get('sales_targets')->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('sales_targets', ['id' => $id])->row();
    }

    public function get_by_bulan($bulan)
    {
        return $this->db->get_where('sales_targets', ['bulan' => $bulan])->row();
    }

    public function insert($data)
    {
        return $this->db->insert('sales_targets', $data);
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update('sales_targets', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('sales_targets', ['id' => $id]);
    }
}
