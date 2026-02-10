<div class="col-12">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title"><?= $subtitle ?></h3>
            <div class="card-tools">
                <!-- ✅ Tombol Import -->
                <button id="btnImport" class="btn btn-success" data-toggle="modal" data-target="#modalImport">
                    <i class="fa fa-file-excel"></i> Import Excel
                </button>
                <!-- ✅ Tombol Tambah -->
                <button id="btnAddBrownbox" class="btn btn-success" data-toggle="modal" data-target="#modalAddBrownbox">
                    <i class="fas fa-plus-circle"></i> Tambah Keepstock
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped server-side-datatable"
                   data-url="<?= site_url('databarang/master_keepstock/ajax_list') ?>">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Brownbox</th>
                        <th>SKU List</th>
                        <th>Departemen</th>
                        <th>Description List</th>
                        <th>Rack List</th>
                        <th>Qty</th>
                        <th>Total Amount</th>
                        <th style="width:120px;">Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Brownbox</th>
                        <th>SKU List</th>
                        <th>Departemen</th>
                        <th>Description List</th>
                        <th>Rack List</th>
                        <th>Qty</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- ✅ Modal Import -->
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title">Import Data Keepstock (Excel)</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="importExcelForm" action="<?= site_url('databarang/master_keepstock/importExcel') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih File Excel (.xls / .xlsx)</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xls,.xlsx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btnImportExcel"><i class="fas fa-upload"></i> Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ✅ Modal Tambah Brownbox -->
<div class="modal fade" id="modalAddBrownbox" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h4 class="modal-title">Tambah Data Keepstock</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addBrownboxForm" action="<?= site_url('databarang/master_keepstock/addData') ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="brownbox">Brownbox (Prefix)</label>
                        <input type="text" id="brownbox" name="brownbox" class="form-control" placeholder="Contoh: A001" required>
                    </div>
                    <div class="form-group">
                        <label for="departemen_id">Departemen</label>
                        <select id="departemen_id" name="departemen_id" class="form-control" required>
                            <option value="">-- Pilih Departemen --</option>
                            <?php foreach ($listDepartement as $departemen): ?>
                                <option value="<?= $departemen->id_departement ?>"><?= $departemen->departement ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Brownbox</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="mode" value="ctn" checked>
                            <label class="form-check-label">Dengan Turunan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="mode" value="sku">
                            <label class="form-check-label">Tanpa Turunan (Multi SKU)</label>
                        </div>
                    </div>
                    <div class="form-group" id="ctn-group">
                        <label>Jumlah Brownbox Turunan</label>
                        <input type="number" id="ctn" name="ctn" min="1" max="10" value="1" class="form-control" required>
                    </div>
                    <div class="form-group" id="sku-group" style="display:none;">
                        <label>Jumlah SKU</label>
                        <input type="number" id="sku_count" name="sku_count" min="1" max="10" value="1" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6" id="skuInputs"></div>
                        <div class="col-md-6" id="qtyInputs"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btnSubmitAddBrownbox"><i class="fas fa-plus-circle"></i> Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ✅ Modal Edit Brownbox -->
<div class="modal fade" id="modalEditBrownbox" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <form id="formEditBrownbox">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h4 class="modal-title">Edit Keepstock</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="brownbox_awal" id="brownbox_awal">
          <div class="form-group">
            <label>Brownbox</label>
            <input type="text" name="brownbox" id="edit_brownbox" class="form-control" readonly>
            <button type="button" id="btnEnableRename" class="btn btn-sm btn-warning mt-2">
              <i class="fa fa-edit"></i> Ubah Nama Brownbox
            </button>
          </div>
          <div class="form-group">
            <label for="edit_departement_id">Departemen</label>
            <select id="edit_departement_id" name="departemen_id" class="form-control">
              <?php foreach ($listDepartement as $departemen): ?>
              <option value="<?= $departemen->id_departement ?>"><?= $departemen->departement ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div id="editSkuWrapper" class="row"></div>
          <button type="button" id="btnAddSkuRow" class="btn btn-sm btn-info mt-2">
            <i class="fa fa-plus"></i> Tambah SKU
          </button>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnSaveEdit" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
