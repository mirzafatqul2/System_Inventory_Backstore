<div class="col-12">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><?= $subtitle ?></h3>
            <div class="card-tools">
                <button id="btnInput" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Input Refill
                </button>
                <button id="btnExport" class="btn btn-success ml-2">
                    <i class="fa fa-file-excel"></i> Export Excel
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped server-side-datatable" data-url="<?= site_url('databarang/data_refill/ajax_list') ?>">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Refill</th>
                        <th>Brownbox</th>
                        <th>SKU</th>
                        <th>Qty Refill</th>
                        <th>Refill By</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Refill</th>
                        <th>Brownbox</th>
                        <th>SKU</th>
                        <th>Qty Refill</th>
                        <th>Refill By</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal Input -->
<div class="modal fade" id="modal-input" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="formRefill" action="<?= site_url('databarang/RefillKeepstock/add') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-inputLabel">Input Refill</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Brownbox</label>
                        <input type="text" name="brownbox" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>SKU</label>
                        <input type="text" name="sku" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Qty Refill</label>
                        <input type="number" name="qty_refill" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Refill By</label>
                        <input type="text" name="refill_by" class="form-control" value="<?= $this->session->userdata('username') ?>" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    var tableRefill = $('.server-side-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: $('.server-side-datatable').data('url'),
            type: "POST"
        },
        order: [],
        columnDefs: [{ targets: [0], orderable: false }]
    });

    $('#btnInput').click(function() {
        $('#modal-input').modal('show');
    });
});

// Export Excel
$('#btnExport').click(function(e) {
    e.preventDefault();

    Swal.fire({
        icon: 'success',
        title: 'File Sedang Diproses...',
        text: 'Silakan tunggu sebentar.',
        showConfirmButton: false,
        timer: 1500
    });

    setTimeout(() => {
        window.location.href = "<?= site_url('databarang/data_refill/exportExcel') ?>";
    }, 1500);
});
</script>
