<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelRefillKeepstock extends CI_Model {

    var $table = 'refill_keepstock';
    var $column_order = [
        'refill_keepstock.id_refill',
        'refill_keepstock.refill_date',
        'refill_keepstock.brownbox',
        'refill_keepstock.sku',
        'refill_keepstock.qty_refill',
        'refill_keepstock.refill_by'
    ];
    var $column_search = [
        'refill_keepstock.refill_date',
        'refill_keepstock.brownbox',
        'refill_keepstock.sku',
        'refill_keepstock.refill_by'
    ];
    var $order = ['refill_keepstock.refill_date' => 'desc'];

    private function _get_datatables_query()
    {
        $this->db->from($this->table);
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        if (!empty($search_value)) {
            $this->db->group_start();
            foreach ($this->column_search as $i => $item) {
                if ($i === 0) {
                    $this->db->like($item, $search_value);
                } else {
                    $this->db->or_like($item, $search_value);
                }
            }
            $this->db->group_end();
        }

        if (isset($_POST['order'])) {
            $order_col_idx = $_POST['order'][0]['column'];
            $order_dir = $_POST['order'][0]['dir'];
            if (isset($this->column_order[$order_col_idx])) {
                $this->db->order_by($this->column_order[$order_col_idx], $order_dir);
            }
        } else if (!empty($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        $length = isset($_POST['length']) ? (int)$_POST['length'] : -1;
        $start  = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        if ($length != -1) $this->db->limit($length, $start);
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

   public function add_refill($data)
{
    // Cek apakah brownbox dan sku ada di master_keepstock
    $this->db->where(['brownbox' => $data['brownbox'], 'sku' => $data['sku']]);
    $query = $this->db->get('master_keepstock');
    $row = $query->row();

    if (!$row) {
        // SKU dan brownbox tidak ditemukan
        return [
            'status' => false,
            'message' => 'SKU dan Brownbox tidak ditemukan di master_keepstock.'
        ];
    }

    // Hitung stok baru setelah refill (boleh tambah atau kurang)
    $new_qty = $row->qty + $data['qty_refill'];
    if ($new_qty < 0) {
        return [
            'status' => false,
            'message' => 'Stok tidak cukup untuk dikurangi sebanyak ' . $data['qty_refill']
        ];
    }

    // Simpan ke tabel refill_keepstock
    $this->db->insert('refill_keepstock', $data);

    // Update master_keepstock
    $this->db->where(['brownbox' => $data['brownbox'], 'sku' => $data['sku']]);

    if ($new_qty == 0) {
        // Jika stok habis, null-kan semua kolom kecuali brownbox
        $this->db->update('master_keepstock', [
            'sku'        => null,
            'qty'        => null,
            'departement' => null,
            // tambahkan kolom lain yang ingin di-null-kan di sini
        ]);
    } else {
        // Jika stok masih ada, update qty saja
        $this->db->update('master_keepstock', ['qty' => $new_qty]);
    }

    return ['status' => true];
}


}
