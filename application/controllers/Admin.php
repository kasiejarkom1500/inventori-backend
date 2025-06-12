<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH.'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        
        $this->load->model('Admin_model');
        $this->load->model('User_model');
		is_logged_in();
    }
    public function index()
    {
        is_logged_in();
        $data['judul'] = "admin";
        

        $data['data_admin'] = $this->Admin_model->tampil_data();
       
        $this->load->view('templates/header', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer');
    }


    
    public function tambah()
    {
        is_logged_in();
        $data['judul']="admin";
        $data['data_admin'] = $this->Admin_model->tampil_data();

        
        $this->form_validation->set_rules('nama','nama admin','required|trim');
        $this->form_validation->set_rules('email','email','required|trim|valid_email');
        $this->form_validation->set_rules('username','Username',
        'required|trim');
        $this->form_validation->set_rules('password','Password',
        'min_length[5]',[
            'min_length' => "Password minimal 5 digit"
        ]);
       
        

        if ($this->form_validation->run() == FALSE){
            
        $this->load->view('templates/header',$data);
        $this->load->view('admin/tambah',$data);
        $this->load->view('templates/footer');
    }
       else{
            $this->Admin_model->simpan();
            redirect('admin');
        }
        
    

    }

    public function hapus($id)
    {
		is_logged_in();
        $this->Admin_model->hapus($id);
        redirect('admin');
    }



    public function ubah($id= '')
    {
		is_logged_in();
        
        $data['judul'] = "admin";
        $data_admin = $this->Admin_model->get_id($id);
        $data['ubah_admin'] = $this->Admin_model->get_id($id);

        $this->form_validation->set_rules('nama', 'nama admin', 'required|trim');
        $this->form_validation->set_rules('email','email','required|trim|valid_email');
        $this->form_validation->set_rules(
            'username',
            'username',
            'required|trim'
        );
        $this->form_validation->set_rules(
            'password',
            'Password',
            'min_length[5]',
            [
                'min_length' => "Password minimal 5 digit",
                
            ]
        );
        
        

        if ($this->form_validation->run() == FALSE) {
            if ($data_admin > 0) {
                $data['ubah_admin'] = $this->Admin_model->get_id($id);
                $this->load->view('templates/header', $data);
                $this->load->view('admin/ubah', $data);
                $this->load->view('templates/footer');
            } else {
                $pesan = "Data tidak ditemukan";
                $url = base_url('admin');
                echo "<script>
        alert('$pesan');
        location='$url';
    </script>";
            }
        } else {
            $this->Admin_model->ubah();
            $pesan = "Data berhasil diupdate";
            $url = base_url('admin');
            echo "<script>
            alert('$pesan');
            location='$url';
        </script>";
        }
    }


    public function get($id = null)

    {
        $this->db->select('*');
        $this->db->from('admin');
        if ($id != null) {
            $this->db->where('id', $id);
        }
        return $this->db->get();
    }


	public function data_user()
	{
		is_logged_in();
        $data['title'] = 'Profile';
		$data['data_user'] = $this->User_model->tampil_data();
        $this->load->view('templates/header', $data);
        $this->load->view('admin/user/index', $data);
        $this->load->view('templates/footer');
	}


	 
    public function tambah_user()
    {
        is_logged_in();
        $data['judul']="User Pegawai";
        $data['admin_ruangan'] = $this->Admin_model->get_all_ruangan();

        
        $this->form_validation->set_rules('nama','Nama Admin','required|trim');
        $this->form_validation->set_rules('jabatan','Jabatan','required|trim');
		$this->form_validation->set_rules('ruangan','Ruangan','required|trim');
        $this->form_validation->set_rules('username','Username',
        'required|trim');
        $this->form_validation->set_rules('password','Password',
        'min_length[5]',[
            'min_length' => "Password minimal 5 digit"
        ]);
       
        

        if ($this->form_validation->run() == FALSE){
            
        $this->load->view('templates/header',$data);
        $this->load->view('admin/user/tambah',$data);
        $this->load->view('templates/footer');
    }
       else{
            $this->User_model->simpan();
            redirect('admin/data_user');
        }
        
    

    }

	public function ubah_user($id= '')
    {
		is_logged_in();
        
        $data['judul'] = "User";
        $data_user = $this->User_model->get_id($id);
        $data['ubah_user'] = $this->User_model->get_id($id);

        $this->form_validation->set_rules('nama', 'nama admin', 'required|trim');
        $this->form_validation->set_rules('jabatan','Jabatan','required|trim');
		$this->form_validation->set_rules('ruangan','Ruangan','required|trim');
        $this->form_validation->set_rules(
            'username',
            'username',
            'required|trim'
        );
        $this->form_validation->set_rules(
            'password',
            'Password',
            'min_length[5]',
            [
                'min_length' => "Password minimal 5 digit",
                
            ]
        );
        
        

        if ($this->form_validation->run() == FALSE) {
            if ($data_user > 0) {
                $data['ubah_user'] = $this->User_model->get_id($id);
                $this->load->view('templates/header', $data);
                $this->load->view('admin/user/ubah', $data);
                $this->load->view('templates/footer');
            } else {
                $pesan = "Data tidak ditemukan";
                $url = base_url('admin/data_user');
                echo "<script>
        alert('$pesan');
        location='$url';
    </script>";
            }
        } else {
            $this->User_model->ubah();
            $pesan = "Data berhasil diupdate";
            $url = base_url('admin/data_user');
            echo "<script>
            alert('$pesan');
            location='$url';
        </script>";
        }
    }
    
	public function hapus_user($id)
    {
		is_logged_in();
        $this->User_model->hapus($id);
        redirect('admin/data_user');
    }

	public function admin_pengajuan()
	{
		is_logged_in();
        $data['title'] = 'Admin Pengajuan';
		$data['pengajuan'] = $this->Admin_model->tampil_data1();
        $this->load->view('templates/header', $data);
        $this->load->view('admin/admin_pengajuan/index', $data);
        $this->load->view('templates/footer');
	}

	public function tambah_admin_pengajuan()
    {
        is_logged_in();
        $data['judul']="Admin Pengajuan";

        
        $this->form_validation->set_rules('nama','Nama Admin','required|trim');
        $this->form_validation->set_rules('jabatan','Jabatan','required|trim');
		$this->form_validation->set_rules('ruangan','Ruangan','required|trim');
        $this->form_validation->set_rules('username','Username',
        'required|trim');
        $this->form_validation->set_rules('password','Password',
        'min_length[5]',[
            'min_length' => "Password minimal 5 digit"
        ]);
       
        

        if ($this->form_validation->run() == FALSE){
            
        $this->load->view('templates/header',$data);
        $this->load->view('admin/admin_pengajuan/tambah',$data);
        $this->load->view('templates/footer');
    }
       else{
            $this->Admin_model->simpan_pengajuan();
            redirect('admin/admin_pengajuan');
        }
        
    

    }

	public function ubah_pengajuan($id= '')
    {
		is_logged_in();
        
        $data['judul'] = "Admin Pengajuan";
        $data_pengajuan = $this->Admin_model->get_id1($id);
        $data['ubah_pengajuan'] = $this->Admin_model->get_id1($id);

        $this->form_validation->set_rules('nama', 'nama admin', 'required|trim');
        $this->form_validation->set_rules('jabatan','Jabatan','required|trim');
		$this->form_validation->set_rules('ruangan','Ruangan','required|trim');
        $this->form_validation->set_rules(
            'username',
            'username',
            'required|trim'
        );
        $this->form_validation->set_rules(
            'password',
            'Password',
            'min_length[5]',
            [
                'min_length' => "Password minimal 5 digit",
                
            ]
        );
        
        

        if ($this->form_validation->run() == FALSE) {
            if ($data_pengajuan > 0) {
                $data['ubah_pengajuan'] = $this->Admin_model->get_id1($id);
                $this->load->view('templates/header', $data);
                $this->load->view('admin/admin_pengajuan/ubah', $data);
                $this->load->view('templates/footer');
            } else {
                $pesan = "Data tidak ditemukan";
                $url = base_url('admin/admin_pengajuan');
                echo "<script>
        alert('$pesan');
        location='$url';
    </script>";
            }
        } else {
            $this->Admin_model->ubah_pengajuan();
            $pesan = "Data berhasil diupdate";
            $url = base_url('admin/admin_pengajuan');
            echo "<script>
            alert('$pesan');
            location='$url';
        </script>";
        }
    }
    
	public function hapus_pengajuan($id)
    {
		is_logged_in();
        $this->Admin_model->hapus1($id);
        redirect('admin/admin_pengajuan');
    }

	public function excel() {
        $file_mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        
        if(isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {

            $inputFileName = $_FILES['file']['tmp_name'];

            try {
                $spreadsheet = IOFactory::load($inputFileName);
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                die('Error loading file: '.$e->getMessage());
            }
            
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            foreach ($sheetData as $key => $row) {
                if ($key == 0) continue; // Skip the header row
                
                $data = array(
                    'nama' => $row[0],
                    'jabatan' => $row[1],
                    'ruangan' => $row[2],
                    'username' => $row[3],
                );

                $this->Admin_model->insert_pegawai($data);
            }

            $this->session->set_flashdata('success', 'Data has been successfully imported.');
            redirect('admin/data_user');
        } else {
            $this->session->set_flashdata('error', 'Invalid file format.');
            redirect('admin/data_user');
        }
    }

	public function download_excel() {
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        // Set properties
        $spreadsheet->getProperties()
                    ->setCreator("Your Name")
                    ->setLastModifiedBy("Your Name")
                    ->setTitle("Template Barang")
                    ->setSubject("Template Barang")
                    ->setDescription("Template Excel for Barang")
                    ->setKeywords("excel phpoffice phpspreadsheet template")
                    ->setCategory("Template");

        // Add data headers
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'Jabatan');
        $sheet->setCellValue('C1', 'Ruangan');
        $sheet->setCellValue('D1', 'Username');

        // Set headers bold
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // Set auto column width
        foreach(range('A','D') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Prepare the Excel file
        $filename = 'template_pegawai.xlsx';
        $writer = new Xlsx($spreadsheet);

        // Save Excel file to php://output (browser)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }


    public function admin_ruangan()
	{
		is_logged_in();
        $data['title'] = 'Admin Ruangan';
		$data['ruangan'] = $this->Admin_model->tampil_data2();
        $this->load->view('templates/header', $data);
        $this->load->view('admin/admin_ruangan/index', $data);
        $this->load->view('templates/footer');
	}

	public function tambah_admin_ruangan()
    {
        is_logged_in();
        $data['judul']="Admin Ruangan";

        
        $this->form_validation->set_rules('ruangan','Ruangan','required|trim');
        $this->form_validation->set_rules('penanggungjawab','Penanggungjawab','required|trim');
        $this->form_validation->set_rules('jabatan','Jabatan','required|trim');

       
        

        if ($this->form_validation->run() == FALSE){
            
        $this->load->view('templates/header',$data);
        $this->load->view('admin/admin_ruangan/tambah',$data);
        $this->load->view('templates/footer');
    }
       else{
            $this->Admin_model->simpan_ruangan();
            redirect('admin/admin_ruangan');
        }
        
    

    }

	public function ubah_ruangan($id= '')
    {
		is_logged_in();
        
        $data['judul'] = "Admin Ruangan";
        $data_ruangan = $this->Admin_model->get_id2($id);
        $data['ubah_ruangan'] = $this->Admin_model->get_id2($id);

        $this->form_validation->set_rules('ruangan','Ruangan','required|trim');
        $this->form_validation->set_rules('penanggungjawab','Penanggungjawab','required|trim');
        $this->form_validation->set_rules('jabatan','Jabatan','required|trim');
        
        

        if ($this->form_validation->run() == FALSE) {
            if ($data_ruangan > 0) {
                $data['ubah_ruangan'] = $this->Admin_model->get_id2($id);
                $this->load->view('templates/header', $data);
                $this->load->view('admin/admin_ruangan/ubah', $data);
                $this->load->view('templates/footer');
            } else {
                $pesan = "Data tidak ditemukan";
                $url = base_url('admin/admin_ruangan');
                echo "<script>
        alert('$pesan');
        location='$url';
    </script>";
            }
        } else {
            $this->Admin_model->ubah_ruangan();
            $pesan = "Data berhasil diupdate";
            $url = base_url('admin/admin_ruangan');
            echo "<script>
            alert('$pesan');
            location='$url';
        </script>";
        }
    }
    
	public function hapus_ruangan($id)
    {
		is_logged_in();
        $this->Admin_model->hapus2($id);
        redirect('admin/admin_ruangan');
    }

}
