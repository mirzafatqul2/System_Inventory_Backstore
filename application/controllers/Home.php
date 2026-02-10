<?php


defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

    public function index()
    {
        $menu = 'dashboard';
        $submenu = '';

        //mendapatkan menuItems yang aktif
        $menuItems = get_menu_items($menu, $submenu);
        $data = array(
            'title' => 'DASHBOARD',
            'subtitle' => 'HALAMAN UTAMA',
            'isi' => 'home',
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems
        );

        $this->load->view('layout/wrapper.php', $data, false);
    }
}

/* End of file Home.php */
