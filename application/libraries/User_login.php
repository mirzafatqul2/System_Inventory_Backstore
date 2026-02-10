<?php


defined('BASEPATH') or exit('No direct script access allowed');

class User_login
{
    protected $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('m_auth');
    }

    public function login($username)
    {
        // Memanggil fungsi login_user untuk mendapatkan data karyawan
        $test = $this->ci->m_auth->login_user($username);

        if ($test) {
            // Mengambil data dari objek $test
            $id_karyawan = $test->id_karyawan;
            $nama_karyawan = $test->nama_karyawan;
            $username = $test->username;
            $jabatan = $test->jabatan;
            $telepon = $test->telepon;
            $status = $test->status;
            $foto = $test->foto;

            // Menyimpan data ke session
            $this->ci->session->set_userdata('id_karyawan', $id_karyawan);
            $this->ci->session->set_userdata('nama_karyawan', $nama_karyawan);
            $this->ci->session->set_userdata('username', $username);
            $this->ci->session->set_userdata('jabatan', $jabatan);
            $this->ci->session->set_userdata('telepon', $telepon);
            $this->ci->session->set_userdata('status', $status);
            $this->ci->session->set_userdata('foto', $foto);

            // Redirect ke halaman home setelah login berhasil
            redirect('dashboardutama');
        } else {
            // Jika login gagal, set flashdata untuk menampilkan pesan error
            $this->ci->session->set_flashdata('error', 'Username atau Password yang Anda Masukkan Salah!');

            // Redirect ke halaman login
            redirect('auth/login_user');
        }
    }


    public function protect()
    {
        if ($this->ci->session->userdata('username') == '') {
            $this->ci->session->set_flashdata('error', 'Silahkan Login Terlebih Dahulu!');
            redirect('auth/login_user');
        }
    }

    public function logout()
    {
        $this->ci->session->unset_userdata('username');
        $this->ci->session->unset_userdata('nama_karyawan');
        $this->ci->session->unset_userdata('level_karyawan');
        $this->ci->session->set_flashdata('message', 'Anda Berhasil Logout!');
        redirect('auth/login_user');
    }
}

/* End of file User_login.php */