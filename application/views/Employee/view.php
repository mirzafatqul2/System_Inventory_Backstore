<div class="col-12">
  <div class="card card-warning">
    <div class="card-header">
      <h3 class="card-title"><?= $subtitle ?></h3>
      <div class="card-tools">
        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalTambah">
          <i class="fa fa-user-plus"></i> Tambah Karyawan
        </button>
      </div>
    </div>
    <div class="card-body">
      <table id="tabel-karyawan" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th width='10px'>No</th>
            <th>Nama Karyawan</th>
            <th>NIK</th>
            <th>Jabatan</th>
            <th>Telepon</th>
            <th>Status</th>
            <th>Foto</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
          <tr>
            <th width='10px'>No</th>
            <th>Nama Karyawan</th>
            <th>NIK</th>
            <th>Jabatan</th>
            <th>Telepon</th>
            <th>Status</th>
            <th>Foto</th>
            <th>Action</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <form action="<?= base_url('employee/AddData') ?>" method="post" enctype="multipart/form-data" id="formTambah">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Karyawan</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>NIK</label>
            <input type="text" name="nik" id="nik" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Nama Karyawan</label>
            <input type="text" name="nama_karyawan" id="nama_karyawan" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Telepon</label>
            <input type="text" name="telepon" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Jabatan</label>
            <select name="jabatan" class="form-control" required>
              <option value="">-- Pilih Jabatan --</option>
              <option value="1">Supervisor</option>
              <option value="2">Assistant Supervisor</option>
              <option value="3">Store Boy</option>
              <option value="4">Kasir</option>
              <option value="5">Promotor</option>
            </select>
          </div>
          <div class="form-group">
            <label>Password (Auto)</label>
            <input type="text" name="password" id="password" class="form-control" readonly required>
          </div>
          <div class="form-group">
            <label>Foto</label>
            <input type="file" name="gambar" class="form-control-file">
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

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog">
    <form action="<?= base_url('employee/UpdateData') ?>" method="post" enctype="multipart/form-data" id="formEdit">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Karyawan</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_karyawan" id="edit_id">
          <div class="form-group">
            <label>Nama Karyawan</label>
            <input type="text" name="nama_karyawan" id="edit_nama" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Telepon</label>
            <input type="text" name="telepon" id="edit_telepon" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Jabatan</label>
            <select name="jabatan" id="edit_jabatan" class="form-control" required>
              <option value="1">Supervisor</option>
              <option value="2">Assistant Supervisor</option>
              <option value="3">Store Boy</option>
              <option value="4">Kasir</option>
              <option value="5">Promotor</option>
            </select>
          </div>
          <div class="form-group">
            <label>Ganti Password (Opsional)</label>
            <input type="text" name="password" id="edit_password" class="form-control">
          </div>
          <div class="form-group">
            <label>Foto (Opsional)</label>
            <input type="file" name="gambar" class="form-control-file">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#tabel-karyawan').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: '<?= base_url('employee/ajax_list') ?>',
      type: 'POST',
      error: function(xhr, status, error) {
        console.error('AJAX Error:', xhr.responseText);
        alert('Gagal mengambil data dari server!');
      }
    },
    columnDefs: [
      { targets: [0, 7], orderable: false }
    ]
  });

  // Fungsi generate password minimal 8 karakter
  function padToMinLength(str, minLen) {
    const pad = "123456789";
    while (str.length < minLen) {
      str += pad[Math.floor(Math.random() * pad.length)];
    }
    return str;
  }

  // Auto-generate password dari gabungan 4 karakter akhir nama & nik
  $('#nik, #nama_karyawan').on('keyup change', function() {
    const nik = $('#nik').val().trim();
    const nama = $('#nama_karyawan').val().replace(/\s/g, '').toUpperCase();
    if (nik.length >= 4 && nama.length >= 4) {
      const pass = padToMinLength(nama.slice(-4) + nik.slice(-4), 8);
      $('#password').val(pass);
    } else {
      $('#password').val('');
    }
  });

  // Event edit klik
  $('#tabel-karyawan').on('click', '.btn-edit', function() {
    const id = $(this).data('id');
    $.ajax({
      url: '<?= base_url("employee/GetById/") ?>' + id,
      method: 'GET',
      dataType: 'json',
      success: function(data) {
        $('#edit_id').val(data.id_karyawan);
        $('#edit_nama').val(data.nama_karyawan);
        $('#edit_telepon').val(data.telepon);
        $('#edit_jabatan').val(data.jabatan);
        $('#edit_password').val('');
        $('#modalEdit').modal('show');
      }
    });
  });
    // Konfirmasi sebelum submit form Edit
  $('#formEdit').on('submit', function(e) {
    e.preventDefault();
    Swal.fire({
      title: 'Simpan perubahan?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, simpan',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        e.currentTarget.submit(); // Lanjut submit form
      }
    });
  });

  // Event hapus dengan SweetAlert
  $('#tabel-karyawan').on('click', '.btn-delete', function() {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Yakin ingin menghapus?',
      text: 'Data akan dihapus permanen!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = '<?= base_url("employee/DeleteData/") ?>' + id;
      }
    });
  });
// Toggle status aktif/nonaktif
$('#tabel-karyawan').on('click', '.btn-status', function () {
  const id = $(this).data('id');
  const status = $(this).data('status');

  $.ajax({
    url: '<?= base_url("employee/updateStatus") ?>',
    type: 'POST',
    data: { id: id, status: status },
    dataType: 'json',
    success: function (res) {
      if (res.status) {
        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: 'Status berhasil diperbarui',
          timer: 1500,
          showConfirmButton: false
        });
        $('#tabel-karyawan').DataTable().ajax.reload(null, false); // reload tanpa reset halaman
      }
    },
    error: function (xhr) {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: 'Terjadi kesalahan saat memperbarui status'
      });
    }
  });
});


});
</script>
