<div class="col-12">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><?= $subtitle ?></h3>
            <div class="card-tools">
                <!-- ✅ Tombol input sales -->
                <button id="btnInput" class="btn btn-default" data-toggle="modal" data-target="#modal-input">
                    <i class="fas fa-plus-circle"></i> Input Sales
                </button>
                <!-- ✅ Tombol export excel -->
                <button id="btnExportDaily" class="btn btn-success">
                    <i class="fa fa-file-excel"></i> Export Excel
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Sales Target</th>
                        <th>Sales Achievement</th>
                        <th>% Achieve</th>
                        <th>UPT</th>
                        <th>ATV</th>
                        <th>SCR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach($dailyAchievements as $daily): ?>
                        <?php
                        $achive = ($daily->daily_target > 0) ? round(($daily->daily_sales / $daily->daily_target) * 100, 2) : 0;
                        $upt = ($daily->transaction > 0) ? number_format(($daily->qty_sold / $daily->transaction), 2, ',', '.') : 0;
                        $atv = ($daily->transaction > 0) ? $daily->daily_sales / $daily->transaction : 0;
                        $scr = ($daily->traffic > 0) ? number_format(($daily->transaction / $daily->traffic * 100), 2, ',', '.') : 0;
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $daily->tanggal ?></td>
                            <td><?= rupiah($daily->daily_target) ?></td>
                            <td><?= rupiah($daily->daily_sales) ?></td>
                            <td>
                                <?php if ($achive >= 100): ?>
                                    <span class="badge badge-success"><?= $achive ?>%</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><?= $achive ?>%</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $upt ?></td>
                            <td><?= rupiah($atv) ?></td>
                            <td><?= $scr ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Sales Target</th>
                        <th>Sales Achievement</th>
                        <th>% Achieve</th>
                        <th>UPT</th>
                        <th>ATV</th>
                        <th>SCR</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- ✅ Modal Input Sales -->
<div class="modal fade" id="modal-input">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title">Input Daily Sales</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="dailySalesForm" action="<?= site_url('datasales/sales_achievement/inputDailySales') ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Total Traffic</label>
                        <input type="number" name="traffic" id="traffic" class="form-control" required>
                    </div>
                    <div class="row">
                        <!-- Komputer 1 -->
                        <div class="col-md-6">
                            <h5 class="text-center font-weight-bold">Komputer 1</h5>
                            <div class="form-group">
                                <label>Daily Sales</label>
                                <input type="text" id="sales1_display" class="form-control" placeholder="Rp 0" required>
                                <input type="hidden" name="sales_1" id="sales1">
                            </div>
                            <div class="form-group">
                                <label>Total Transaksi</label>
                                <input type="number" name="transaksi_1" id="transaksi_1" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Qty Terjual</label>
                                <input type="number" name="qty_sold_1" id="qty_sold_1" class="form-control" required>
                            </div>
                        </div>
                        <!-- Komputer 2 -->
                        <div class="col-md-6">
                            <h5 class="text-center font-weight-bold">Komputer 2</h5>
                            <div class="form-group">
                                <label>Daily Sales</label>
                                <input type="text" id="sales2_display" class="form-control" placeholder="Rp 0" required>
                                <input type="hidden" name="sales_2" id="sales2">
                            </div>
                            <div class="form-group">
                                <label>Total Transaksi</label>
                                <input type="number" name="transaksi_2" id="transaksi_2" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Qty Terjual</label>
                                <input type="number" name="qty_sold_2" id="qty_sold_2" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnInputSales">Input Sales</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Format Rupiah
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }
    $('#sales1_display').on('input', function() {
        const rawInput = this.value;
        const formatted = formatRupiah(rawInput, 'Rp ');
        $(this).val(formatted);
        const cleanNumber = formatted.replace(/[^,\d]/g, '').replace(',', '.');
        $('#sales1').val(cleanNumber);
    });
    $('#sales2_display').on('input', function() {
        const rawInput = this.value;
        const formatted = formatRupiah(rawInput, 'Rp ');
        $(this).val(formatted);
        const cleanNumber = formatted.replace(/[^,\d]/g, '').replace(',', '.');
        $('#sales2').val(cleanNumber);
    });

    // Submit input sales
    $('#btnInputSales').on('click', function(e) {
    e.preventDefault();

    // ambil semua field
    const tanggal = $('#tanggal').val();
    const sales1 = $('#sales1').val();
    const transaksi_1 = $('#transaksi_1').val();
    const qty_sold_1 = $('#qty_sold_1').val();
    const sales2 = $('#sales2').val();
    const transaksi_2 = $('#transaksi_2').val();
    const qty_sold_2 = $('#qty_sold_2').val();
    const traffic = $('#traffic').val();

    // validasi kosong
    if (!tanggal || !sales1 || !transaksi_1 || !qty_sold_1 || !sales2 || !transaksi_2 || !qty_sold_2 || !traffic) {
        Swal.fire('Error', 'Semua field wajib diisi!', 'warning');
        return;
    }

    // 🔥 cek tanggal dulu lewat AJAX
    $.ajax({
        url: "<?= base_url('datasales/sales_achievement/checkTanggal') ?>",
        type: "POST",
        data: { tanggal: tanggal },
        dataType: "json",
        success: function(res) {
            if (res.status === 'exists') {
                // kalau sudah ada data, konfirmasi dulu
                Swal.fire({
                    title: 'Konfirmasi',
                    text: res.message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#dailySalesForm').submit();
                    }
                });
            } else {
                // belum ada data, langsung konfirmasi input
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Yakin ingin menambahkan data sales ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#dailySalesForm').submit();
                    }
                });
            }
        },
        error: function() {
            Swal.fire('Error', 'Terjadi kesalahan saat memeriksa tanggal.', 'error');
        }
    });
});


    // ✅ Export Excel dengan cek dulu via AJAX
    $('#btnExportDaily').click(function(e) {
        e.preventDefault();
        $.ajax({
            url: "<?= base_url('datasales/sales_achievement/checkDailyData/'.$bulan) ?>",
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Export',
                        text: res.message
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'File Sedang Diproses...',
                        text: res.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                    setTimeout(() => {
                        window.location.href = "<?= base_url('datasales/sales_achievement/exportDailyExcel/'.$bulan) ?>";
                    }, 2000);
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Terjadi kesalahan saat memeriksa data.'
                });
            }
        });
    });
</script>
