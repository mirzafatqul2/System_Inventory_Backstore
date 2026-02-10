<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MasterKeepstock extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModelMasterKeepstock');
        $this->load->model('ModelListDepartement');
    }

    public function index()
    {
        $menu = 'data_barang';
        $submenu = 'master_keepstock';
        $menuItems = get_menu_items($menu, $submenu);

        $data = array(
            'title'         => 'Master Keepstock',
            'subtitle'      => 'Data Master Keepstock',
            'isi'           => 'Barang/MasterKeepstock/view',
            'menu'          => $menu,
            'submenu'       => $submenu,
            'menuItems'     => $menuItems,
            'listDepartement' => $this->ModelListDepartement->get_all(),
        );

        $this->load->view('layout/wrapper', $data, false);
    }

    // ✅ Datatable list (group per brownbox)
    public function ajax_list()
    {
        $list = $this->ModelMasterKeepstock->get_datatables_group();
        $data = [];
        $no = isset($_POST['start']) ? (int)$_POST['start'] : 0;

        foreach ($list as $item) {
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $item->brownbox;

            // Gabungkan SKU per brownbox
            $skuList = $this->ModelMasterKeepstock->get_sku_by_brownbox($item->brownbox);
            $skuHtml    = "<ul style='list-style-type: none; padding-left: 0; margin: 0'>";
            $descHtml   = "<ul style='list-style-type: none; padding-left: 0; margin: 0'>";
            $rackHtml   = "<ul style='list-style-type: none; padding-left: 0; margin: 0'>";
            $qtyHtml    = "<ul style='list-style-type: none; padding-left: 0; margin: 0'>";
            $amountHtml = "<ul style='list-style-type: none; padding-left: 0; margin: 0'>";
            foreach ($skuList as $sku) {
                $skuHtml   .= "<li>{$sku->sku}</li>";
                $desc = !empty($sku->description) ? $sku->description : 'SKU Belum terdaftar';
                $descHtml  .= "<li>{$desc}</li>";
                $rack = !empty($sku->numb_rack) ? $sku->numb_rack : 'SKU Belum terdaftar';
                $rackHtml  .= "<li>{$rack}</li>";
                $qtyHtml   .= "<li>{$sku->qty}</li>";
                $price = !empty($sku->price)
    ? 'Rp ' . number_format(((int)$sku->qty) * ((int)$sku->price), 0, ',', '.')
    : 'SKU Belum terdaftar';
$amountHtml .= "<li>{$price}</li>";
            }
            $skuHtml  .= "</ul>";
            $descHtml .= "</ul>";
            $rackHtml .= "</ul>";
            $qtyHtml  .= "</ul>";
            $amountHtml .= "</ul>";

            $row[] = $skuHtml;
            $row[] = $item->departement;
            $row[] = $descHtml;
            $row[] = $rackHtml;
            $row[] = $qtyHtml;
            $row[] = $amountHtml; // ✅ tampilkan per SKU (list)

            // ✅ Hanya tombol Edit & Printsheet
            $row[] = '
                <button type="button" class="btn btn-warning btn-edit" data-brownbox="'.$item->brownbox.'">
                    <i class="fas fa-edit"></i>
                </button>
                <a href="'.site_url('databarang/master_keepstock/printsheet/'.$item->brownbox).'" target="_blank" class="btn btn-info">
                    <i class="fas fa-print"></i>
                </a>
            ';
            $data[] = $row;
        }

        $output = [
            "draw" => intval($_POST['draw'] ?? 1),
            "recordsTotal" => $this->ModelMasterKeepstock->count_all_group(),
            "recordsFiltered" => $this->ModelMasterKeepstock->count_filtered_group(),
            "data" => $data,
        ];

        echo json_encode($output);
    }

    // ✅ Tambah data
    public function addData()
    {
        $brownbox = strtoupper($this->input->post('brownbox'));
        $departemen_id = $this->input->post('departemen_id');
        $mode = $this->input->post('mode');
        $sku = $this->input->post('sku');
        $qty = $this->input->post('qty');

        $errors = [];
        $data = [];

        if ($mode === 'ctn') {
            $ctn = (int) $this->input->post('ctn');
            for ($i = 0; $i < $ctn; $i++) {
                $suffix = chr(65 + $i);
                $newBrownbox = $brownbox . $suffix;
                if ($this->ModelMasterKeepstock->validasi_brownbox($newBrownbox)) {
                    $errors[] = "Brownbox <b>$newBrownbox</b> sudah ada di sistem.";
                    break;
                }
                $skuConflict = $this->ModelMasterKeepstock->validasi_sku($sku[$i], $newBrownbox);
                if ($skuConflict) {
                    $errors[] = "SKU <b>{$sku[$i]}</b> sudah terdaftar di brownbox <b>{$skuConflict->brownbox}</b>.";
                    break;
                }
                $data[] = [
                    'brownbox' => $newBrownbox,
                    'sku' => $sku[$i],
                    'qty' => $qty[$i],
                    'departement' => $departemen_id,
                ];
            }
        } else {
            if ($this->ModelMasterKeepstock->validasi_brownbox($brownbox)) {
                $errors[] = "Brownbox <b>$brownbox</b> sudah ada di sistem.";
            }
            for ($i = 0; $i < count($sku); $i++) {
                $skuConflict = $this->ModelMasterKeepstock->validasi_sku($sku[$i], $brownbox);
                if ($skuConflict) {
                    $errors[] = "SKU <b>{$sku[$i]}</b> sudah terdaftar di brownbox <b>{$skuConflict->brownbox}</b>.";
                    continue;
                }
                $data[] = [
                    'brownbox' => $brownbox,
                    'sku' => $sku[$i],
                    'qty' => $qty[$i],
                    'departement' => $departemen_id,
                ];
            }
        }

        if (!empty($errors)) {
            $this->session->set_flashdata('error', implode('<br>', $errors));
            redirect('databarang/master_keepstock');
        }

        if (!empty($data)) {
            $this->ModelMasterKeepstock->insert($data);
            $this->session->set_flashdata('success', 'Data Keepstock berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Tidak ada data yang ditambahkan.');
        }
        redirect('databarang/master_keepstock');
    }

    // ✅ Import Excel
    public function importExcel()
    {
        if (empty($_FILES['file_excel']['tmp_name'])) {
            $this->session->set_flashdata('error', 'File Excel tidak ditemukan.');
            redirect(site_url('databarang/master_keepstock'));
        }

        try {
            $objExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['file_excel']['tmp_name']);
        } catch (Exception $e) {
            $this->session->set_flashdata('error', 'File Excel tidak valid: ' . $e->getMessage());
            redirect(site_url('databarang/master_keepstock'));
        }

        $sheet = $objExcel->getActiveSheet()->toArray(null, true, true, true);

        if (count($sheet) <= 1) {
            $this->session->set_flashdata('error', 'File kosong atau tidak valid.');
            redirect(site_url('databarang/master_keepstock'));
        }

        // Hapus semua data lama
        $this->db->empty_table('master_keepstock');

        $data = [];
        foreach ($sheet as $idx => $row) {
            if ($idx == 1) continue;
            if (empty($row['A']) && empty($row['B']) && empty($row['C']) && empty($row['D'])) continue;

            $brownbox   = isset($row['A']) ? trim($row['A']) : '';
            $sku        = isset($row['B']) ? trim($row['B']) : '';
            $qty        = isset($row['C']) && $row['C'] !== '' ? (int)$row['C'] : 0;
            $departemen = isset($row['D']) ? trim($row['D']) : '';

            $data[] = [
                'brownbox'    => $brownbox,
                'sku'         => $sku !== '' ? $sku : null,
                'qty'         => $qty,
                'departement' => $departemen
            ];
        }

        if (!empty($data)) {
            $this->db->insert_batch('master_keepstock', $data);
            $this->session->set_flashdata('success', 'Data Keepstock berhasil diimport.');
        } else {
            $this->session->set_flashdata('error', 'Tidak ada data valid yang diimport.');
        }

        redirect(site_url('databarang/master_keepstock'));
    }

    // ✅ Update data per brownbox (dengan validasi total qty 0)
