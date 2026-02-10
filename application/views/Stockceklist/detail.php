<div class="col-12">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><?= $subtitle ?></h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped server-side-detail" data-url="<?= site_url('Stockceklist/ajax_detail') ?>">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>SKU</th>
                        <th>Variance</th>
                        <th>Amount</th>
                        <th>Tanggal Dibuat</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var detailTable = $('.server-side-detail').DataTable({
        processing: true,
        serverSide: true,
        order: [],
        ajax: {
            url: $('.server-side-detail').data('url'),
            type: "POST",
            data: function(d) {
                d.assignment = "<?= $assignment ?>";
                d.tanggal = "<?= $tanggal ?>";
            }
        },
        columnDefs: [
            { targets: [0], orderable: false }
        ]
    });
});
</script>
