<?php


defined('BASEPATH') or exit('No direct script access allowed');

class M_auth extends CI_Model
{
    public function login_user($username)
    {
        $this->db->select('*');
        $this->db->from('data_karyawan');
        $this->db->where(
            'username',
            $username
        );
        return $this->db->get()->row();
    }
}

/* End of file M_auth.php */
