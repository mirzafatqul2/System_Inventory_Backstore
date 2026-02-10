<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelDashboardBarang extends CI_Model {

    public function get_kpi_box_summary()
    {
        $max_box = 539;

    $query_filled = $this->db->query("
        SELECT COUNT(DISTINCT brownbox) AS box_terisi
        FROM master_keepstock
        WHERE sku IS NOT NULL AND sku != 0
    ")->row();

    $box_terisi = $query_filled ? (int)$query_filled->box_terisi : 0;
    $box_kosong = $max_box - $box_terisi;

    $query_total_sku = $this->db->query("
        SELECT COUNT(DISTINCT sku) AS total_sku
        FROM master_keepstock
        WHERE sku IS NOT NULL AND sku != 0
    ")->row();

    $total_sku = $query_total_sku ? (int)$query_total_sku->total_sku : 0;

    return [
        'max_box'    => $max_box,
        'box_terisi' => $box_terisi,
        'box_kosong' => $box_kosong,
        'total_sku'  => $total_sku,
    ];
    }

    public function get_box_per_departemen()
    {
        $sql = "SELECT ld.departement AS departement_name, COUNT(DISTINCT mk.brownbox) AS jumlah_box
                FROM master_keepstock mk
                JOIN list_departement ld ON ld.id_departement = mk.departement
                WHERE mk.sku IS NOT NULL AND mk.sku != 0
                GROUP BY mk.departement, ld.departement
                ORDER BY ld.id_departement";
        return $this->db->query($sql)->result();
    }

    public function get_total_amount_per_departemen()
    {
        $sql = "SELECT ld.departement AS departement_name, 
                       SUM(lb.price * mk.qty) AS total_amount
                FROM master_keepstock mk
                JOIN list_barang lb ON lb.sku = mk.sku
                JOIN list_departement ld ON ld.id_departement = mk.departement
                WHERE lb.price IS NOT NULL
                GROUP BY mk.departement, ld.departement
                ORDER BY ld.id_departement";
        return $this->db->query($sql)->result();
    }

    public function get_tren_refill_per_bulan()
    {
        $sql = "SELECT DATE_FORMAT(refill_date, '%Y-%m') AS bulan, COUNT(sku) AS total_refill
                FROM refill_keepstock
                GROUP BY bulan
                ORDER BY bulan";
        return $this->db->query($sql)->result();
    }

    public function get_sku_stok_kritis($threshold = 5)
    {
        $sql = "SELECT lb.sku, lb.description, lb.qty AS qty_list_barang, mk.qty AS qty_keepstock, (lb.qty - mk.qty) AS selisih
                FROM list_barang lb
                JOIN master_keepstock mk ON mk.sku = lb.sku
                WHERE lb.qty - mk.qty <= ?
                ORDER BY selisih DESC";
        return $this->db->query($sql, [$threshold])->result();
    }

    public function get_refill_by_date($tanggal)
    {
        $sql = "SELECT rk.sku, lb.description, rk.qty_refill, rk.brownbox
                FROM refill_keepstock rk
                JOIN list_barang lb ON lb.sku = rk.sku
                WHERE DATE(rk.refill_date) = ?
                ORDER BY rk.brownbox, rk.sku";
        return $this->db->query($sql, [$tanggal])->result();
    }
}
