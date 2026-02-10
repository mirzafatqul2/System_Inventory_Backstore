<div class="col-12">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><?= $subtitle ?></h3>
            <div class="card-tools">
                <button id="btnExportBrownbox" class="btn btn-success">
    <i class="fa fa-file-excel"></i> Export Brownbox Tidak Update
</button>
                <button id="btnImport" class="btn btn-success" data-toggle="modal" data-target="#modalImport">
                    <i class="fa fa-file-excel"></i> Import Excel
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped server-side-datatable" data-url="<?= site_url('databarang/list_barang/ajax_list') ?>">
                <thead>
                    <tr>
                        <th width="10px">No</th>
                        <th>SKU</th>
                        <th>Description</th>
                        <th>Number Rack</th>
                        <th>Brownbox</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Long SKU</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>SKU</th>
                        <th>Description</th>
                        <th>Number Rack</th>
                        <th>Brownbox</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Long SKU</th>
                        <th>Remark</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="importExcelForm" action="<?= site_url('databarang/list_barang/importExcel') ?>" method="post" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title" id="modalImportLabel">Import Data Excel</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="fileExcel">Pilih File Excel (.xls / .xlsx)</label>
                        <input type="file" id="file_excel" name="file_excel" class="form-control" accept=".xls,.xlsx" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnImportExcel"><i class="fas fa-upload"></i> Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.server-side-datatable').each(function() {
        var urlAjax = $(this).data('url');
        $(this).DataTable({
            processing: true,
            serverSide: true,
            order: [],
            ajax: {
                url: urlAjax,
                type: "POST"
            },
            columns: [
                { data: 'no' },
                { data: 'sku' },
                { data: 'description' },
                { data: 'numb_rack' },
                { data: 'brownbox' },
                { data: 'price' },
                { data: 'qty' },
                { data: 'long_sku' },
                { data: 'remark' }
            ],
            columnDefs: [
                { targets: [0], orderable: false }
            ],
        });
    });

    $('#btnImportExcel').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah kamu yakin ingin mengimport file Excel ini?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Import!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#importExcelForm').submit();
            }
        });
    });
});
$('#btnExportBrownbox').click(function(e) {
    e.preventDefault();

    $.ajax({
        url: "<?= base_url('databarang/list_barang/checkBrownboxData') ?>",
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
                    window.location.href = "<?= base_url('databarang/list_barang/exportExcel') ?>";
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