public function update()
{
    $brownbox_lama = trim($this->input->post('brownbox_awal')); // dari hidden input
    $brownbox_baru = trim($this->input->post('brownbox'));      // dari text input
    $departemen_id = $this->input->post('departemen_id');
    $skus          = $this->input->post('sku'); // array
    $qtys          = $this->input->post('qty'); // array

    if (!is_array($skus)) $skus = [];
    if (!is_array($qtys)) $qtys = [];

    // 🔎 Validasi SKU: pastikan SKU baru tidak dipakai di brownbox lain
    foreach ($skus as $sku) {
        $sku = trim($sku);
        if ($sku !== '') {
            $cek = $this->db
                ->where('sku', $sku)
                ->where('brownbox !=', $brownbox_lama)
                ->count_all_results('master_keepstock');
            if ($cek > 0) {
                echo json_encode([
                    'status'  => false,
                    'message' => "SKU <b>$sku</b> sudah dipakai di brownbox lain!"
                ]);
                return;
            }
        }
    }

    // ✅ Kalau nama brownbox sama → cukup update data
    if ($brownbox_baru === $brownbox_lama) {
        foreach ($skus as $i => $sku) {
            $sku = trim($sku);
            if ($sku !== '') {
                $this->db->where('brownbox', $brownbox_lama);
                $this->db->where('sku', $sku);
                $this->db->update('master_keepstock', [
                    'qty'         => isset($qtys[$i]) ? (int)$qtys[$i] : 0,
                    'departement' => $departemen_id
                ]);
            }
        }

        echo json_encode([
            'status'  => true,
            'message' => 'Data berhasil diupdate!'
        ]);
        return;
    }

    // ✅ Kalau nama brownbox berubah → cek apakah brownbox baru sudah ada
    $jumlahSkuLama = $this->db->where('brownbox', $brownbox_lama)->count_all_results('master_keepstock');
    $jumlahSkuBaru = $this->db->where('brownbox', $brownbox_baru)->count_all_results('master_keepstock');

    // 🔹 Jika brownbox baru sudah ada → MERGE
    if ($jumlahSkuBaru > 0) {
        // Pastikan tidak ada SKU bentrok di brownbox baru
        foreach ($skus as $sku) {
            $conflict = $this->db->where('brownbox', $brownbox_baru)
                                 ->where('sku', trim($sku))
                                 ->count_all_results('master_keepstock');
            if ($conflict > 0) {
                echo json_encode([
                    'status'  => false,
                    'message' => "SKU <b>$sku</b> sudah ada di brownbox <b>$brownbox_baru</b>!"
                ]);
                return;
            }
        }

        // ✅ Lakukan merge: update semua baris brownbox lama jadi brownbox baru
        $rows = $this->db->where('brownbox', $brownbox_lama)->get('master_keepstock')->result();
        foreach ($rows as $i => $row) {
            $dataUpdate = [
                'brownbox'    => $brownbox_baru,
                'departement' => $departemen_id,
                'sku'         => isset($skus[$i]) ? trim($skus[$i]) : $row->sku,
                'qty'         => isset($qtys[$i]) ? (int)$qtys[$i] : $row->qty,
            ];
            // pastikan SKU tidak kosong
            if ($dataUpdate['sku'] !== '') {
                $this->db->where('id', $row->id)->update('master_keepstock', $dataUpdate);
            }
        }

        echo json_encode([
            'status'  => true,
            'message' => "Brownbox <b>$brownbox_lama</b> berhasil digabung ke <b>$brownbox_baru</b>!"
        ]);
        return;
    }

    // 🔹 Kalau brownbox baru belum ada
    if ($jumlahSkuLama > 1) {
        // Kalau lama punya >1 SKU → tidak boleh rename ke brownbox kosong
        echo json_encode([
            'status'  => false,
            'message' => "Brownbox <b>$brownbox_lama</b> punya lebih dari 1 SKU, tidak bisa di-rename!"
        ]);
        return;
    }

    // ✅ Lakukan rename ke brownbox baru
    $this->db->where('brownbox', $brownbox_lama)->update('master_keepstock', [
        'brownbox'    => $brownbox_baru,
        'departement' => $departemen_id,
        'sku'         => isset($skus[0]) ? trim($skus[0]) : '',
        'qty'         => isset($qtys[0]) ? (int)$qtys[0] : 0
    ]);

    echo json_encode([
        'status'  => true,
        'message' => "Brownbox <b>$brownbox_lama</b> berhasil di-rename menjadi <b>$brownbox_baru</b>!"
    ]);
}

    // ✅ Get detail untuk modal edit
    public function get_detail()
    {
        $brownbox = $this->input->post('brownbox');
        $data = $this->ModelMasterKeepstock->get_sku_by_brownbox($brownbox);
        echo json_encode($data);
    }

    // ✅ Printsheet
    public function printsheet($brownbox)
    {
        $items = $this->db->select('mk.sku, mk.qty, ld.departement, lb.numb_rack')
            ->from('master_keepstock mk')
            ->join('list_departement ld', 'ld.id_departement = mk.departement', 'left')
            ->join('list_barang lb', 'lb.sku = mk.sku', 'left')
            ->where('mk.brownbox', $brownbox)
            ->get()
            ->result();

        if (!$items) {
            show_404();
            return;
        }

        $data = [
            'brownbox' => $brownbox,
            'items' => $items,
        ];

        $this->load->view('Barang/MasterKeepstock/printsheet', $data);
    }
}
