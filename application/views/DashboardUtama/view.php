<div class="container-fluid">
  <div class="row">
    <!-- LEFT COLUMN: Daily Sales Report -->
    <div class="col-md-4">
      <div class="card card-success">
        <div class="card-header">
          <h3 class="card-title">Daily Sales Report</h3>
        </div>
        <div class="card-body">
          <table class="table table-sm table-bordered mb-3">
            <tr>
              <th>Latest Date of Sales</th>
              <td class="text-right"><?= $latest_date ?></td>
            </tr>
            <tr>
              <th>Target Up-to-Date</th>
              <td class="text-right"><?= number_format($target_up_to_date, 2, ',', '.') ?></td>
            </tr>
            <tr>
              <th>Sales Up-to-Date</th>
              <td class="text-right"><?= number_format($sales_up_to_date, 2, ',', '.') ?></td>
            </tr>
            <tr>
              <th>% Achieve</th>
              <td class="text-right"><?= $percent_achieve ?>%</td>
            </tr>
            <tr>
              <th>Performance Status</th>
              <td class="text-right <?= ($performance_status == '✅ Achieved') ? 'text-success' : 'text-danger' ?>">
                <?= $performance_status ?>
              </td>
            </tr>
            <tr>
              <th>Today Sales Target</th>
              <td class="text-right"><?= number_format($today_sales_target, 2, ',', '.') ?></td>
            </tr>
          </table>
          <div class="mt-3 p-3 rounded" style="background-color: #f9f9f9; border-left: 5px solid #28a745;">
        <h6 class="mb-2"><i class="fas fa-lightbulb text-success"></i> Things to Improve</h6>
        <ul class="mb-0">
          <li>Transaction (Customer Traffic): 
  <b><?= ($today_sales < $today_target) ? 'Yes' : '' ?></b>
</li>

          <li>ATV (Avg. Transaction Value): 
            <b><?= ($last_avg_transaction < 100000) ? 'Yes' : '' ?></b>
          </li>
          <li>UPT (Units per Transaction): 
            <b><?= ($last_upt < 4) ? 'Yes' : '' ?></b>
          </li>
        </ul>
      </div>

        </div>
      </div>
    </div>

    <!-- MIDDLE COLUMN: Daily Refill KeepStock -->
<div class="col-md-4">
  <div class="card card-warning">
    <div class="card-header">
      <h3 class="card-title">Summary Brownbox</h3>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-bordered table-sm">
        <thead>
          <tr>
            <th><?= htmlspecialchars($summaryBrownbox['tanggal']) ?></th>
            <?php foreach (['LANTAI 1', 'LANTAI 2'] as $lok): ?>
              <th><?= $lok ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
  <?php
  $rows = [
    'KEEPSTOCK MAKSIMUM'   => 'max_keepstock',
    'KEEPSTOCK HARI INI'   => 'keepstock_hari_ini',
    'SKU HARI INI'         => 'sku_hari_ini',
    'KEEPSTOCK KOSONG'     => 'keepstock_kosong',
    'PENAMBAHAN'           => 'penambahan',
    'PENURUNAN'            => 'penurunan',
    'TOTAL SKU'            => 'total_sku',
    'TOTAL KEEPSTOCK'      => 'total_keepstock',
  ];
  foreach ($rows as $label => $key): ?>
    <tr>
      <td><?= $label ?></td>
      <?php foreach (['LANTAI 1', 'LANTAI 2'] as $lok): ?>
        <td><?= isset($summaryBrownbox[$lok][$key]) ? number_format($summaryBrownbox[$lok][$key]) : '0' ?></td>
      <?php endforeach; ?>
    </tr>
  <?php endforeach; ?>
</tbody>

      </table>
    </div>
  </div>
