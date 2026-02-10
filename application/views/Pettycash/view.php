  <div class="col-12">
      <div class="card card-warning">
          <div class="card-header">
              <h3 class="card-title"><?= $subtitle ?></h3>
              <div class="card-tools">
                  <button id="btnExport" class="btn btn-success">
                      <i class="fa fa-file-excel"></i> Export Excel
                  </button>
                  <button id="btnAddPettycash" class="btn btn-success">
                      <i class="fas fa-plus"></i> Tambah Penggunaan
                  </button>
              </div>
          </div>

          <div class="card-body">
              <table class="table table-bordered table-striped server-side-datatable" data-url="<?= site_url('pettycash/ajax_list') ?>">
                  <thead>
                      <tr>
                          <th width="10px">No</th>
                          <th>Tanggal Pengajuan</th>
                          <th>COA</th>
                          <th>Description COA</th>
                          <th>Keterangan</th>
                          <th>Amount</th>
                          <th>Status Claim</th>
                          <th>Tanggal Dibuat</th>
                          <th width="80px">Aksi</th>
                      </tr>
                  </thead>
                  <tfoot>
                      <tr>
                          <th>No</th><th>Tanggal Pengajuan</th><th>COA</th><th>Description COA</th><th>Keterangan</th><th>Amount</th><th>Status Claim</th><th>Tanggal Dibuat</th><th>Aksi</th>
                      </tr>
                  </tfoot>
              </table>
          </div>
      </div>
  </div>

  <!-- Modal Tambah -->
  <div class="modal fade" id="modalInput" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <!-- ✅ Form pakai POST biasa -->
      <form id="formAddPettycash" action="<?= site_url('pettycash/addData') ?>" method="post">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Claim Petty Cash</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Tanggal Pengajuan</label>
              <input type="date" name="date" class="form-control" required>
            </div>
            <div class="form-group">
              <label>COA</label>
              <select name="coa" class="form-control" required>
                <option value="">-- Pilih COA --</option>
                <?php foreach ($coa_list as $coa): ?>
                  <option value="<?= $coa->coa ?>"><?= $coa->coa ?> - <?= $coa->desc_coa ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Deskripsi Penggunaan</label>
              <input type="text" name="desc_use" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Amount</label>
              <input type="number" name="amount" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Edit -->
  <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <!-- ✅ Form pakai POST biasa -->
      <form id="formEditPettycash" action="<?= site_url('pettycash/updateData') ?>" method="post">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Claim Petty Cash</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Tanggal Pengajuan</label>
              <input type="date" name="date" id="edit_date" class="form-control" required>
            </div>
            <div class="form-group">
              <label>COA</label>
              <select name="coa" id="edit_coa" class="form-control" required>
                <option value="">-- Pilih COA --</option>
                <?php foreach ($coa_list as $coa): ?>
                  <option value="<?= $coa->coa ?>"><?= $coa->coa ?> - <?= $coa->desc_coa ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label>Deskripsi Penggunaan</label>
              <input type="text" name="desc_use" id="edit_desc_use" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Amount</label>
              <input type="number" name="amount" id="edit_amount" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    const site_url = "<?= site_url() ?>";
  $(document).ready(function() {
      var table = $('.server-side-datatable').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
              url: $('.server-side-datatable').data('url'),
              type: "POST"
          },
          order: [],
          columnDefs: [{ targets: [0, 8], orderable: false }]
      });

      $('#btnAddPettycash').click(function() {
          $('#modalInput').modal('show');
      });

      $(document).on('click', '.btn-edit', function() {
          var id = $(this).data('id');
          $.get('<?= site_url('pettycash/getById/') ?>' + id, function(res) {
              var data = JSON.parse(res);
              if (data.id) {
                  $('#edit_id').val(data.id);
                  $('#edit_date').val(data.date);
                  $('#edit_coa').val(data.coa);
                  $('#edit_desc_use').val(data.desc_use ?? '');
                  $('#edit_amount').val(data.amount);
                  $('#modalEdit').modal('show');
              } else {
                  Swal.fire('Error', data.message || 'Data tidak ditemukan', 'error');
              }
          });
      });

      $(document).on('click', '.btn-delete', function() {
          var id = $(this).data('id');
          Swal.fire({
          title: 'Yakin ingin menghapus?',
          text: "Data tidak dapat dikembalikan!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal'
      }).then((result) => {
          if (result.isConfirmed) {
              window.location.href = site_url + 'pettycash/pettycash/deleteData/' + id;
          }
      });
      });
    $(document).on('click', '.btn-update-status', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Apakah Anda yakin mengubah status?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, ubah status',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formStatus' + id).submit();
            }
        });
    });

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
              window.location.href = "<?= site_url('pettycash/export_excel') ?>";
          }, 1500);
      });
  });
  </script>
