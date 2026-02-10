<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelSalesDashboard extends CI_Model {

    // ===================== MTD Metrics =====================

    public function get_mtd_sales($bulan, $tahun) {
        $this->db->select_sum('daily_sales');
        $this->db->where('MONTH(tanggal)', $bulan);
        $this->db->where('YEAR(tanggal)', $tahun);
        $this->db->where('tanggal <=', date('Y-m-d'));
        return $this->db->get('sales_achievements')->row()->daily_sales ?: 0;
    }

    public function get_mtd_target($bulan, $tahun) {
        $this->db->select_sum('daily_target');
        $this->db->where('MONTH(tanggal)', $bulan);
        $this->db->where('YEAR(tanggal)', $tahun);
        $this->db->where('tanggal <=', date('Y-m-d'));
        return $this->db->get('sales_achievements')->row()->daily_target ?: 0;
    }

    public function get_achievement_percentage($bulan, $tahun) {
        $sales = $this->get_mtd_sales($bulan, $tahun);
        $target = $this->get_mtd_target($bulan, $tahun);
        return $target > 0 ? round(($sales / $target) * 100, 2) : 0;
    }

    public function get_mtd_last_month($bulan, $tahun) {
        $tanggal_hari_ini = date('d');
        $first_day_this_month = DateTime::createFromFormat('Y-m-d', "$tahun-$bulan-01");
        $first_day_last_month = $first_day_this_month->modify('-1 month');
        $bulan_lalu = $first_day_last_month->format('m');
        $tahun_lalu = $first_day_last_month->format('Y');

        $this->db->select_sum('daily_sales');
        $this->db->where('MONTH(tanggal)', $bulan_lalu);
        $this->db->where('YEAR(tanggal)', $tahun_lalu);
        $this->db->where('DAY(tanggal) <=', $tanggal_hari_ini);
        return $this->db->get('sales_achievements')->row()->daily_sales ?: 0;
    }

    // ===================== Growth KPI Metrics =====================

    public function get_growth_metrics($bulan, $tahun) {
        $tanggal = date('d');
        $first_day_this_month = DateTime::createFromFormat('Y-m-d', "$tahun-$bulan-01");
        $first_day_last_year = (clone $first_day_this_month)->modify('-1 year');
        $bulan_tahun_lalu = $first_day_last_year->format('m');
        $tahun_lalu = $first_day_last_year->format('Y');

        // Data bulan ini
        $this_month = $this->db->select('
            SUM(daily_sales) AS sales,
            SUM(qty_sold) AS upt,
            (CASE WHEN SUM(transaction) > 0 THEN SUM(daily_sales)/SUM(transaction) ELSE 0 END) AS atv,
            (CASE WHEN SUM(traffic) > 0 THEN SUM(transaction)/SUM(traffic)*100 ELSE 0 END) AS scr
        ')
        ->where('MONTH(tanggal)', $bulan)
        ->where('YEAR(tanggal)', $tahun)
        ->where('DAY(tanggal) <=', $tanggal)
        ->get('sales_achievements')->row();

        // Data tahun lalu
        $last_year = $this->db->select('
            SUM(daily_sales) AS sales,
            SUM(qty_sold) AS upt,
            (CASE WHEN SUM(transaction) > 0 THEN SUM(daily_sales)/SUM(transaction) ELSE 0 END) AS atv,
            (CASE WHEN SUM(traffic) > 0 THEN SUM(transaction)/SUM(traffic)*100 ELSE 0 END) AS scr
        ')
        ->where('MONTH(tanggal)', $bulan_tahun_lalu)
        ->where('YEAR(tanggal)', $tahun_lalu)
        ->where('DAY(tanggal) <=', $tanggal)
        ->get('sales_achievements')->row();

        // Fungsi hitung pertumbuhan (%)
        $calc_growth = function($this_val, $last_val) {
            if ($last_val == 0) return 0;
            return (($this_val / $last_val) * 100) - 100;
        };

        return (object)[
            'sales_growth' => $calc_growth($this_month->sales ?: 0, $last_year->sales ?: 1),
            'upt_growth'   => $calc_growth($this_month->upt ?: 0, $last_year->upt ?: 1),
            'atv_growth'   => $calc_growth($this_month->atv ?: 0, $last_year->atv ?: 1),
            'scr_growth'   => $calc_growth($this_month->scr ?: 0, $last_year->scr ?: 1),
        ];
    }

    // ===================== Chart: Monthly =====================

    public function get_monthly_sales($tahun) {
        return $this->db->select('MONTH(tanggal) as bulan, SUM(daily_sales) as total_sales')
            ->where('YEAR(tanggal)', $tahun)
            ->group_by('MONTH(tanggal)')
            ->get('sales_achievements')->result();
    }

    public function get_monthly_sales_last_year($tahun_lalu) {
        return $this->db->select('MONTH(tanggal) as bulan, SUM(daily_sales) as total_sales')
            ->where('YEAR(tanggal)', $tahun_lalu)
            ->group_by('MONTH(tanggal)')
            ->get('sales_achievements')->result();
    }

    // ===================== Chart: Daily =====================

    public function get_labels_harian($bulan, $tahun) {
        $hari_terakhir = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $labels = [];
        for ($i = 1; $i <= $hari_terakhir; $i++) {
            $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        return $labels;
    }

    public function get_daily_targets($bulan, $tahun) {
        $result = $this->db->select('DAY(tanggal) as hari, daily_target')
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->get('sales_achievements')->result();
        $data = [];
        foreach($result as $row) $data[(int)$row->hari] = $row->daily_target;
        return $data;
    }

    public function get_daily_achievements($bulan, $tahun) {
        $result = $this->db->select('DAY(tanggal) as hari, daily_sales')
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->get('sales_achievements')->result();
        $data = [];
        foreach($result as $row) $data[(int)$row->hari] = $row->daily_sales;
        return $data;
    }

    public function get_daily_achievements_last_year($bulan, $tahun_lalu) {
        $result = $this->db->select('DAY(tanggal) as hari, daily_sales')
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun_lalu)
            ->get('sales_achievements')->result();
        $data = [];
        foreach($result as $row) $data[(int)$row->hari] = $row->daily_sales;
        return $data;
    }
}
