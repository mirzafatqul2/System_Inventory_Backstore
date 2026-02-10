<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelDashboardOmnimbus extends CI_Model
{
    // Total kerugian dari variance negatif (Short)
    public function get_total_short_value()
    {
        $this->db->select('ABS(SUM(amount * variance)) AS total_short', false);
        $this->db->from('stock_checklist');
        $this->db->where('variance <', 0);
        return $this->db->get()->row()->total_short ?: 0;
    }

    // Total kelebihan dari variance positif (Extra)
    public function get_total_extra_value()
    {
        $this->db->select('ABS(SUM(amount * variance)) AS total_extra', false);
        $this->db->from('stock_checklist');
        $this->db->where('variance >', 0);
        return $this->db->get()->row()->total_extra ?: 0;
    }

    // Total kerugian dari data_damage (Missing)
    public function get_total_missing_value()
    {
        $this->db->select('ABS(SUM(amount_damage * qty_damage)) AS total_missing', false);
        return $this->db->get('data_damage')->row()->total_missing ?: 0;
    }

    // Breakdown per assignment (stock checklist)
    public function get_loss_per_assignment()
{
    $this->db->select("
        assignment,
        SUM(CASE WHEN variance < 0 THEN ABS(amount * variance) ELSE 0 END) AS total_short,
        SUM(CASE WHEN variance > 0 THEN ABS(amount * variance) ELSE 0 END) AS total_extra
    ", false);
    $this->db->from('stock_checklist');
    $this->db->group_by('assignment');
    return $this->db->get()->result();
}


    // Breakdown per kategori damage
    public function get_loss_per_kategory()
    {
        $this->db->select('kategory_damage, ABS(SUM(amount_damage * qty_damage)) AS total_loss');
        $this->db->group_by('kategory_damage');
        return $this->db->get('data_damage')->result();
    }

    // Tren bulanan kerugian (stock checklist)
    public function get_trend_bulanan_loss_sc()
    {
        $this->db->select("DATE_FORMAT(created_at, '%Y-%m') AS bulan, ABS(SUM(amount * variance)) AS total_loss", false);
        $this->db->from('stock_checklist');
        $this->db->group_by("DATE_FORMAT(created_at, '%Y-%m')");
        $this->db->order_by('bulan');
        return $this->db->get()->result();
    }

    // Tren bulanan kerugian dari damage
    public function get_trend_bulanan_loss_damage()
    {
        $this->db->select("DATE_FORMAT(created_at, '%Y-%m') AS bulan, ABS(SUM(amount_damage * qty_damage)) AS total_loss", false);
        $this->db->from('data_damage');
        $this->db->group_by("DATE_FORMAT(created_at, '%Y-%m')");
        $this->db->order_by('bulan');
        return $this->db->get()->result();
    }

    // Tren bulanan short, extra, missing (gabungan)
    public function get_trend_bulanan_short_extra_missing()
    {
        $sql = "
            SELECT bulan, 
                   SUM(short_total) AS short_total, 
                   SUM(extra_total) AS extra_total, 
                   SUM(missing_total) AS missing_total
            FROM (
                SELECT DATE_FORMAT(created_at, '%Y-%m') AS bulan,
                       SUM(CASE WHEN variance < 0 THEN ABS(amount * variance) ELSE 0 END) AS short_total,
                       SUM(CASE WHEN variance > 0 THEN ABS(amount * variance) ELSE 0 END) AS extra_total,
                       0 AS missing_total
                FROM stock_checklist
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')

                UNION ALL

                SELECT DATE_FORMAT(created_at, '%Y-%m') AS bulan,
                       0 AS short_total,
                       0 AS extra_total,
                       ABS(SUM(amount_damage * qty_damage)) AS missing_total
                FROM data_damage
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ) AS combined
            GROUP BY bulan
            ORDER BY bulan ASC
        ";

        return $this->db->query($sql)->result();
    }

    // Riwayat stock checklist terbaru
    public function get_recent_stockcheck($limit = 5)
    {
        $this->db->select('assignment, sku, variance, amount, created_at');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('stock_checklist')->result();
    }

    // Riwayat data damage terbaru
    public function get_recent_damage($limit = 5)
    {
        $this->db->select('sku, qty_damage, amount_damage, created_at');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('data_damage')->result();
    }
}