$(function(){
    const table = $('.server-side-datatable').DataTable({
        processing:true, serverSide:true,
        ajax:{url:$('.server-side-datatable').data('url'),type:'POST'},
        columnDefs:[
            {targets:[0,8],orderable:false},
            {targets:[8],className:'text-center'}
        ]
    });

    // Import Excel
    $('#btnImportExcel').on('click',function(e){
        e.preventDefault();
        Swal.fire({title:'Konfirmasi',text:'Yakin import file Excel?',icon:'question',showCancelButton:true})
        .then(res=>{ if(res.isConfirmed) $('#importExcelForm').submit(); });
    });

    // Add Brownbox
    function renderFields(){
        const prefix=$('#brownbox').val().toUpperCase();
        const mode=$('input[name="mode"]:checked').val();
        $('#skuInputs').html(''); $('#qtyInputs').html('');
        if(mode==='ctn'){
            let c=Math.min(Math.max(parseInt($('#ctn').val())||1,1),10);
            for(let i=0;i<c;i++){
                const suffix=String.fromCharCode(65+i);
                $('#skuInputs').append(`<div class="form-group"><label>SKU ${prefix}${suffix}</label><input type="text" name="sku[]" class="form-control" required></div>`);
                $('#qtyInputs').append(`<div class="form-group"><label>Qty ${prefix}${suffix}</label><input type="number" name="qty[]" min="1" class="form-control" required></div>`);
            }
        } else {
            let c=Math.min(Math.max(parseInt($('#sku_count').val())||1,1),10);
            for(let i=0;i<c;i++){
                $('#skuInputs').append(`<div class="form-group"><label>SKU ${i+1}</label><input type="text" name="sku[]" class="form-control" required></div>`);
                $('#qtyInputs').append(`<div class="form-group"><label>Qty ${i+1}</label><input type="number" name="qty[]" min="1" class="form-control" required></div>`);
            }
        }
    }
    $('#ctn,#sku_count,#brownbox').on('input change',renderFields);
    $('input[name="mode"]').on('change',function(){
        const mode=$(this).val();
        $('#ctn-group').toggle(mode==='ctn');
        $('#sku-group').toggle(mode!=='ctn');
        renderFields();
    });
    $(document).ready(renderFields);

    $('#btnSubmitAddBrownbox').on('click',function(e){
        e.preventDefault();
        let form=$('#addBrownboxForm');
        let isValid=true; let firstInvalid=null;
        form.find('input[required],select[required]').each(function(){
            if($(this).val().trim()===''){
                isValid=false;
                if(!firstInvalid) firstInvalid=$(this);
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        if(!isValid){
            Swal.fire({icon:'error',title:'Error',text:'Semua field wajib diisi!'}).then(()=>firstInvalid&&firstInvalid.focus());
            return;
        }
        Swal.fire({title:'Konfirmasi',text:'Yakin ingin menambahkan data ini?',icon:'question',showCancelButton:true})
        .then(r=>r.isConfirmed&&form.submit());
    });

    // Edit Brownbox
    $(document).on('click','.btn-edit',function(){
        let brownbox=$(this).data('brownbox');
        $.post('<?=site_url('databarang/master_keepstock/get_detail')?>',{brownbox:brownbox},function(res){
            $('#editSkuWrapper').html('');
            $('#edit_brownbox').prop('readonly', true).val(brownbox);
            $('#brownbox_awal').val(brownbox);
            if(res.length>0){
                $('#edit_departement_id').val(res[0].departemen_id);
                res.forEach((item,i)=>{
                    $('#editSkuWrapper').append(`
                        <div class="row rowSkuItem w-100 mb-2">
                            <div class="col-md-5">
                                <label>SKU ${i+1}</label>
                                <input type="text" name="sku[]" class="form-control" value="${item.sku}" required>
                            </div>
                            <div class="col-md-5">
                                <label>Qty ${i+1}</label>
                                <input type="number" name="qty[]" class="form-control" value="${item.qty}" min="0" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btnRemoveSku"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    `);
                });
            }
            $('#modalEditBrownbox').modal('show');
        },'json');
    });

    // Enable rename brownbox
    $(document).on('click','#btnEnableRename',function(){
        let countSku = $('#editSkuWrapper .rowSkuItem').length;
        if(countSku>1){
            Swal.fire({icon:'error',title:'Tidak Bisa Rename',text:'Brownbox dengan banyak SKU tidak dapat di-rename!'});
        } else {
            $('#edit_brownbox').prop('readonly', false).focus();
        }
    });

    // Add SKU row
    $(document).on('click','#btnAddSkuRow',function(){
        let count = $('#editSkuWrapper .rowSkuItem').length + 1;
        let newRow = `
            <div class="row rowSkuItem w-100 mb-2">
                <div class="col-md-5">
                    <label>SKU ${count}</label>
                    <input type="text" name="sku[]" class="form-control" required>
                </div>
                <div class="col-md-5">
                    <label>Qty ${count}</label>
                    <input type="number" name="qty[]" class="form-control" min="0" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btnRemoveSku"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
        $('#editSkuWrapper').append(newRow);
    });

    // Remove SKU row
    $(document).on('click','.btnRemoveSku',function(){
        $(this).closest('.rowSkuItem').remove();
        $('#editSkuWrapper .rowSkuItem').each(function(idx){
            $(this).find('label').eq(0).text('SKU ' + (idx+1));
            $(this).find('label').eq(1).text('Qty ' + (idx+1));
        });
    });

    // Save Edit
    $('#btnSaveEdit').on('click', function () {
        $.ajax({
            url: '<?=site_url('databarang/master_keepstock/update')?>',
            type: 'POST',
            data: $('#formEditBrownbox').serialize(),
            dataType: 'json',
            success: function (resp) {
                if (resp.status) {
                    Swal.fire({icon:'success',title:'Sukses',text:resp.message || 'Data berhasil diupdate!'})
                    .then(()=>{
                        $('#modalEditBrownbox').modal('hide');
                        table.ajax.reload(null,false);
                    });
                } else {
                    Swal.fire({icon:'error',title:'Error',html:resp.message || 'Gagal mengupdate data!'});
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({icon:'error',title:'Error',text:'Terjadi kesalahan: '+error});
            }
        });
    });
});
</script>
