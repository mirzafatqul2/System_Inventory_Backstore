<div class="col-12">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><?= $subtitle ?></h3>
            <div class="card-tools">
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalImportExcel">
    <i class="fa fa-file-import"></i> Import Excel
</button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped server-side-summary" data-url="<?= site_url('Stockceklist/ajax_summary') ?>">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Assignment</th>
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
    <form action="<?= site_url('dataomnimbus/data_ceklist/importExcel') ?>" method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title">Import Data Kerusakan</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Pilih Kategori Damage</label>
            <select name="assignment" class="form-control" required>
              <option value="">-- Pilih --</option>
              <option value="Negative Balance Check">Negative Balance Check</option>
              <option value="Unsold Stock Balance Check">Unsold Stock Balance Check</option>
              <option value="Store Initiative (Barcode Check)">Store Initiative (Barcode Check)</option>
              <option value="Store Initiative (Missing Item Check)">Store Initiative (Missing Item Check)</option>
              <option value="Store Initiative (Weekly Balance Check)">Store Initiative (Weekly Balance Check)</option>
              <option value="Store Initiative (Balance Check by Gondola)">Store Initiative (Balance Check by Gondola)</option>
              <option value="VIP A">VIP A</option>
              <option value="VIP B">VIP B</option>
              <option value="VIP C">VIP C</option>
              <option value="VIP D">VIP D</option>
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
        var assignment = $(this).data('assignment');
        var tanggal = $(this).data('tanggal');
        var url = '<?= site_url('Stockceklist/detail') ?>?assignment=' + encodeURIComponent(assignment) + '&tanggal=' + encodeURIComponent(tanggal);
        window.open(url, '_blank');
    });
});
</script>
