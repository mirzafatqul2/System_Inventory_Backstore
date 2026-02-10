<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Keep Stock Form</title>
<style>
    @page {
        size: A4;
        margin: 0;
    }
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 0;
    }
    .page {
        width: 210mm;
        height: 297mm;
        padding: 5mm;
        box-sizing: border-box;
        page-break-after: always;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    td, th {
        border: 1px solid #000;
        padding: 3px;
        text-align: center;
    }
    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
        height: 40mm;
    }
    .header-left {
        width: 60mm; /* lebar sama dengan kolom SKU */
        height: 25mm;
        border: 1px solid #000;
        font-size: 28px;
        font-weight: bold;
        text-align: center;
        vertical-align: middle;
    }
    .header-right {
        text-align: center;
        font-size: 28px;
        font-weight: bold;
    }
    .sku-table th:nth-child(1),
    .sku-table td:nth-child(1) {
        width: 60mm; /* samakan dengan header-left */
    }
</style>
</head>
<body>
<?php
$chunks = array_chunk($items, 5);
if (empty($chunks)) {
    $chunks = [[]];
}
foreach ($chunks as $pageItems):
?>
<div class="page">
    <table class="header-table">
        <tr>
            <td class="header-left"><?= htmlspecialchars($brownbox) ?></td>
            <td class="header-right">KEEP STOCK<br>FORM</td>
        </tr>
    </table>

    <?php
    for ($i = 0; $i < 5; $i++):
        $row = isset($pageItems[$i]) ? $pageItems[$i] : null;
    ?>
    <table class="sku-table">
        <thead>
            <tr>
                <th>SKU</th>
                <th>IN</th>
                <th>OUT</th>
                <th>STOCK</th>
                <th>DATE UPDATE</th>
                <th>VALIDATOR</th>
                <th>TGL</th>
                <th>DSP</th>
                <th>GT</th>
                <th>RAK</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $row ? htmlspecialchars($row->sku) : '&nbsp;' ?></td>
                <td style="width:10mm"><?= $row ? htmlspecialchars($row->qty) : '&nbsp;' ?></td>
                <td style="width:10mm">&nbsp;</td>
                <td><?= $row ? htmlspecialchars($row->qty) : '&nbsp;' ?></td>
                <td><?= $row ? date('d/m/Y') : '&nbsp;' ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <?php for ($j = 0; $j < 6; $j++): ?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
    <?php endfor; ?>
</div>
<?php endforeach; ?>

<script>
window.print();
</script>
</body>
</html>