</div>

    <!-- RIGHT COLUMN: Weekly Damage, Short, Extra -->
    <div class="col-md-4">
  <div class="card card-warning">
    <div class="card-header">
      <h3 class="card-title">Weekly Damage, Short & Extra</h3>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-sm table-bordered mb-0">
        <thead class="text-center bg-secondary">
          <tr>
            <th>Week</th>
            <th>Total Damage</th>
            <th>Total Short</th>
            <th>Total Extra</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $total_damage_month = 0;
          $total_short_month = 0;
          $total_extra_month = 0;

          for ($week=1; $week<=5; $week++) { // asumsi max 5 minggu/bulan
              $damage = 0; $short = 0; $extra = 0;

              foreach ($weekly_damage as $row) {
                  if ($row['week_number'] == $week) $damage = $row['total_damage'];
              }
              foreach ($weekly_short_extra as $row) {
                  if ($row['week_number'] == $week) {
                      $short = $row['total_short'];
                      $extra = $row['total_extra'];
                  }
              }

              $total_damage_month += $damage;
              $total_short_month += $short;
              $total_extra_month += $extra;
          ?>
          <tr class="text-right">
            <td class="text-center"><?= $week ?></td>
            <td><?= number_format($damage, 0, ',', '.') ?></td>
            <td><?= number_format($short, 0, ',', '.') ?></td>
            <td><?= number_format($extra, 0, ',', '.') ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>

      <!-- Insights Box -->
      <div class="mt-3 p-3 rounded" style="background-color: #f9f9f9; border-left: 5px solid #ffc107;">
        <ul class="mb-0">
          <li>Total Damage This Month: 
            <b class="text-danger"><?= number_format($total_damage_month, 0, ',', '.') ?></b>
          </li>
          <li>Total Short This Month: 
            <b class="text-danger"><?= number_format($total_short_month, 0, ',', '.') ?></b>
          </li>
          <li>Total Extra This Month: 
            <b class="text-success"><?= number_format($total_extra_month, 0, ',', '.') ?></b>
          </li>
          <li>Worst Week: 
            <b class="text-danger">
            <?php
              $max_issue = 0; $worst_week = '-';
              for ($week=1; $week<=5; $week++) {
                $d = 0; $s = 0;
                foreach ($weekly_damage as $row) if ($row['week_number'] == $week) $d = $row['total_damage'];
                foreach ($weekly_short_extra as $row) if ($row['week_number'] == $week) $s = $row['total_short'];
                if (($d+$s) > $max_issue) {
                  $max_issue = $d+$s;
                  $worst_week = 'Week '.$week;
                }
              }
              echo $worst_week;
            ?>
            </b>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
</div> <!-- CLOSE ROW -->

  <!-- NOTICE BOARD -->
  <div class="card">
    <div class="card-header bg-primary">
      <h3 class="card-title text-white">NOTICE BOARD</h3>
    </div>
    <div class="card-body table-responsive">
      <table id="performanceTable" class="table table-bordered table-hover text-sm">
        <thead class="bg-secondary text-center">
          <tr>
            <th class="d-none">No Bulan</th> <!-- hidden column for sorting -->
            <th class="text-left">Bulan</th>
            <th>Total Sales</th>
            <th>Total Incentive</th>
            <th>Total Damage</th>
            <th>Total Short</th>
            <th>Total Extra</th>
            <th>Total SKU IB</th>
            <th>Total IB Pending</th>
            <th>%</th>
          </tr>
        </thead>
        <tbody>
  <?php
  $sales_data = [];
  foreach ($monthly_sales as $row) {
    $sales_data[(int)$row['month']] = [
      'sales' => $row['sales'],
      'incentive' => $row['incentive']
    ];
  }
  $damage_data = [];
  foreach ($monthly_damage as $row) {
    $damage_data[(int)$row['month']] = $row['damage'];
  }
  $short_extra_data = [];
  foreach ($monthly_short_extra as $row) {
    $short_extra_data[(int)$row['month']] = [
      'short_amount' => $row['short_amount'],
      'extra_amount' => $row['extra_amount']
    ];
  }
  $ib_data = [];
  foreach ($monthly_ib as $row) {
    $ib_data[(int)$row['month']] = [
      'sku_ib' => $row['total_sku_ib'],
      'ib_pending' => $row['total_ib_pending']
    ];
  }

  // Inisialisasi total
  $total_sales_sum = 0;
  $total_incentive_sum = 0;
  $total_damage_sum = 0;
  $total_short_sum = 0;
  $total_extra_sum = 0;
  $total_sku_ib_sum = 0;
  $total_ib_pending_sum = 0;
  $total_months_with_sales = 0;

  foreach ($months as $month_num => $month_name):
    $total_sales = $sales_data[$month_num]['sales'] ?? 0;
    $total_incentive = $sales_data[$month_num]['incentive'] ?? 0;
    $total_damage = $damage_data[$month_num] ?? 0;
    $total_short = $short_extra_data[$month_num]['short_amount'] ?? 0;
    $total_extra = $short_extra_data[$month_num]['extra_amount'] ?? 0;
    $total_sku_ib = $ib_data[$month_num]['sku_ib'] ?? 0;
    $total_ib_pending = $ib_data[$month_num]['ib_pending'] ?? 0;
    $percent= ($total_sku_ib > 0)
      ? number_format(($total_ib_pending / $total_sku_ib) * 100, 2)
      : '-';

    // Akumulasi total
    $total_sales_sum += $total_sales;
    $total_incentive_sum += $total_incentive;
    $total_damage_sum += $total_damage;
    $total_short_sum += $total_short;
    $total_extra_sum += $total_extra;
    $total_sku_ib_sum += $total_sku_ib;
    $total_ib_pending_sum += $total_ib_pending;
    if ($total_sales > 0) $total_months_with_sales++;
  ?>
  <tr class="text-right">
    <td class="d-none"><?= $month_num ?></td>
    <td class="text-left"><?= $month_name ?></td>
    <td><?= number_format($total_sales,0,',','.') ?></td>
    <td><?= number_format($total_incentive,0,',','.') ?></td>
    <td><?= number_format($total_damage,0,',','.') ?></td>
    <td><?= number_format($total_short,0,',','.') ?></td>
    <td><?= number_format($total_extra,0,',','.') ?></td>
    <td><?= number_format($total_sku_ib) ?></td>
    <td><?= number_format($total_ib_pending) ?></td>
    <td><?= $percent ?>%</td>
  </tr>
  <?php endforeach; ?>

  <?php
// Hitung net missing dan net loss
$net_missing = $total_damage_sum + $total_short_sum;
$net_loss = $net_missing - $total_extra_sum;
$average_sales = ($total_months_with_sales > 0) ? $total_sales_sum / $total_months_with_sales : 0;

// Hitung total percentage IB pending
$total_percent_achieve = ($total_sku_ib_sum > 0)
  ? number_format(($total_ib_pending_sum / $total_sku_ib_sum) * 100, 2)
  : '-';
?>

<!-- TOTAL ROW -->
<tr class="text-right font-weight-bold bg-light">
  <td class="d-none"></td>
  <td class="text-left">TOTAL</td>
  <td><?= number_format($total_sales_sum, 0, ',', '.') ?></td>
  <td><?= number_format($total_incentive_sum, 0, ',', '.') ?></td>
  <td><?= number_format($total_damage_sum, 0, ',', '.') ?></td>
  <td><?= number_format($total_short_sum, 0, ',', '.') ?></td>
  <td><?= number_format($total_extra_sum, 0, ',', '.') ?></td>
  <td><?= number_format($total_sku_ib_sum) ?></td>
  <td><?= number_format($total_ib_pending_sum) ?></td>
  <td><?= $total_percent_achieve ?>%</td>
</tr>

<!-- COMBINED AVERAGE & NET ROW -->
<tr class="text-right font-weight-bold bg-secondary text-white">
  <td class="d-none"></td>
  <td class="text-left" colspan="2">AVERAGE SALES: <?= number_format($average_sales, 0, ',', '.') ?></td>
  <td class="text-left" colspan="3">NET MISSING (Damage + Short): <?= number_format($net_missing, 0, ',', '.') ?></td>
  <td class="text-left" colspan="4">NET LOSS (Net Missing - Extra): <?= number_format($net_loss, 0, ',', '.') ?></td>
</tr>

</tbody>

      </table>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('#performanceTable').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": false,
      "autoWidth": false,
      "responsive": true,
      "order": [[0, "asc"]],
      "columnDefs": [
        { "targets": 0, "visible": false }
      ]
    });
  });
</script>
