<div class="container-fluid">
<div class="row">
  <!-- KPI BOXES -->
  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box bg-danger">
      <span class="info-box-icon"><i class="fas fa-minus-circle"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Short (Variance Minus)</span>
        <span class="info-box-number">Rp <?= number_format($totalShort,0,',','.') ?></span>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box bg-success">
      <span class="info-box-icon"><i class="fas fa-plus-circle"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Extra (Variance Plus)</span>
        <span class="info-box-number">Rp <?= number_format($totalExtra,0,',','.') ?></span>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box bg-warning">
      <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Missing (Damage)</span>
        <span class="info-box-number">Rp <?= number_format($totalMissing,0,',','.') ?></span>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box bg-primary">
      <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Net Loss</span>
        <span class="info-box-number">Rp <?= number_format($netLoss,0,',','.') ?></span>
      </div>
    </div>
  </div>
</div>

<!-- CHARTS & BREAKDOWN -->
<div class="row">
  <div class="col-md-6">
    <div class="card card-success">
      <div class="card-header">
        <h3 class="card-title">Trend Bulanan Short / Extra / Missing</h3>
      </div>
      <div class="card-body">
        <canvas id="trendKPI" style="min-height:250px;height:250px;max-height:250px;"></canvas>
      </div>
    </div>
  </div>

  <div class="col-md-6">
  <div class="card card-warning" style="min-height:335px;height:335px;max-height:335px;">
    <div class="card-header"><h3 class="card-title">Breakdown Assignment (Short & Extra)</h3></div>
    <div class="card-body table-responsive p-0">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Assignment</th>
            <th>Total Short</th>
            <th>Total Extra</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($lossPerAssignment as $la): ?>
            <tr>
              <td><?= htmlspecialchars($la->assignment) ?></td>
              <td>Rp <?= number_format($la->total_short,0,',','.') ?></td>
              <td>Rp <?= number_format($la->total_extra,0,',','.') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</div>

<!-- PIE & BREAKDOWN -->
<div class="row">
  <div class="col-md-6">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Distribusi Kategori Damage (DDR / DMO / DMC)</h3>
      </div>
      <div class="card-body">
        <canvas id="pieKPI" style="min-height:250px;height:250px;max-height:250px;"></canvas>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card card-danger" style="min-height:335px;height:335px;max-height:335px;">
      <div class="card-header"><h3 class="card-title">Breakdown Kategori Missing (Damage)</h3></div>
      <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-striped">
          <thead><tr><th>Kategori</th><th>Total Loss</th></tr></thead>
          <tbody>
            <?php foreach($lossPerKategoriDamage as $kd): ?>
              <tr>
                <td><?= htmlspecialchars($kd->kategory_damage) ?></td>
                <td>Rp <?= number_format($kd->total_loss,0,',','.') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- DETAIL TABLES -->
<div class="row">
  <div class="col-md-6">
    <div class="card card-info">
      <div class="card-header"><h3 class="card-title">Detail Stock Checklist</h3></div>
      <div class="card-body table-responsive p-0">
        <table class="table table-striped table-hover">
          <thead><tr><th>Assignment</th><th>SKU</th><th>Variance</th><th>Created At</th></tr></thead>
          <tbody>
            <?php foreach($recentSC as $r): ?>
              <tr>
                <td><?= htmlspecialchars($r->assignment) ?></td>
                <td><?= htmlspecialchars($r->sku) ?></td>
                <td><?= number_format($r->variance) ?></td>
                <td><?= date('d-m-Y H:i', strtotime($r->created_at)) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card card-danger">
      <div class="card-header"><h3 class="card-title">Detail Damage</h3></div>
      <div class="card-body table-responsive p-0">
        <table class="table table-striped table-hover">
          <thead><tr><th>Barcode</th><th>Qty</th><th>Amount</th><th>Created At</th></tr></thead>
          <tbody>
            <?php foreach($recentDamage as $d): ?>
              <tr>
                <td><?= htmlspecialchars($d->sku) ?></td>
                <td><?= number_format($d->qty_damage) ?></td>
                <td>Rp <?= number_format($d->amount_damage,0,',','.') ?></td>
                <td><?= date('d-m-Y H:i', strtotime($d->created_at)) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
            </div>
<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const damageData = <?= json_encode($lossPerKategoriDamage ?? []) ?>;
const damageLabels = damageData.map(item => item.kategory_damage);
const damageValues = damageData.map(item => parseFloat(item.total_loss));

const ctxPie = document.getElementById('pieKPI').getContext('2d');
new Chart(ctxPie, {
  type: 'pie',
  data: {
    labels: damageLabels,
    datasets: [{
      data: damageValues,
      backgroundColor: ['#e74c3c', '#f39c12', '#3498db', '#1abc9c', '#9b59b6', '#2ecc71'],
    }]
  },
  options: {
    responsive: true,
    plugins: {
      tooltip: {
        callbacks: {
          label: function(ctx) {
            return ctx.label + ': Rp ' + ctx.parsed.toLocaleString();
          }
        }
      },
      legend: { position: 'bottom' },
      title: { display: true, text: 'Total Kerugian per Kategori Damage' }
    }
  }
});

const trendData = <?= json_encode($trendShortExtraMissing) ?>;
const bulanLabelsRaw = trendData.map(item => item.bulan);

// Mapping bulan 01-12 ke nama Indonesia
const monthNames = [
  "Januari", "Februari", "Maret", "April", "Mei", "Juni",
  "Juli", "Agustus", "September", "Oktober", "November", "Desember"
];

// Ubah label bulan misal 2025-01 → Januari 2025
const bulanLabels = bulanLabelsRaw.map(bulanStr => {
  const [year, month] = bulanStr.split("-");
  return monthNames[parseInt(month) - 1] + " " + year;
});

const shortValues = trendData.map(item => parseFloat(item.short_total));
const extraValues = trendData.map(item => parseFloat(item.extra_total));
const missingValues = trendData.map(item => parseFloat(item.missing_total));

const ctxLine = document.getElementById('trendKPI').getContext('2d');
new Chart(ctxLine, {
  type: 'line',
  data: {
    labels: bulanLabels,
    datasets: [
      {
        label: 'Short',
        data: shortValues,
        borderColor: '#e74c3c',
        fill: false,
        tension: 0.4
      },
      {
        label: 'Extra',
        data: extraValues,
        borderColor: '#2ecc71',
        fill: false,
        tension: 0.4
      },
      {
        label: 'Missing',
        data: missingValues,
        borderColor: '#f39c12',
        fill: false,
        tension: 0.4
      }
    ]
  },
  options: {
    responsive: true,
    plugins: {
      tooltip: {
        callbacks: {
          label: function(ctx) {
            return ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString();
          }
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        title: { display: true, text: 'Nilai Kerugian (Rp)' }
      },
      x: {
        title: { display: true, text: 'Bulan' }
      }
    }
  }
});

</script>
