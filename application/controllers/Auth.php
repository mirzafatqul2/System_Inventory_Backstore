<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function login_user()
    {
        $this->form_validation->set_rules('username', 'Username', 'required', array(
            'required' => '%s Tidak Boleh Kosong!'
        ));
        $this->form_validation->set_rules('password', 'Password', 'required', array(
            'required' => '%s Tidak Boleh Kosong!'
        ));

        if ($this->form_validation->run() == TRUE) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            // Ambil data karyawan berdasarkan username
            $employee = $this->db->get_where('data_karyawan', ['username' => $username])->row_array();

            if ($employee) {
                $password_verify = $employee['password'];
                $status = $employee['status'];
                if ($status == 1) {  // Jika status aktif
                    if (password_verify($password, $password_verify)) {  // Verifikasi password
                        $this->user_login->login($username);
                    } else {
                        $this->session->set_flashdata('error', 'Password Anda Salah!');
                    }
                } elseif ($status == 2) {  // Jika akun tidak aktif
                    $this->session->set_flashdata('error', 'Akun Tidak Aktif!');
                } else {  // Jika akun sudah dihapus
                    $this->session->set_flashdata('error', 'Akun Sudah di Hapus!');
                }
            } else {
                $this->session->set_flashdata('error', 'Username Tidak Terdaftar!');
            }
        }

        $data = array(
            'title' => 'Login User'
        );
        $this->load->view('v_login_user', $data, FALSE);
    }

    public function logout_user()
    {
        $this->user_login->logout();
    }
}

/* End of file Auth.php */
