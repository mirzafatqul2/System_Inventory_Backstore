<div class="container-fluid">
    <section class="content">
        <!-- FILTER PERIODE -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter"></i> Filter Periode</h3>
            </div>
            <div class="card-body">
                <form method="get" action="<?= base_url('pettycash/dashboard_pettycash') ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="start_date" value="<?= $start_date ?>" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>Tanggal Selesai</label>
                            <input type="date" name="end_date" value="<?= $end_date ?>" class="form-control" required>
                        </div>
                        <div class="col-md-3 align-self-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- PIE CHART -->
            <div class="col-md-6">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Penggunaan Per COA (Periode Terfilter)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="pieChart" style="min-height:250px;height:250px;max-height:250px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- BAR CHART -->
            <div class="col-md-6">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Total Penggunaan Per Bulan (Januari-Desember)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="barChart" style="min-height:250px;height:250px;max-height:250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card card-secondary">
            <div class="card-header"><h3 class="card-title">Detail Penggunaan Petty Cash (Periode Terfilter)</h3></div>
            <div class="card-body">
                <table id="table-detail" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>COA</th>
                            <th>Keterangan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const bulanLabels = <?= json_encode($bulan_labels) ?>;
const bulanData   = <?= json_encode($bulan_data) ?>;

new Chart(document.getElementById('barChart').getContext('2d'), {
    type: 'bar',
    data: { labels: bulanLabels, datasets: [{ label:'Total Pengeluaran', data: bulanData, backgroundColor:'rgba(40,167,69,0.7)' }] },
    options: { responsive:true, plugins:{ legend:{ display:true } }, scales:{ y:{ beginAtZero:true, ticks:{ callback: val=>'Rp '+val.toLocaleString() } } }}
});

const coaLabels = <?= json_encode(array_map(fn($r) => $r->coa, $per_coa)) ?>;
const coaData   = <?= json_encode(array_map(fn($r) => (float)$r->total_amount, $per_coa)) ?>;

new Chart(document.getElementById('pieChart').getContext('2d'), {
    type: 'pie',
    data: { labels: coaLabels, datasets: [{ data: coaData, backgroundColor: coaLabels.map((_,i)=>`hsl(${i*30},70%,60%)`) }] },
    options: { responsive:true, plugins:{ legend:{ position:'bottom' }} }
});

$(document).ready(function(){
    $('#table-detail').DataTable({
        processing: true, serverSide: true,
        ajax: {
            url: '<?= base_url('pettycashdashboard/ajax_list') ?>',
            type: 'POST',
            data: { start_date: '<?= $start_date ?>', end_date: '<?= $end_date ?>' }
        },
        columnDefs: [
                { targets: [0], orderable: false }
            ]
    });
});
</script>
