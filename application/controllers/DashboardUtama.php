<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardUtama extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model('ModelDashboardUtama');
  }

  public function index() {
    $menu = 'dashboard';
    $submenu = '';
    $menuItems = get_menu_items($menu, $submenu);

    $months = [
      1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
      7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
    ];
    $month = date('m');
    $year = date('Y');
    $tanggal = $this->input->get('tanggal');
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }
    $tanggal = $tanggal;

    $weekly_damage = $this->ModelDashboardUtama->get_weekly_damage($year, $month);
$weekly_short_extra = $this->ModelDashboardUtama->get_weekly_short_extra($year, $month);

    $summaryBrownbox = $this->ModelDashboardUtama->get_summary_brownbox_per_lokasi($tanggal);

    $monthly_sales = $this->ModelDashboardUtama->get_monthly_sales_with_incentive($year);
    $monthly_damage = $this->ModelDashboardUtama->get_monthly_damage($year);
    $monthly_short_extra = $this->ModelDashboardUtama->get_monthly_short_extra($year);
    $monthly_ib = $this->ModelDashboardUtama->get_monthly_ib($year);

    $target_up_to_date = $this->ModelDashboardUtama->get_total_target_up_to_yesterday();
$sales_up_to_date = $this->ModelDashboardUtama->get_total_sales_up_to_yesterday();

// Hitung sisa hari bulan ini mulai dari besok
$today = date('Y-m-d');
$besok = date('Y-m-d', strtotime('+1 day'));
$end_of_month = date('Y-m-t');
$sisa_hari = (strtotime($end_of_month) - strtotime($besok)) / 86400 + 1;

// Ambil rata-rata daily target bulan ini
$this->db->select_avg('daily_target', 'avg_daily_target');
$this->db->from('sales_achievements');
$this->db->where('MONTH(tanggal)', date('m'));
$this->db->where('YEAR(tanggal)', date('Y'));
$avg_daily_target_result = $this->db->get()->row();
$avg_daily_target = $avg_daily_target_result ? (float)$avg_daily_target_result->avg_daily_target : 0;

// Hitung gap target vs realisasi
$gap = $target_up_to_date - $sales_up_to_date;

// Today sales target = rata-rata + kekurangan dibagi sisa hari
$today_sales_target = ($sisa_hari > 0)
  ? max(0, $avg_daily_target + ($gap / $sisa_hari))
  : max(0, $avg_daily_target); // fallback kalau tidak ada sisa hari

  
$latest_date_raw = $this->ModelDashboardUtama->get_latest_sales_date();
$today_date = $latest_date_raw ?: date('Y-m-d'); // fallback kalau nggak ada latest date
// Ambil daily_target hari ini
$this->db->select('daily_target, daily_sales');
$this->db->from('sales_achievements');
$this->db->where('tanggal', $today_date);
$today_data = $this->db->get()->row();

$today_target = $today_data ? (float)$today_data->daily_target : 0;
$today_sales = $today_data ? (float)$today_data->daily_sales : 0;

    $last_avg_transaction = $this->ModelDashboardUtama->get_last_avg_transaction();
$last_upt = $this->ModelDashboardUtama->get_last_upt();


    $latest_date_raw = $this->ModelDashboardUtama->get_latest_sales_date();
    $latest_date = $latest_date_raw ? date('d-M-y', strtotime($latest_date_raw)) : '-';

    $current_month_ib = null;
foreach ($monthly_ib as $ib) {
  if ((int)$ib['month'] === (int)$month) {
    $current_month_ib = $ib;
    break;
  }
}

if ($current_month_ib && $current_month_ib['total_sku_ib'] > 0) {
  $percent = number_format(
    ($current_month_ib['total_ib_pending'] / $current_month_ib['total_sku_ib']) * 100,
    2
  );
} else {
  $percent = '0.00';
}

if ($target_up_to_date > 0) {
  $percent = ($sales_up_to_date / $target_up_to_date) * 100;
  if ($sales_up_to_date >= $target_up_to_date) {
    // Sales lebih besar dari target: hitung kelebihan
    $percent_over = number_format($percent - 100, 2);
    $percent_achieve = '+' . $percent_over;
    $performance_status = '✅ Achieved';
  } else {
    // Sales kurang dari target: hitung kekurangan
    $percent_under = number_format(100 - $percent, 2);
    $percent_achieve = '-' . $percent_under;
    $performance_status = '⚠ Not Achieved';
  }
} else {
  $percent_achieve = '0.00';
  $performance_status = '⚠ No Target';
}

    $performance_status = ($sales_up_to_date >= $target_up_to_date) ? '✅ Achieved' : '⚠ Not Achieved';

    $data = [
      'title' => 'Dashboard Utama',
      'isi' => 'DashboardUtama/view',
      'menu' => $menu,
      'submenu' => $submenu,
      'menuItems' => $menuItems,
    'weekly_damage' => $weekly_damage,
    'weekly_short_extra' => $weekly_short_extra,
      'months' => $months,
      'monthly_sales' => $monthly_sales,
      'monthly_damage' => $monthly_damage,
      'monthly_short_extra' => $monthly_short_extra,
      'monthly_ib' => $monthly_ib,
      'today_target' => $today_target,
'today_sales' => $today_sales,
      'last_avg_transaction' => $last_avg_transaction,
  'last_upt' => $last_upt,
      'latest_date' => $latest_date,
      'target_up_to_date' => $target_up_to_date,
      'sales_up_to_date' => $sales_up_to_date,
      'percent_achieve' => $percent_achieve,
      'performance_status' => $performance_status,
            'summaryBrownbox' => $summaryBrownbox,
      'today_sales_target' => $today_sales_target,
      'percent' => $percent
    ];

    $this->load->view('layout/wrapper', $data);
  }
}
