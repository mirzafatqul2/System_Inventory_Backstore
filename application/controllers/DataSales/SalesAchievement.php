<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalesAchievement extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModelSalesAchievement');
        $this->load->helper('format_helper');
        // pastikan session sudah autoload
    }

    public function index()
    {
        $menu = 'data_sales';
        $submenu = 'sales_achievement';
        $menuItems = get_menu_items($menu, $submenu);

        $data = [
            'title' => 'Sales Achievement',
            'subtitle' => 'Data Sales Achievement',
            'isi' => 'Sales/SalesAchievement/view',
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems,
            'salesAchievements' => $this->ModelSalesAchievement->get_monthly_summary(),
        ];

        $this->load->view('layout/wrapper', $data, false);
    }

    public function dailyAchievement($bulan)
{
    $menu = 'data_sales';
    $submenu = 'sales_achievement';
    $menuItems = get_menu_items($menu, $submenu);

    $data = array(
        'title' => 'Daily Sales Achievement',
        'subtitle' => 'Data Harian ' . format_bulan($bulan),
        'isi' => 'Sales/SalesAchievement/daily_sales',
        'menu' => $menu,
        'submenu' => $submenu,
        'menuItems' => $menuItems,
        'bulan' => $bulan,
        'dailyAchievements' => $this->ModelSalesAchievement->get_monthly_detail($bulan),
    );

    $this->load->view('layout/wrapper', $data, false);
}


    public function inputDailySales()
    {
        $tanggal = $this->input->post('tanggal', true);
        $traffic = (int)$this->input->post('traffic');
        $sales1 = (int)str_replace('.', '', $this->input->post('sales_1'));
        $transaksi1 = (int)$this->input->post('transaksi_1');
        $qtysold1 = (int)$this->input->post('qty_sold_1');
        $sales2 = (int)str_replace('.', '', $this->input->post('sales_2'));
        $transaksi2 = (int)$this->input->post('transaksi_2');
        $qtysold2 = (int)$this->input->post('qty_sold_2');

        if (empty($tanggal)) {
            $this->session->set_flashdata('error', 'Tanggal harus diisi.');
            redirect('datasales/sales_achievement/dailyAchievement/' . $bulan);
            return;
        }

        $sales = $sales1 + $sales2;
        $transaksi = $transaksi1 + $transaksi2;
        $qty_sold = $qtysold1 + $qtysold2;

        $bulan = date('Y-m', strtotime($tanggal));

        $data = [
            'daily_sales' => $sales,
            'qty_sold'    => $qty_sold,
            'transaction' => $transaksi,
            'traffic'     => $traffic
        ];

        $this->ModelSalesAchievement->input($tanggal, $data);
        $this->session->set_flashdata('success', 'Data Daily Sales Berhasil Disimpan.');
        redirect('datasales/sales_achievement/dailyAchievement/' . $bulan);
    }

    public function exportExcel()
    {
        $data = $this->ModelSalesAchievement->get_monthly_summary();
        if (empty($data)) {
            $this->session->set_flashdata('error', 'Tidak ada data untuk diekspor.');
            redirect('datasales/sales_achievement/dailyAchievement/' . $bulan);
            return;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['No','Bulan','Sales Target','Sales Achievement','% Achieve','UPT','ATV','SCR','Performance Status'];
        $col = 'A';
        foreach($headers as $h){
            $sheet->setCellValue($col.'1', $h);
            $sheet->getStyle($col.'1')->getFont()->setBold(true);
            $col++;
        }

        $row = 2; $no = 1;
        foreach ($data as $item) {
            $achievePercent = ($item->target_bulanan > 0) ? round(($item->total_sales / $item->target_bulanan) * 100, 2) : 0;
            $status = ($achievePercent >= 100) ? 'ACHIEVED' : 'NOT ACHIEVED';

            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $item->bulan);
            $sheet->setCellValue('C'.$row, $item->target_bulanan);
            $sheet->setCellValue('D'.$row, $item->total_sales);
            $sheet->setCellValue('E'.$row, $achievePercent.' %');
            $sheet->setCellValue('F'.$row, $item->upt);
            $sheet->setCellValue('G'.$row, $item->atv);
            $sheet->setCellValue('H'.$row, $item->scr.' %');
            $sheet->setCellValue('I'.$row, $status);
            $row++;
        }

        foreach(range('A','I') as $col){
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'SalesAchievement.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

   public function exportDailyExcel($bulan)
{
    $data = $this->ModelSalesAchievement->get_monthly_detail($bulan);

    if (empty($data)) {
        $this->session->set_flashdata('error', 'Tidak ada data untuk diekspor.');
        redirect('datasales/sales_achievement/dailyAchievement/' . $bulan);
    }

    // Buat Spreadsheet
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Tanggal');
    $sheet->setCellValue('C1', 'Sales Target');
    $sheet->setCellValue('D1', 'Sales Achievement');
    $sheet->setCellValue('E1', '% Achieve');
    $sheet->setCellValue('F1', 'UPT');
    $sheet->setCellValue('G1', 'ATV');
    $sheet->setCellValue('H1', 'SCR');

    // Isi data
    $row = 2;
    $no = 1;
    foreach ($data as $daily) {
        // hitungan sesuai view mu sebelumnya
        $achive = ($daily->daily_target > 0) ? round(($daily->daily_sales / $daily->daily_target) * 100, 2) : 0;
        $upt = ($daily->transaction > 0) ? ($daily->qty_sold / $daily->transaction) : 0;
        $atv = ($daily->transaction > 0) ? ($daily->daily_sales / $daily->transaction) : 0;
        $scr = ($daily->traffic > 0) ? ($daily->transaction / $daily->traffic * 100) : 0;

        $sheet->setCellValue('A' . $row, $no++);
        $sheet->setCellValue('B' . $row, $daily->tanggal);
        $sheet->setCellValue('C' . $row, $daily->daily_target);
        $sheet->setCellValue('D' . $row, $daily->daily_sales);
        $sheet->setCellValue('E' . $row, $achive . '%');
        $sheet->setCellValue('F' . $row, number_format($upt, 2, ',', '.'));
        $sheet->setCellValue('G' . $row, $atv);
        $sheet->setCellValue('H' . $row, number_format($scr, 2, ',', '.') . '%');

        $row++;
    }

    // Output
    $filename = 'DailySales-' . $bulan . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

public function checkDailyData($bulan)
{
    $data = $this->ModelSalesAchievement->get_monthly_detail($bulan);
    if (empty($data)) {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada data untuk diekspor.']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Data siap untuk diekspor.']);
    }
}

public function checkTanggal()
{
    $tanggal = $this->input->post('tanggal');

    $row = $this->db->get_where('sales_achievements', ['tanggal' => $tanggal])->row();

    if ($row) {
        // cek apakah datanya masih kosong (belum diinput)
        $isEmpty = (
            (int)$row->daily_sales === 0 &&
            (int)$row->transaction === 0 &&
            (int)$row->qty_sold === 0 &&
            (int)$row->traffic === 0
        );

        if ($isEmpty) {
            // tanggal ada tapi kosong, anggap belum ada data
            echo json_encode([
                'status' => 'ok',
                'message' => 'Tanggal ini belum memiliki data sales, silakan input.'
            ]);
        } else {
            // tanggal ada dan sudah ada data, berarti perlu konfirmasi update
            echo json_encode([
                'status' => 'exists',
                'message' => 'Tanggal '.$tanggal.' sudah ada data salesnya. Apakah Anda ingin mengubah?'
            ]);
        }
    } else {
        // tanggal sama sekali belum ada (kalau misalnya ada kondisi tertentu)
        echo json_encode([
            'status' => 'ok',
            'message' => 'Tanggal ini belum ada di database, silakan input.'
        ]);
    }
}

}
