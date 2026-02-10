<div class="col-12">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><?= $subtitle ?></h3>
            <div class="card-tools">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-default">
                    Tambah Departement
                </button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="10px">No</th>
                        <th>Departement</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($listDepartement as $dept):
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $dept->departement ?></td>
                            <td>
                                <button type="button" class="btn btn-warning btn-edit" data-id="<?= $dept->id_departement ?>">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-delete" data-id="<?= $dept->id_departement ?>">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th width="10px">No</th>
                        <th>Departement</th>
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
                <h4 class="modal-title">Tambah Departement</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="departementForm" action="<?= site_url('databarang/list_departement/addData') ?>" method="post">
                    <div class="form-group">
                        <label for="departement">Departement</label>
                        <input type="text" id="departement" name="departement" class="form-control" required>
                    </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnAddSaveChanges">Save changes</button>
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
            <form id="editDepartementForm" action="<?= site_url('databarang/list_departement/updateData') ?>" method="post">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title">Edit Departement</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group">
                        <label for="edit_departement">Departement</label>
                        <input type="text" name="edit_departement" id="edit_departement" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSaveChanges">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#btnAddSaveChanges').on('click', function(e) {
        e.preventDefault();

        const departement = $('#departement').val().trim();
        if (!departement) {
            Swal.fire('Error', 'Departement tidak boleh kosong!', 'warning');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi',
            text: `Apakah kamu yakin ingin menambahkan ${departement} ke data departement?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Tambah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#departementForm').submit();
            }
        });
    });

    $(document).ready(function() {
        $('.btn-edit').click(function() {
            const id = $(this).data('id');

            $.ajax({
                url: '<?= site_url('databarang/list_departement/getDataById') ?>',
                type: 'GET',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    $('#edit_id').val(data.id_departement);
                    $('#edit_departement').val(data.departement);
                    $('#modal-edit').modal('show');
                },
                error: function() {
                    Swal.fire('Gagal', 'Data tidak ditemukan.', 'error');
                }
            });
        });

        $('#btnSaveChanges').on('click', function(e) {
            e.preventDefault();

            const newDept = $('#edit_departement').val().trim();
            if (!newDept) {
                Swal.fire('Error', 'Departement tidak boleh kosong!', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah kamu yakin ingin mengubah departement menjadi ${newDept}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#editDepartementForm').submit();
                }
            });
        });
    });
    $(document).ready(function() {
        $('.btn-delete').click(function() {
            var id = $(this).data('id');
            if (!id) {
                Swal.fire('Error', 'ID Departement tidak ditemukan.', 'error');
                return;
            }
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: 'Data tidak bisa dikembalikan setelah dihapus!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('databarang/list_departement/deleteData/') ?>" + id;
                }
            });
        });
    });
</script>