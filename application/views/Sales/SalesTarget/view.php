<div class="col-12">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><?= $subtitle ?></h3>
            <div class="card-tools">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-default">
                    Tambah Sales Target
                </button>
                <button id="btnExport" class="btn btn-success">
                    <i class="fa fa-file-excel"> </i> Export Excel
                </button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="10px">No</th>
                        <th>Bulan</th>
                        <th>Base</th>
                        <th>Level 1</th>
                        <th>Level 2</th>
                        <th>Level 3</th>
                        <th>Level 4</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($sales_target as $salesTarget):
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= format_bulan($salesTarget->bulan) ?></td>
                            <td><?= rupiah($salesTarget->base_target) ?></td>
                            <td><?= rupiah($salesTarget->level1_target) ?></td>
                            <td><?= rupiah($salesTarget->level2_target) ?></td>
                            <td><?= rupiah($salesTarget->level3_target) ?></td>
                            <td><?= rupiah($salesTarget->level4_target)     ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-edit" data-id="<?= $salesTarget->id ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-delete" data-id="<?= $salesTarget->id ?>">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th width="10px">No</th>
                        <th>Bulan</th>
                        <th>Base</th>
                        <th>Level 1</th>
                        <th>Level 2</th>
                        <th>Level 3</th>
                        <th>Level 4</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
</div>
<!-- /.card -->

