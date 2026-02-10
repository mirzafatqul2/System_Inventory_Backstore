function loadServerSideTable(selector, ajaxUrl, columns) {
    const table = $(selector);

    if ($.fn.DataTable.isDataTable(table)) {
        table.DataTable().clear().destroy();
    }

    table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: ajaxUrl,
            type: 'POST'
        },
        columns: columns,
        responsive: true,
        autoWidth: false,
        order: [], 
        language: {
            processing: "Memuat data, mohon tunggu..."
        }
    });
}
