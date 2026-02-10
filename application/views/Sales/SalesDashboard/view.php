<div class="container-fluid">
  <!-- KPI Info Boxes -->
  <div class="row">
    <div class="col-md-3">
      <div class="info-box bg-primary">
        <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">MTD Sales</span>
          <span class="info-box-number" id="mtdSales">Rp 0</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="info-box bg-success">
        <span class="info-box-icon"><i class="fas fa-bullseye"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">MTD Target</span>
          <span class="info-box-number" id="mtdTarget">Rp 0</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="info-box bg-info">
        <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Achievement</span>
          <span class="info-box-number" id="achievement">0%</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="info-box bg-warning">
        <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Growth vs Last Month</span>
          <span class="info-box-number" id="growth">0%</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Monthly Recap Charts -->
  <div class="row">
    <!-- Monthly Sales Chart -->
    <div class="col-md-6">
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">Monthly Sales: This Year vs Last Year</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <canvas id="monthlySalesChart" style="min-height:250px; height:250px; max-height:250px; max-width:100%;"></canvas>
        </div>
      </div>
    </div>

    <!-- Daily Sales Chart -->
    <div class="col-md-6">
      <div class="card card-info">
        <div class="card-header">
          <h3 class="card-title">Daily Sales vs Target</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <canvas id="dailySalesChart" style="min-height:250px; height:250px; max-height:250px; max-width:100%;"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Growth KPI Footer -->
  <div class="card">
    <div class="card-footer">
      <div class="row">
        <div class="col-sm-3 col-6">
          <div class="description-block border-right text-center">
            <span class="description-percentage text-success" id="growthSalesIcon"><i class="fas fa-caret-up"></i></span>
            <h5 class="description-header" id="growthSalesValue">0%</h5>
            <span class="description-text">GROWTH SALES</span>
          </div>
        </div>
        <div class="col-sm-3 col-6">
          <div class="description-block border-right text-center">
            <span class="description-percentage text-success" id="growthUptIcon"><i class="fas fa-caret-up"></i></span>
            <h5 class="description-header" id="growthUptValue">0%</h5>
            <span class="description-text">GROWTH UPT</span>
          </div>
        </div>
        <div class="col-sm-3 col-6">
          <div class="description-block border-right text-center">
            <span class="description-percentage text-success" id="growthAtvIcon"><i class="fas fa-caret-up"></i></span>
            <h5 class="description-header" id="growthAtvValue">0%</h5>
            <span class="description-text">GROWTH ATV</span>
          </div>
        </div>
        <div class="col-sm-3 col-6">
          <div class="description-block text-center">
            <span class="description-percentage text-success" id="growthScrIcon"><i class="fas fa-caret-up"></i></span>
            <h5 class="description-header" id="growthScrValue">0%</h5>
            <span class="description-text">GROWTH SCR</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Script Section -->
<script>
const base_url = "<?= base_url(); ?>";

$(document).ready(function() {
  // Load KPI MTD data
  $.getJSON(base_url + 'SalesDashboard/get_mtd_data', function(data) {
    $('#mtdSales').text(formatRupiah(data.mtd_sales));
    $('#mtdTarget').text(formatRupiah(data.mtd_target));
    $('#achievement').text(data.achievement + '%');
    $('#growth').text(data.growth + '%');
    $('#achievement').css('color', data.achievement < 80 ? 'red' : 'green');
    $('#growth').css('color', data.growth < 0 ? 'red' : 'green');
  });

  // Load Growth Metrics
  $.getJSON(base_url + 'SalesDashboard/get_kpi_data', function(data) {
    setGrowth('#growthSalesIcon', '#growthSalesValue', data.growth_sales);
    setGrowth('#growthUptIcon', '#growthUptValue', data.growth_upt);
    setGrowth('#growthAtvIcon', '#growthAtvValue', data.growth_atv);
    setGrowth('#growthScrIcon', '#growthScrValue', data.growth_scr);
  });

  // Monthly Chart
  $.getJSON(base_url + 'SalesDashboard/get_monthly_sales_chart_data', function(data) {
    new Chart($('#monthlySalesChart'), {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [
          {
            label: 'This Year',
            data: data.this_year,
            borderColor: 'rgba(60,141,188,0.8)',
            backgroundColor: 'rgba(60,141,188,0.2)',
            fill: true
          },
          {
            label: 'Last Year',
            data: data.last_year,
            borderColor: 'rgba(210,214,222,1)',
            backgroundColor: 'rgba(210,214,222,0.2)',
            fill: true
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  });

  // Daily Chart
  $.getJSON(base_url + 'SalesDashboard/get_daily_sales_chart_data', function(data) {
    new Chart($('#dailySalesChart'), {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [
          {
            label: 'Target',
            data: data.targets,
            borderColor: '#28a745',
            fill: false
          },
          {
            label: 'Achievement',
            data: data.achievements,
            borderColor: '#007bff',
            fill: false
          },
          {
            label: 'Last Year',
            data: data.last_year,
            borderColor: '#ffc107',
            fill: false
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });
  });
});

// Format Rupiah
function formatRupiah(angka) {
  if (!angka) return 'Rp 0';
  angka = Math.round(angka);
  var number_string = angka.toString().replace(/[^,\d]/g, ''),
      sisa = number_string.length % 3,
      rupiah = number_string.substr(0, sisa),
      ribuan = number_string.substr(sisa).match(/\d{3}/g);
  if (ribuan) {
    var separator = sisa ? '.' : '';
    rupiah += separator + ribuan.join('.');
  }
  return 'Rp ' + rupiah;
}

// Growth KPI Icon + Value
function setGrowth(iconSelector, valueSelector, growth) {
  const icon = $(iconSelector + ' i');
  if (growth < 0) {
    icon.attr('class', 'fas fa-caret-down').parent().removeClass('text-success').addClass('text-danger');
  } else if (growth === 0) {
    icon.attr('class', 'fas fa-caret-left').parent().removeClass('text-success text-danger').addClass('text-warning');
  } else {
    icon.attr('class', 'fas fa-caret-up').parent().removeClass('text-danger text-warning').addClass('text-success');
  }
  $(valueSelector).text(growth.toFixed(2) + '%');
}
</script>
