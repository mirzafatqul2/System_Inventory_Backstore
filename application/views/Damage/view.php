<div class="col-12">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><?= $subtitle ?></h3>
            <div class="card-tools">
<!-- Tombol Import -->
<button class="btn btn-primary" data-toggle="modal" data-target="#modalImportExcel">
    <i class="fa fa-file-import"></i> Import Excel
</button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped server-side-summary" data-url="<?= site_url('damage/ajax_summary') ?>">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- Modal Import Excel -->
<div class="modal fade" id="modalImportExcel" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <form action="<?= site_url('dataomnimbus/data_damage/importExcel') ?>" method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title">Import Data Kerusakan</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Pilih Kategori Damage</label>
            <select name="kategory" class="form-control" required>
              <option value="">-- Pilih --</option>
              <option value="DMO">DMO (Damage Due to on Handling)</option>
              <option value="DMC">DMC (Damage Customer Return)</option>
              <option value="DMQ">DMQ (Damage Due to Quality)</option>
              <option value="DDR">DDR (Damage During Receiving)</option>
              <option value="DMP">DMP (Damage Due to Expire)</option>
              <option value="DPI">DPI (Damage Due to Promotion Item)</option>
            </select>
          </div>
          <div class="form-group">
            <label>File Excel</label>
            <input type="file" name="file_excel" class="form-control" required accept=".xls,.xlsx">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Import</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
$(document).ready(function() {
    var summaryTable = $('.server-side-summary').DataTable({
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: $('.server-side-summary').data('url'),
            type: "POST"
        },
        columnDefs: [
            { targets: [0, 3], orderable: false }
        ]
    });

    $('.server-side-summary').on('click', '.btn-view-detail', function() {
        var kategory = $(this).data('kategory');
        var tanggal = $(this).data('tanggal');
        var url = '<?= site_url('damage/detail') ?>?kategory=' + encodeURIComponent(kategory) + '&tanggal=' + encodeURIComponent(tanggal);
        window.open(url, '_blank');
    });
});
</script>
