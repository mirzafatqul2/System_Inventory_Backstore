<div class="container-fluid">

    <!-- KPI Boxes -->
    <div class="row">
        <div class="col-md-3">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kapasitas Maksimal</span>
                    <span class="info-box-number"><?= number_format($kpi['max_box']) ?> Box</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Box Terisi</span>
                    <span class="info-box-number"><?= number_format($kpi['box_terisi']) ?> Box</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Box Kosong</span>
                    <span class="info-box-number"><?= number_format($kpi['box_kosong']) ?> Box</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-barcode"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total SKU</span>
                    <span class="info-box-number"><?= number_format($kpi['total_sku']) ?> SKU</span>
                </div>
            </div>
        </div>
    </div>

     <!-- Chart Row -->
    <div class="row">
        <!-- PIE CHART -->
        <div class="col-md-6">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Box Terisi per Departemen</h3>
                </div>
                <div class="card-body">
                    <canvas id="pieBoxPerDept" style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- BAR CHART -->
        <!-- BAR CHART: Total Amount -->
<div class="col-md-6">
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Total Amount per Departemen</h3>
        </div>
        <div class="card-body">
            <canvas id="barAmountPerDept" style="min-height:300px;height:300px;max-height:300px;"></canvas>
        </div>
    </div>
</div>

    </div>

    <!-- Tren Refill -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Tren Refill per Bulan</h3>
                </div>
                <div class="card-body">
                    <canvas id="lineTrenRefill" style="height:300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Data Refill Tanggal <?= htmlspecialchars(date('d/m/Y', strtotime('-1 day'))) ?></h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm" id="tableRefillYesterday">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Deskripsi</th>
                                <th>Qty Refill</th>
                                <th>Brownbox</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($refillYesterday as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row->sku) ?></td>
                                    <td><?= htmlspecialchars($row->description) ?></td>
                                    <td><?= number_format($row->qty_refill) ?></td>
                                    <td><?= htmlspecialchars($row->brownbox) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
                            </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data chart dari PHP
    const labels = [<?php foreach($boxPerDepartemen as $d) echo "\"$d->departement_name\","; ?>];
    const dataValues = [<?php foreach($boxPerDepartemen as $d) echo "$d->jumlah_box,"; ?>];
    const dataAmount = [<?php foreach($amountPerDepartemen as $d) echo "$d->total_amount,"; ?>];

    // PIE
    new Chart(document.getElementById('pieBoxPerDept').getContext('2d'), {
        type: 'pie',
        data: { labels, datasets: [{ data: dataValues, backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545','#6f42c1','#20c997','#fd7e14','#17a2b8','#343a40','#6610f2'] }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // BAR
    new Chart(document.getElementById('barAmountPerDept').getContext('2d'), {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Total Amount (Rp)', data: dataAmount, backgroundColor: '#17a2b8' }] },
        options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } } } }
    });

    // Line Chart Tren Refill
    new Chart(document.getElementById('lineTrenRefill').getContext('2d'), {
        type: 'line',
        data: {
            labels: [<?php foreach($trenRefill as $row) echo "\"$row->bulan\","; ?>],
            datasets: [{
                label: 'Total Refill',
                data: [<?php foreach($trenRefill as $row) echo "$row->total_refill,"; ?>],
                borderColor: '#28a745',
                fill: false
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    $('#tableStokKritis, #tableRefillYesterday').DataTable();
});
</script>
