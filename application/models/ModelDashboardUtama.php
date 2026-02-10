<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelDashboardUtama extends CI_Model {

  public function get_monthly_sales_with_incentive($year) {
    $this->db->select('MONTH(tanggal) AS month, SUM(IFNULL(daily_sales,0)) AS sales');
    $this->db->from('sales_achievements');
    $this->db->where('YEAR(tanggal)', $year);
    $this->db->group_by('month');
    $this->db->order_by('month', 'ASC');
    $result = $this->db->get()->result_array();

    $sales_with_incentive = [];
    foreach ($result as $row) {
      $sales = (float)$row['sales'];
      if ($sales > 1200000000) {
        $incentive = $sales * 0.016;
      } elseif ($sales >= 600000000) {
        $incentive = $sales * 0.014;
      } else {
        $incentive = $sales * 0.012;
      }
      $sales_with_incentive[] = [
        'month' => (int)$row['month'],
        'sales' => $sales,
        'incentive' => $incentive
      ];
    }
    return $sales_with_incentive;
  }

  public function get_monthly_damage($year) {
    $this->db->select('MONTH(created_at) AS month, ABS(SUM(IFNULL(amount_damage * qty_damage,0))) AS damage');
    $this->db->from('data_damage');
    $this->db->where('YEAR(created_at)', $year);
    $this->db->group_by('month');
    return $this->db->get()->result_array();
  }

  public function get_monthly_short_extra($year) {
    $this->db->select('MONTH(created_at) AS month');
    $this->db->select('SUM(IF(variance < 0, ABS(amount * variance), 0)) AS short_amount', false);
    $this->db->select('SUM(IF(variance > 0, amount * variance, 0)) AS extra_amount', false);
    $this->db->from('stock_checklist');
    $this->db->where('YEAR(created_at)', $year);
    $this->db->group_by('month');
    return $this->db->get()->result_array();
}


  public function get_monthly_ib($year) {
    $this->db->select('
      MONTH(tanggal) AS month,
      SUM(sku_ib) AS total_sku_ib,
      SUM(ib_pending) AS total_ib_pending
    ');
    $this->db->from('datang_barang');
    $this->db->where('YEAR(tanggal)', $year);
    $this->db->group_by('month');
    return $this->db->get()->result_array();
  }

  public function get_total_target_up_to_yesterday() {
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $month_start = date('Y-m-01');
    $this->db->select_sum('daily_target', 'total_target');
    $this->db->from('sales_achievements');
    $this->db->where('tanggal >=', $month_start);
    $this->db->where('tanggal <=', $yesterday);
    return (float) ($this->db->get()->row()->total_target ?: 0);
  }

  public function get_total_sales_up_to_yesterday() {
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $month_start = date('Y-m-01');
    $this->db->select_sum('daily_sales', 'total_sales');
    $this->db->from('sales_achievements');
    $this->db->where('tanggal >=', $month_start);
    $this->db->where('tanggal <=', $yesterday);
    return (float) ($this->db->get()->row()->total_sales ?: 0);
  }

  public function get_latest_sales_date() {
  $this->db->select('tanggal');
  $this->db->from('sales_achievements');
  $this->db->where('daily_sales IS NOT NULL');
  $this->db->where('daily_sales >', 0);
  $this->db->order_by('tanggal', 'DESC');
  $this->db->limit(1);
  $result = $this->db->get()->row();
  return $result ? $result->tanggal : null;
}


  public function get_last_avg_transaction() {
    $latest_date = $this->get_latest_sales_date();
    if (!$latest_date) return null;

    $this->db->select('SUM(daily_sales) AS total_sales, SUM(transaction) AS total_transactions');
    $this->db->from('sales_achievements');
    $this->db->where('tanggal', $latest_date);
    $result = $this->db->get()->row();

    if ($result && $result->total_transactions > 0) {
        return $result->total_sales / $result->total_transactions;
    }
    return null;
}

public function get_last_upt() {
    $latest_date = $this->get_latest_sales_date();
    if (!$latest_date) return null;

    $this->db->select('SUM(qty_sold) AS total_qty, SUM(transaction) AS total_transactions');
    $this->db->from('sales_achievements');
    $this->db->where('tanggal', $latest_date);
    $result = $this->db->get()->row();

    if ($result && $result->total_transactions > 0) {
        return $result->total_qty / $result->total_transactions;
    }
    return null;
}

 public function get_summary_brownbox_per_lokasi($tanggal = null)
    {
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }

        // Hardcoded kapasitas maksimal
        $max_per_lokasi = [
            'LANTAI 1' => 231,
            'LANTAI 2' => 308,
        ];

        // Mapping brownbox prefix ke lokasi
        $lokasi_mapping = [
            'A' => 'LANTAI 1',
            'B' => 'LANTAI 2',
        ];

        // Ambil master keepstock hari ini
        $this->db->select('brownbox, sku, qty');
        $master = $this->db->get('master_keepstock')->result_array();

        $keepstock_hari_ini = [];
        $sku_hari_ini = [];

        $brownbox_qty = [];

        foreach ($master as $row) {
            $prefix = strtoupper(substr($row['brownbox'], 0, 1));
            $lokasi = $lokasi_mapping[$prefix] ?? null;
            if (!$lokasi) continue;

            // Simpan untuk perhitungan total keepstock
            $brownbox_qty[$row['brownbox']][$lokasi][] = $row['qty'];

            // Hitung brownbox aktif
            if (!empty($row['qty']) && $row['qty'] > 0) {
                $keepstock_hari_ini[$lokasi][$row['brownbox']] = true;
            }

            // Hitung SKU aktif
            if (!empty($row['sku'])) {
                $sku_hari_ini[$lokasi][$row['sku']] = true;
            }
        }

        // Ambil refill hari ini
        $this->db->where('DATE(refill_date)', $tanggal);
        $refill = $this->db->get('refill_keepstock')->result_array();

        $penambahan = ['LANTAI 1' => [], 'LANTAI 2' => []];
        $penurunan = ['LANTAI 1' => [], 'LANTAI 2' => []];
        $sku_berubah = ['LANTAI 1' => []];

        foreach ($refill as $row) {
            $prefix = strtoupper(substr($row['brownbox'], 0, 1));
            $lokasi = $lokasi_mapping[$prefix] ?? null;
            if (!$lokasi) continue;

            $brownbox = $row['brownbox'];
            $sku = $row['sku'];

            // SKU berubah (selama ada refill apapun)
            if ($sku) {
                $sku_berubah[$lokasi][$sku] = true;
            }

            // Cek total qty setelah refill (dari master)
            $qty_after = array_sum($brownbox_qty[$brownbox][$lokasi] ?? [0]);

            if ($qty_after > 0) {
                // Jika sebelumnya kosong, dianggap penambahan
                if (!isset($keepstock_hari_ini[$lokasi][$brownbox])) {
                    $penambahan[$lokasi][$brownbox] = true;
                }
            } else {
                // Qty jadi 0 → dianggap penurunan
                $penurunan[$lokasi][$brownbox] = true;
            }
        }

        // Susun summary akhir
        $summary = [];

        foreach (['LANTAI 1', 'LANTAI 2'] as $lokasi) {
            $total_keepstock = count($keepstock_hari_ini[$lokasi] ?? []);
            $total_penambahan = count($penambahan[$lokasi]);
            $total_penurunan = count($penurunan[$lokasi]);

            $summary[$lokasi] = [
                'max_keepstock' => $max_per_lokasi[$lokasi],
                'keepstock_hari_ini' => $total_keepstock,
                'sku_hari_ini' => count($sku_hari_ini[$lokasi] ?? []),
                'keepstock_kosong' => max(0, $max_per_lokasi[$lokasi] - $total_keepstock),
                'penambahan' => $total_penambahan,
                'penurunan' => $total_penurunan,
                'total_keepstock' => $total_keepstock + $total_penambahan - $total_penurunan,
                'total_sku' => count($sku_berubah[$lokasi] ?? []),
            ];
        }

        $summary['tanggal'] = $tanggal;
        return $summary;
    }
    
  public function get_weekly_damage($year, $month) {
    $this->db->select("
        WEEK(created_at, 1) - WEEK(DATE_SUB(created_at, INTERVAL DAYOFMONTH(created_at)-1 DAY), 1) + 1 AS week_number,
        ABS(SUM(amount_damage * qty_damage)) AS total_damage
    ");
    $this->db->from('data_damage');
    $this->db->where('YEAR(created_at)', $year);
    $this->db->where('MONTH(created_at)', $month);
    $this->db->group_by('week_number');
    $this->db->order_by('week_number');
    return $this->db->get()->result_array();
}

public function get_weekly_short_extra($year, $month) {
    $this->db->select("
        WEEK(created_at, 1) - WEEK(DATE_SUB(created_at, INTERVAL DAYOFMONTH(created_at)-1 DAY), 1) + 1 AS week_number,
       SUM(CASE WHEN variance < 0 THEN ABS(amount * variance) ELSE 0 END) AS total_short,
        SUM(CASE WHEN variance > 0 THEN ABS(amount * variance) ELSE 0 END) AS total_extra
    ");
    $this->db->from('stock_checklist');
    $this->db->where('YEAR(created_at)', $year);
    $this->db->where('MONTH(created_at)', $month);
    $this->db->group_by('week_number');
    $this->db->order_by('week_number');
    return $this->db->get()->result_array();
}

}
