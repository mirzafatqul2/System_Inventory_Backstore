<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ModelSalesAchievement extends CI_Model
{
    public function get_monthly_summary()
    {
        $this->db->select('
            DATE_FORMAT(sa.tanggal, "%Y-%m") AS bulan,
            SUM(sa.daily_sales) AS total_sales,
            MAX(st.base_target) AS target_bulanan,
            SUM(sa.qty_sold) AS total_barang_kejual,
            SUM(sa.transaction) AS total_transaksi,
            AVG(sa.traffic) AS rata_traffic,
            (SUM(sa.qty_sold)/NULLIF(SUM(sa.transaction),0)) AS upt,
            (SUM(sa.daily_sales)/NULLIF(SUM(sa.transaction),0)) AS atv,
            (SUM(sa.transaction)/NULLIF(SUM(sa.traffic),0)) AS scr
        ');
        $this->db->from('sales_achievements sa');
        $this->db->join('sales_targets st','sa.sales_target_id = st.id','left');
        $this->db->group_by('bulan');
        $this->db->order_by('bulan','DESC');
        return $this->db->get()->result();
    }

    public function get_monthly_detail($bulan)
{
    $this->db->select('
        tanggal,
        daily_target,
        daily_sales,
        qty_sold,
        transaction,
        traffic
    ');
    $this->db->from('sales_achievements');
    $this->db->where("DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'", null, false);
    $this->db->order_by('tanggal', 'ASC');
    return $this->db->get()->result();
}



    public function input($tanggal, $data)
    {
        $exists = $this->db->get_where('sales_achievements', ['tanggal' => $tanggal])->row();
        if ($exists) {
            $this->db->where('tanggal', $tanggal);
            $this->db->update('sales_achievements', $data);
        } else {
            $data['tanggal'] = $tanggal;
            $this->db->insert('sales_achievements', $data);
        }
    }
}
