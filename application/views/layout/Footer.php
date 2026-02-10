</div>
<!-- /.row -->
</div>
<!-- /.container-fluid -->
</div>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<!-- Footer Section -->
<footer class="main-footer">
    <div class="float-right d-none d-sm-block">
        <b>Version</b> 1.0.
    </div>
    <strong>BACKSTORE MR DIY</strong> All rights reserved.
</footer>

<!-- Main Footer -->
</div>
<!-- ./wrapper -->

<!-- Loading Overlay -->
<div id="loading-overlay">
    <div class="spinner"></div>
    <p class="loading-text">Memuat data, mohon tunggu...</p>
</div>
<script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- DataTables  & Plugins -->
<script src="<?= base_url() ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/jszip/jszip.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- ChartJS -->
<script src="<?= base_url() ?>assets/plugins/chart.js/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- AdminLTE App -->
<script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>

<!-- SweetAlert2 -->
<script src="<?= base_url() ?>assets/plugins/sweetalert2/sweetalert2.min.js"></script>

<!-- Custom Loading and SweetAlert Script -->
<!-- Load Spinner CSS & JS -->
<script src="<?= base_url('assets/dist/js/loading.js') ?>"></script>
<script>
    <?php if ($this->session->flashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= $this->session->flashdata('success') ?>',
            timer: 1500,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            html: `<?= str_replace("\n", "<br>", trim($this->session->flashdata('error'))) ?>`
        });
    <?php endif; ?>
</script>
</body>

</html>