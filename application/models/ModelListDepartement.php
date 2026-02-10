<?php


defined('BASEPATH') or exit('No direct script access allowed');

class ModelListDepartement extends CI_Model
{

    public function get_all()
    {
        return $this->db->order_by('id_departement', 'DESC')->get('list_departement')->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('list_departement', ['id_departement' => $id])->row();
    }

    public function insert($data)
    {
        return $this->db->insert('list_departement', $data);
    }

    public function update($id, $data)
    {
        return $this->db->where('id_departement', $id)->update('list_departement', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('list_departement', ['id_departement' => $id]);
    }
}

/* End of file ModelListDepartement.php */