<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title">Tambah Sales Target</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="salesTargetForm" action="<?= site_url('datasales/sales_target/addData') ?>" method="post">
                    <div class="form-group">
                        <label for="bulan">Bulan</label>
                        <input type="month" name="bulan" id="bulan" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="base_display">Base Sales Target</label>
                        <input type="text" id="base_display" class="form-control" placeholder="Rp 0" required>
                        <input type="hidden" name="base" id="base">
                    </div>

                    <div class="form-group">
                        <label for="level1_display">Level 1</label>
                        <input type="text" id="level1_display" class="form-control" placeholder="Rp 0" readonly>
                        <input type="hidden" name="level1" id="level1">
                    </div>

                    <div class="form-group">
                        <label for="level2_display">Level 2</label>
                        <input type="text" id="level2_display" class="form-control" placeholder="Rp 0" readonly>
                        <input type="hidden" name="level2" id="level2">
                    </div>

                    <div class="form-group">
                        <label for="level3_display">Level 3</label>
                        <input type="text" id="level3_display" class="form-control" placeholder="Rp 0" readonly>
                        <input type="hidden" name="level3" id="level3">
                    </div>

                    <div class="form-group">
                        <label for="level4_display">Level 4</label>
                        <input type="text" id="level4_display" class="form-control" placeholder="Rp 0" readonly>
                        <input type="hidden" name="level4" id="level4">
                    </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="btnAddSaveChanges">Save changes</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<div class="modal fade" id="modal-edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editSalesTargetForm" action="<?= site_url('datasales/sales_target/updateData') ?>" method="post">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title">Edit Sales Target</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group">
                        <label for="edit_bulan">Bulan</label>
                        <input type="month" name="bulan" id="edit_bulan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_base_display">Base Sales Target</label>
                        <input type="text" id="edit_base_display" class="form-control" placeholder="Rp 0" required>
                        <input type="hidden" name="base" id="edit_base">
                    </div>

                    <div class="form-group">
                        <label for="edit_level1_display">Level 1</label>
                        <input type="text" id="edit_level1_display" class="form-control" placeholder="Rp 0" readonly>
                        <input type="hidden" name="level1" id="edit_level1">
                    </div>

                    <div class="form-group">
                        <label for="edit_level2_display">Level 2</label>
                        <input type="text" id="edit_level2_display" class="form-control" placeholder="Rp 0" readonly>
                        <input type="hidden" name="level2" id="edit_level2">
                    </div>

                    <div class="form-group">
                        <label for="edit_level3_display">Level 3</label>
                        <input type="text" id="edit_level3_display" class="form-control" placeholder="Rp 0" readonly>
                        <input type="hidden" name="level3" id="edit_level3">
                    </div>

                    <div class="form-group">
                        <label for="edit_level4_display">Level 4</label>
                        <input type="text" id="edit_level4_display" class="form-control" placeholder="Rp 0" readonly>
                        <input type="hidden" name="level4" id="edit_level4">
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveChanges">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix === undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }

    $('#base_display').on('input', function() {
        const rawInput = this.value;
        const formatted = formatRupiah(rawInput, 'Rp ');
        $(this).val(formatted);

        const cleanNumber = formatted.replace(/[^,\d]/g, '').replace(',', '.');
        $('#base').val(cleanNumber);

        const base = parseFloat(cleanNumber);
        if (!isNaN(base)) {
            const level1 = Math.round(base * 1.1);
            const level2 = Math.round(base * 1.2);
            const level3 = Math.round(base * 1.3);
            const level4 = Math.round(base * 1.4);

            $('#level1').val(level1);
            $('#level2').val(level2);
            $('#level3').val(level3);
            $('#level4').val(level4);

            $('#level1_display').val(formatRupiah(level1.toString(), 'Rp '));
            $('#level2_display').val(formatRupiah(level2.toString(), 'Rp '));
            $('#level3_display').val(formatRupiah(level3.toString(), 'Rp '));
            $('#level4_display').val(formatRupiah(level4.toString(), 'Rp '));
        } else {
            $('#level1, #level2, #level3, #level4').val('');
            $('#level1_display, #level2_display, #level3_display, #level4_display').val('');
        }

        $('#btnAddSaveChanges').on('click', function(e) {
            e.preventDefault();

            const bulan = $('#bulan').val();
            if (!bulan) {
                Swal.fire('Error', 'Silakan pilih bulan terlebih dahulu.', 'warning');
                return;
            }

            const baseValidation = $('#base').val();
            if (!baseValidation) {
                Swal.fire('Error', 'Base Target Harus di Isi!', 'warning');
                return;
            }

            const bulanFormatted = new Date(bulan).toLocaleString('id-ID', {
                month: 'long',
                year: 'numeric'
            });

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Kamu Yakin Ingin Menambahkan Data Sales Target di Bulan ${bulanFormatted}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tambah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#salesTargetForm').submit();
                }
            });
        });

    });

    $(document).ready(function() {
        $('.btn-edit').click(function() {
            const id = $(this).data('id');

            $.ajax({
                url: '<?= site_url('datasales/sales_target/getDataById') ?>',
                type: 'GET',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_bulan').val(data.bulan);

                    const base = parseFloat(data.base_target);
                    const level1 = Math.round(base * 1.1);
                    const level2 = Math.round(base * 1.2);
                    const level3 = Math.round(base * 1.3);
                    const level4 = Math.round(base * 1.4);

                    // Set nilai numerik (untuk input hidden)
                    $('#edit_base').val(base);
                    $('#edit_level1').val(level1);
                    $('#edit_level2').val(level2);
                    $('#edit_level3').val(level3);
                    $('#edit_level4').val(level4);

                    // Set tampilan (readonly dalam Rupiah)
                    $('#edit_base_display').val(formatRupiah(base.toString(), 'Rp '));
                    $('#edit_level1_display').val(formatRupiah(level1.toString(), 'Rp '));
                    $('#edit_level2_display').val(formatRupiah(level2.toString(), 'Rp '));
                    $('#edit_level3_display').val(formatRupiah(level3.toString(), 'Rp '));
                    $('#edit_level4_display').val(formatRupiah(level4.toString(), 'Rp '));

                    $('#modal-edit').modal('show');
                },
                error: function() {
                    Swal.fire('Gagal', 'Data Tidak Ditemukan', 'error');
                }
            });
        });
        $('#edit_base_display').on('input', function() {
            const rawInput = this.value;
            const formatted = formatRupiah(rawInput, 'Rp ');
            $(this).val(formatted);

            const cleanNumber = formatted.replace(/[^,\d]/g, '').replace(',', '.');
            $('#edit_base').val(cleanNumber);

            const base = parseFloat(cleanNumber);
            if (!isNaN(base)) {
                const level1 = Math.round(base * 1.1);
                const level2 = Math.round(base * 1.2);
                const level3 = Math.round(base * 1.3);
                const level4 = Math.round(base * 1.4);

                $('#edit_level1').val(level1);
                $('#edit_level2').val(level2);
                $('#edit_level3').val(level3);
                $('#edit_level4').val(level4);

                $('#edit_level1_display').val(formatRupiah(level1.toString(), 'Rp '));
                $('#edit_level2_display').val(formatRupiah(level2.toString(), 'Rp '));
                $('#edit_level3_display').val(formatRupiah(level3.toString(), 'Rp '));
                $('#edit_level4_display').val(formatRupiah(level4.toString(), 'Rp '));
            } else {
                $('#level1, #level2, #level3, #level4').val('');
                $('#level1_display, #level2_display, #level3_display, #level4_display').val('');
            }
        });
        $('#btnSaveChanges').on('click', function(e) {
            e.preventDefault();

            const bulan = $('#edit_bulan').val();
            const bulanFormatted = new Date(bulan).toLocaleString('id-ID', {
                month: 'long',
                year: 'numeric'
            });

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Kamu Yakin Ingin Mengubah Data Sales Target di Bulan ${bulanFormatted}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#editSalesTargetForm').submit();
                }
            });
        });
    });
    $(document).ready(function() {
        $('.btn-delete').click(function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: 'Data tidak bisa dikembalikan setelah dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('datasales/sales_target/deleteData/') ?>" + id;
                }
            });
        });
    });
    $('#btnExport').click(function(e) {
        e.preventDefault();

        Swal.fire({
            icon: 'success',
            title: 'File Sedang Diproses...',
            text: 'Export Excel Dalam Proses!',
            showConfirmButton: false,
            timer: 1500
        });

        setTimeout(() => {
            window.location.href = "<?= base_url('datasales/sales_target/exportExcel') ?>"
        }, 1500);
    })
</script>