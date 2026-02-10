<div class="col-12">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><?= $subtitle ?></h3>
            <div class="card-tools">
                <button id="btnAddData" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Tambah Data
                </button>
                <button id="btnExport" class="btn btn-success">
                    <i class="fa fa-file-excel"></i> Export Excel
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped server-side-datatable"
                   data-url="<?= site_url('dataomnimbus/datang_barang/ajax_list') ?>">
                <thead>
                    <tr>
                        <th width="10px">No</th>
                        <th>Tanggal</th>
                        <th>Surat Jalan</th>
                        <th>Ref No</th>
                        <th>Amount</th>
                        <th>SKU IB</th>
                        <th>IB Pending</th>
                        <th>Total CTN</th>
                        <th>Waktu Update</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Surat Jalan</th>
                        <th>Ref No</th>
                        <th>Amount</th>
                        <th>SKU IB</th>
                        <th>IB Pending</th>
                        <th>Total CTN</th>
                        <th>Waktu Update</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="modalFormLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formData" method="post" action="<?= site_url('dataomnimbus/datang_barang/saveData') ?>">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title" id="modalFormLabel">Tambah Data Barang Datang</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="id">
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
            </div>
            <div class="form-group">
                <label>Surat Jalan</label>
                <input type="text" class="form-control" id="surat_jalan" name="surat_jalan" required>
            </div>
            <div class="form-group">
                <label>Ref No</label>
                <input type="text" class="form-control" id="ref_no" name="ref_no" required>
            </div>
            <div class="form-group">
                <label>Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" required>
            </div>
            <div class="form-group">
                <label>SKU IB</label>
                <input type="number" class="form-control" id="sku_ib" name="sku_ib" required>
            </div>
            <div class="form-group">
                <label>IB PENDING</label>
                <input type="number" class="form-control" id="ib_pending" name="ib_pending" required>
            </div>
            <div class="form-group">
                <label>Total CTN</label>
                <input type="number" class="form-control" id="ctn" name="ctn" required>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
$(document).ready(function() {
    var table = $('.server-side-datatable').DataTable({
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: $('.server-side-datatable').data('url'),
            type: "POST",
        },
        columnDefs: [{ targets: [0,9], orderable: false }]
    });

    $('#btnAddData').click(function() {
        $('#modalFormLabel').text('Tambah Data Barang Datang');
        $('#formData')[0].reset();
        $('#id').val('');
        $('#modalForm').modal('show');
    });

    $(document).on('click', '.btnEdit', function() {
        var data = $(this).data();
        $('#modalFormLabel').text('Edit Data Barang Datang');
        $('#id').val(data.id);
        $('#tanggal').val(data.tanggal);
        $('#surat_jalan').val(data.surat_jalan);
        $('#ref_no').val(data.ref_no);
        $('#amount').val(data.amount);
        $('#sku_ib').val(data.sku_ib);
        $('#ib_pending').val(data.ib_pending);
        $('#ctn').val(data.ctn);
        $('#modalForm').modal('show');
    });

});
</script>

