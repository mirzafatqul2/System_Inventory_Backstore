<?php


defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalesTarget extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ModelSalesTarget');
    }

    public function index()
    {
        //mengambil data sales_target dari model
        $sales_target = $this->ModelSalesTarget->get_all();
        //Pastikan data sales_target tidak kosong
        if (empty($sales_target)) {
            $sales_target = [];
        }
        //menentukan menu dan submenu yang aktif
        $menu = 'data_sales';
        $submenu = 'sales_target';

        //mendapatkan menuItems yang aktif
        $menuItems = get_menu_items($menu, $submenu);

        //data view
        $data = array(
            'title' => 'Sales Target',
            'subtitle' => 'Data Sales Target',
            'isi' => 'Sales/SalesTarget/view',
            'menu' => $menu,
            'submenu' => $submenu,
            'menuItems' => $menuItems,
            'sales_target' => $sales_target
        );

        $this->load->view('layout/wrapper', $data, false);
    }

    public function addData()
    {
        $bulan = $this->input->post('bulan');
        $base_target = $this->input->post('base');
        $level1_target = $this->input->post('level1');
        $level2_target = $this->input->post('level2');
        $level3_target = $this->input->post('level3');
        $level4_target = $this->input->post('level4');

        $data = array(
            'bulan' => $bulan,
            'base_target' => $base_target,
            'level1_target' => $level1_target,
            'level2_target' => $level2_target,
            'level3_target' => $level3_target,
            'level4_target' => $level4_target,
        );
        $cek = $this->db->get_where('sales_targets', ['bulan' => $bulan])->row();
        if ($cek) {
            $this->session->set_flashdata('error', 'Target Bulan ini Sudah Ada.');
            redirect('datasales/sales_target');
        } else {
            $insert = $this->ModelSalesTarget->insert($data);
            if ($insert) {
                $salesTargetId = $this->db->insert_id();

                $dateInMonth = new DateTime($bulan . '-01');
                $daysInMonth = $dateInMonth->format('t');

                $weekendCount = 0;
                $weekdayCount = 0;

                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $date = new DateTime($bulan . '-' . str_pad($i, 2, '0', STR_PAD_LEFT));
                    $day = $date->format('N');
                    if ($day == 6 || $day == 7) {
                        $weekendCount++;
                    } else {
                        $weekdayCount++;
                    }
                }

                $salesTarget = $level1_target;
                $weekendPortion = 0.40 * $salesTarget;
                $weekdayPortion = 0.60 * $salesTarget;

                $targetWeekday = $weekdayCount > 0 ? $weekdayPortion / $weekdayCount : 0;
                $targetWeekend = $weekendCount > 0 ? $weekendPortion / $weekendCount : 0;

                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $date = new DateTime($bulan . '-' . str_pad($i, 2, '0', STR_PAD_LEFT));
                    $day = $date->format('N');

                    $dailyTarget = ($day == 6 || $day == 7) ? $targetWeekend : $targetWeekday;

                    $this->db->insert('sales_achievements', [
                        'sales_target_id' => $salesTargetId,
                        'tanggal' => $date->format('Y-m-d'),
                        'daily_target' => round($dailyTarget, 2)
                    ]);
                }
                $formattedBulan = format_bulan($bulan);
                $this->session->set_flashdata('success', "Data Sales Target $formattedBulan Berhasil Ditambahkan!");
            } else {
                $this->session->set_flashdata('error', 'Gagal Menambahkan Sales Target.');
            }
            redirect('datasales/sales_target');
        }
    }

    public function getDataById()
    {
        $idSalesTarget = $this->input->get('id');
        $data = $this->ModelSalesTarget->get_by_id($idSalesTarget);
        echo json_encode($data);
    }

    public function updateData()
    {
        $this->form_validation->set_rules('bulan', 'Bulan', 'required');
        $this->form_validation->set_rules('base', 'Base Target', 'required');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('datasales/sales_target');
        } else {
            $id = $this->input->post('id');
            $bulan = $this->input->post('bulan');
            $base = $this->input->post('base');
            $level1 = $this->input->post('level1');
            $level2 = $this->input->post('level2');
            $level3 = $this->input->post('level3');
            $level4 = $this->input->post('level4');

            $data = array(
                'bulan' => $bulan,
                'base_target' => $base,
                'level1_target' => $level1,
                'level2_target' => $level2,
                'level3_target' => $level3,
                'level4_target' => $level4,
            );

            // Update tabel sales_targets
            $this->ModelSalesTarget->update($id, $data);

            // Lanjut update daily target di tabel sales_achievements
            $dateInMonth = new DateTime($bulan . '-01');
            $daysInMonth = $dateInMonth->format('t');

            $weekendCount = 0;
            $weekdayCount = 0;

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date = new DateTime($bulan . '-' . str_pad($i, 2, '0', STR_PAD_LEFT));
                $day = $date->format('N');
                if ($day == 6 || $day == 7) {
                    $weekendCount++;
                } else {
                    $weekdayCount++;
                }
            }

            $salesTarget = $level1; // asumsi pakai level1 untuk target
            $weekendPortion = 0.40 * $salesTarget;
            $weekdayPortion = 0.60 * $salesTarget;

            $targetWeekday = $weekdayCount > 0 ? $weekdayPortion / $weekdayCount : 0;
            $targetWeekend = $weekendCount > 0 ? $weekendPortion / $weekendCount : 0;

            // Update setiap baris sales_achievements berdasarkan tanggal
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date = new DateTime($bulan . '-' . str_pad($i, 2, '0', STR_PAD_LEFT));
                $day = $date->format('N');

                $dailyTarget = ($day == 6 || $day == 7) ? $targetWeekend : $targetWeekday;

                $this->db->where('sales_target_id', $id);
                $this->db->where('tanggal', $date->format('Y-m-d'));
                $this->db->update('sales_achievements', [
                    'daily_target' => round($dailyTarget, 2)
                ]);
            }

            $formattedBulan = format_bulan($bulan);
            $this->session->set_flashdata('success', "Data Sales Target $formattedBulan berhasil diperbarui!");
            redirect('datasales/sales_target');
        }
    }

    public function deleteData($id)
    {
        if (!$id) {
            $this->session->set_flashdata('error', 'ID tidak ditemukan!');
            redirect('datasales/sales_target');
        }

        $getDataByID = $this->ModelSalesTarget->get_by_id($id);

        if (!$getDataByID) {
            $this->session->set_flashdata('error', 'Data Sales Target tidak ditemukan!');
            redirect('datasales/sales_target');
        }

        $bulan = $getDataByID->bulan;
        $formattedBulan = format_bulan($bulan);

        $deleted = $this->ModelSalesTarget->delete($id);

        if ($deleted) {
            $this->session->set_flashdata('success', "Data Sales Target bulan $formattedBulan berhasil dihapus!");
        } else {
            $this->session->set_flashdata('error', "Gagal menghapus Data Sales Target bulan $formattedBulan.");
        }

        redirect('datasales/sales_target');
    }

    public function exportExcel()
    {
        $data = $this->ModelSalesTarget->get_all();
        if (empty($data)) {
            $this->session->set_flashdata('error', 'Tidak ada data untuk diekspor.');
            redirect('datasales/sales_target');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Bulan');
        $sheet->setCellValue('C1', 'Base Target');
        $sheet->setCellValue('D1', 'Level 1');
        $sheet->setCellValue('E1', 'Level 2');
        $sheet->setCellValue('F1', 'Level 3');
        $sheet->setCellValue('G1', 'Level 4');

        //Data
        $row = 2;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->bulan);
            $sheet->setCellValue('C' . $row, $item->base_target);
            $sheet->setCellValue('D' . $row, $item->level1_target);
            $sheet->setCellValue('E' . $row, $item->level2_target);
            $sheet->setCellValue('F' . $row, $item->level3_target);
            $sheet->setCellValue('G' . $row, $item->level4_target);
            $row++;
        }

        $filename = 'SalesTarget.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

/* End of file SalesTarget.php */
