    <?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Pettycash extends CI_Controller {

        public function __construct()
        {
            parent::__construct();
            $this->load->model('ModelPettycash');
        }

        public function index()
        {
            $menu = 'data_pettycash';
            $submenu = 'claim_pettycash';
            $menuItems = get_menu_items($menu, $submenu);

            $data = [
                'title' => 'Claim Petty Cash',
                'subtitle' => 'Data Penggunaan Petty Cash',
                'isi' => 'Pettycash/view',
                'menu' => $menu,
                'submenu' => $submenu,
                'menuItems' => $menuItems,
                'coa_list' => $this->db->get('master_coa')->result()
            ];

            $this->load->view('layout/wrapper', $data, false);
        }

        public function ajax_list()
        {
            $list = $this->ModelPettycash->get_datatables();
            $data = [];
            $no = isset($_POST['start']) ? (int)$_POST['start'] : 0;

            foreach ($list as $claim) {
        $no++;
        $row = [];
        $row[] = $no;
        $row[] = date('d-m-Y', strtotime($claim->date));
        $row[] = $claim->coa ?: '-';
        $row[] = $claim->desc_coa ?: '-';
        $row[] = $claim->desc_use;
        $row[] = number_format($claim->amount, 0, ',', '.');

         $id = $claim->id;
        if ($claim->status_claim == 1) { // Pending
            $status_form = '
            <form id="formStatus'.$id.'" method="post" action="'.site_url('pettycash/updateStatus').'">
                <input type="hidden" name="id" value="'.$id.'">
                <input type="hidden" name="status_claim" value="1">
                <button type="button" class="btn btn-warning btn-update-status" data-id="'.$id.'">Pending</button>
            </form>';
        } else { // Claimed
            $status_form = '
            <form id="formStatus'.$id.'" method="post" action="'.site_url('pettycash/updateStatus').'">
                <input type="hidden" name="id" value="'.$id.'">
                <input type="hidden" name="status_claim" value="2">
                <button type="button" class="btn btn-success btn-update-status" data-id="'.$id.'">Claimed</button>
            </form>';
        }
        $row[] = $status_form;
        $row[] = date('d-m-Y H:i', strtotime($claim->created_at));
        $editBtn = '<button class="btn btn-sm btn-primary btn-edit" data-id="' . $claim->id . '"><i class="fa fa-edit"></i></button>';
        $deleteBtn = '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $claim->id . '"><i class="fa fa-trash"></i></button>';
        $row[] = $editBtn . ' ' . $deleteBtn;

        $data[] = $row;
    }


            $output = [
                "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
                "recordsTotal" => $this->ModelPettycash->count_all(),
                "recordsFiltered" => $this->ModelPettycash->count_filtered(),
                "data" => $data,
            ];

            echo json_encode($output);
        }

        public function addData()
    {
        $coa      = $this->input->post('coa');
        $desc_use = $this->input->post('desc_use');
        $amount   = $this->input->post('amount');
        $date     = $this->input->post('date');

        // Di addData()
if (empty($coa) || empty($desc_use) || empty($amount) || empty($date)) {
    $this->session->set_flashdata('error', 'Semua field wajib diisi.');
    redirect('pettycash/pettycash');
}


        $data = [
            'coa'          => $coa,
            'desc_use'     => $desc_use,
            'amount'       => $amount,
            'date'         => $date,
            'status_claim' => 1,
            'created_at'   => date('Y-m-d H:i:s')
        ];

        $insert = $this->ModelPettycash->add($data);
        if($insert){
            $this->session->set_flashdata('success', 'Data Claim Petty Cash Berhasil ditambahkan.');
        }else{
            $this->session->set_flashdata('error', 'Gagal Menambahkan Data.');
            
        }
        redirect('pettycash/pettycash');
    }

        public function getById($id)
        {
            $data = $this->ModelPettycash->getById($id);
            echo json_encode($data);
        }

        public function updateData()
    {
        $id = $this->input->post('id');
        $date = $this->input->post('date');
        $coa = $this->input->post('coa');
        $desc = $this->input->post('desc_use');
        $amount = $this->input->post('amount');

        // Di updateData()
if (empty($id) || empty($date) || empty($coa) || empty($desc) || empty($amount)) {
    $this->session->set_flashdata('error', 'Semua field wajib diisi.');
    redirect('pettycash/pettycash');
}

        $data = [
            'date'       => $date,
            'coa'        => $coa,
            'desc_use'   => $desc,
            'amount'     => $amount,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $update = $this->ModelPettycash->update($id, $data);
        
        if($update){
            $this->session->set_flashdata('success', 'Data Claim Petty Cash Berhasil diubah.');
        }else{
            $this->session->set_flashdata('error', 'Gagal Mengubah Data.'); 
        }
    redirect('pettycash/pettycash');
    }

   public function updateStatus()
{
    $id = $this->input->post('id');
    $current_status = $this->input->post('status_claim');

    if (!$id || !in_array($current_status, ['1','2'])) {
        $this->session->set_flashdata('error', 'Data tidak lengkap atau status tidak valid.');
        redirect('pettycash/pettycash'); // sesuaikan route ini
        return;
    }

    $new_status = ($current_status == '1') ? '2' : '1';

    $update = $this->ModelPettycash->update($id, ['status_claim' => $new_status]);

    if ($update) {
        $this->session->set_flashdata('success', 'Status claim berhasil diubah.');
    } else {
        $this->session->set_flashdata('error', 'Gagal mengubah status claim.');
    }

    redirect('pettycash/pettycash'); // sesuaikan route ini
}


        public function deleteData($id)
        {
            $delete = $this->ModelPettycash->delete($id);
            if($delete){
            $this->session->set_flashdata('success', 'Data Claim Petty Cash Berhasil dihapus.');
        }else{
            $this->session->set_flashdata('error', 'Gagal Menghapus Data.'); 
        }
        redirect('pettycash/pettycash');
        }

        public function export_excel()
        {
            $claims = $this->ModelPettycash->getAll();

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Tanggal');
            $sheet->setCellValue('C1', 'COA');
            $sheet->setCellValue('D1', 'Deskripsi COA');
            $sheet->setCellValue('E1', 'Deskripsi Penggunaan');
            $sheet->setCellValue('F1', 'Jumlah');
            $sheet->setCellValue('G1', 'Status');
            $sheet->setCellValue('H1', 'Dibuat Pada');

            $rowNum = 2;
            $no = 1;
            foreach ($claims as $c) {
                $status = $c->status_claim == 1 ? 'Pending' : 'Claimed';

                $sheet->setCellValue('A' . $rowNum, $no++);
                $sheet->setCellValue('B' . $rowNum, date('d-m-Y', strtotime($c->date)));
                $sheet->setCellValue('C' . $rowNum, $c->coa ?: '-');
                $sheet->setCellValue('D' . $rowNum, $c->desc_coa ?: '-');
                $sheet->setCellValue('E' . $rowNum, $c->desc_use);
                $sheet->setCellValue('F' . $rowNum, $c->amount);
                $sheet->setCellValue('G' . $rowNum, $status);
                $sheet->setCellValue('H' . $rowNum, date('d-m-Y H:i', strtotime($c->created_at)));
                $rowNum++;
            }

            $filename = 'PettyCash_' . date('Ymd_His') . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
        }
    }
