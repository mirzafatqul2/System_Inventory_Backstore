<div class="col-12">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><?= $subtitle ?></h3>
            <div class="card-tools">
                <button id="btnExport" class="btn btn-success">
                    <i class="fa fa-file-excel"> </i> Export Excel
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bulan</th>
                        <th>Sales Target</th>
                        <th>Sales Achievement</th>
                        <th>% Achieve</th>
                        <th>UPT</th>
                        <th>ATV</th>
                        <th>SCR</th>
                        <th>Performance Status</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($salesAchievements as $summary): ?>
                        <?php 
                        $achievePercent = ($summary->target_bulanan > 0) ? round(($summary->total_sales / $summary->target_bulanan) * 100, 2) : 0;
                        ?>
                        <tr class="text-center">
                            <td><?= $no++ ?></td>
                            <td><?= format_bulan($summary->bulan) ?></td>
                            <td><?= rupiah($summary->target_bulanan) ?></td>
                            <td><?= rupiah($summary->total_sales) ?></td>
                            <td>
                                <?php if ($achievePercent >= 100): ?>
                                    <span class="badge badge-success"><?= $achievePercent ?>%</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><?= $achievePercent ?>%</span>
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($summary->upt, 2, ',', '.') ?></td>
                            <td><?= rupiah($summary->atv) ?></td>
                            <td><?= number_format($summary->scr * 100, 2, ',', '.') ?>%</td>
                            <td>
                                <?php if ($achievePercent >= 100): ?>
                                    <span class="badge badge-success">ACHIEVED</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">NOT ACHIEVED</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('datasales/sales_achievement/dailyAchievement/'.$summary->bulan) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Bulan</th>
                        <th>Sales Target</th>
                        <th>Sales Achievement</th>
                        <th>% Achieve</th>
                        <th>UPT</th>
                        <th>ATV</th>
                        <th>SCR</th>
                        <th>Performance Status</th>
                        <th>View</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
$('#btnExport').click(function(e) {
        e.preventDefault();

        Swal.fire({
            icon: 'success',
            title: 'File Sedang Diproses...',
            text: 'Export Excel Dalam Proses!',
            showConfirmButton: false,
            timer: 2500
        });

        setTimeout(() => {
            window.location.href = "<?= base_url('datasales/sales_achievement/exportExcel') ?>"
        }, 1500);
    })
</script>
