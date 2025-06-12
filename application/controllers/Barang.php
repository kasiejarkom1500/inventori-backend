<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Barang extends CI_Controller
{
	public $Barang_model;
	public $barangModel;
	public $form_validation;
	public $db;
	public $session;


	public function __construct()
	{
		parent::__construct();


		$this->load->model('Barang_model');
		$this->load->helper('form');
		is_logged_in();
	}
	public function index()
	{
		is_logged_in();
		$data['judul'] = "Barang";


		$data['barang'] = $this->Barang_model->tampil_data();

		$this->load->view('templates/header', $data);
		$this->load->view('admin/barang/index', $data);
		$this->load->view('templates/footer');
	}

	public function get_all_barang()
	{
		return $this->db->get('barang')->result();
	}

	public function insert_barang()
	{
		// Check if user is logged in
		is_logged_in();

		// Load the form helper and database library if not already loaded
		$this->load->helper('form');
		$this->load->database();

		$kd_barang = $this->input->post('kd_barang');
		$nama_barang = $this->input->post('nama_barang');
		$kategori = $this->input->post('kategori');
		$satuan = $this->input->post('satuan');
		$stok = $this->input->post('stok');
		$harga_satuan = $this->input->post('harga_satuan');
		// $harga_pengajuan_input = $this->input->post('harga_pengajuan'); // This should be the percentage input

		// // Calculate harga_pengajuan based on the percentage
		// $harga_pengajuan = $harga_satuan * ($harga_pengajuan_input / 100) + $harga_satuan;

		// Prepare data for insertion into barang
		$data_barang = array(
			'kd_barang' => $kd_barang,
			'nama_barang' => $nama_barang,
			'kategori' => $kategori,
			'satuan' => $satuan,
			'stok' => $stok,
			'harga_satuan' => $harga_satuan,
		);

		// Attempt to insert data into 'barang' table
		if ($this->db->insert('barang', $data_barang)) {
			// Get the last inserted ID
			$last_id = $this->db->insert_id();


			// Prepare data for insertion into harga_pengajuan
			$data_harga_pengajuan = array(
				'id_barang' => $last_id,
				'harga_pengajuan' => 0
			);

			$this->Barang_model->insert_harga_pengajuan($data_harga_pengajuan);
			$this->session->set_flashdata('success', 'Data Berhasil Ditambahkan');
			redirect('barang');
		} else {
			// Handle failure of barang insertion
			$error = $this->db->error();
			$this->session->set_flashdata('error', 'Data Gagal Ditambahkan ke barang: ' . $error['message']);
			redirect('barang');
		}
	}


	


	public function tambah()
	{
		is_logged_in();
		$data['judul'] = "Barang";
		$this->form_validation->set_rules('kd_barang', 'Kd Barang', 'required|trim|is_unique[barang.kd_barang]');
		$this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim');
		$this->form_validation->set_rules('kategori', 'Jenis Barang', 'required|trim');
		$this->form_validation->set_rules('satuan', 'Satuan', 'required|trim');
		$this->form_validation->set_rules('stok', 'Stok', 'required|trim');
		$this->form_validation->set_rules('harga_satuan', 'Harga', 'required|trim');
		$harga = $this->db->get('harga_pengajuan')->result();
		$data['barang'] = $this->Barang_model->get_all_barang();
		$data['harga'] = $harga;

		if ($this->form_validation->run() == FALSE) {

			$this->load->view('templates/header', $data);
			$this->load->view('admin/barang/tambah', $data);
			$this->load->view('templates/footer');
		} else {
			$this->Barang_model->simpan();
			redirect('barang');
		}
	}


	public function hapus($id)
	{
		is_logged_in();

		$this->Barang_model->hapus($id);
		redirect('barang');
	}



	public function ubah($id = '')
	{
		is_logged_in();

		$data['judul'] = "Barang";
		$data_barang = $this->Barang_model->get_id($id);
		$data['ubah_barang'] = $this->Barang_model->get_id($id);

		$this->form_validation->set_rules('kd_barang', 'Kd Barang', 'required|trim');
		$this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim');
		$this->form_validation->set_rules('kategori', 'Jenis Barang', 'required|trim');
		$this->form_validation->set_rules('satuan', 'Satuan', 'required|trim');
		$this->form_validation->set_rules('stok', 'Stok', 'required|trim');
		$this->form_validation->set_rules('harga_satuan', 'Harga', 'required|trim');
		$this->form_validation->set_rules('id_harga_pengajuan', 'Harga');


		if ($this->form_validation->run() == FALSE) {
			if ($data_barang > 0) {
				$data['ubah_barang'] = $this->Barang_model->get_id($id);
				$this->load->view('templates/header', $data);
				$this->load->view('admin/barang/ubah', $data);
				$this->load->view('templates/footer');
			} else {
				$pesan = "Data tidak ditemukan";
				$url = base_url('barang');
				echo "<script>
        alert('$pesan');
        location='$url';
    </script>";
			}
		} else {
			$this->Barang_model->ubah();
			$pesan = "Data berhasil diupdate";
			$url = base_url('barang');
			echo "<script>
            alert('$pesan');
            location='$url';
        </script>";
		}
	}


	public function update_harga_pengajuan()
	{

		$harga_pengajuan = $this->input->post('harga_pengajuan');
		$this->Barang_model->update_harga_pengajuan($harga_pengajuan);

		redirect('barang');
	}


	public function excel()
	{
		$file_mimes = array(
			'application/vnd.ms-excel',
			'text/plain',
			'text/csv',
			'text/tsv',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
		);

		if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {

			$inputFileName = $_FILES['file']['tmp_name'];

			try {
				$spreadsheet = IOFactory::load($inputFileName);
			} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
				die('Error loading file: ' . $e->getMessage());
			}

			$sheetData = $spreadsheet->getActiveSheet()->toArray();

			// Extract Kd Barang column
			$kd_barang = array_column($sheetData, 0);

			// Check for duplicate values in the uploaded file
			if (count($kd_barang) !== count(array_unique($kd_barang))) {
				$this->session->set_flashdata('error', 'Duplicate Kd Barang found in the uploaded file.');
				redirect('barang');
			}

			foreach ($sheetData as $key => $row) {
				if ($key == 0) continue; // Skip the header row

				// Check if kd_barang already exists in the database
				if ($this->Barang_model->exists_kd_barang($row[0])) {
					$this->session->set_flashdata('error', 'Kd Barang ' . $row[0] . ' already exists in the database.');
					redirect('barang');
				}

				$data_barang = array(
					'kd_barang' => $row[0],
					'nama_barang' => $row[1],
					'kategori' => $row[2],
					'satuan' => $row[3],
					'stok' => $row[4],
					'harga_satuan' => $row[5],
				);

				// Insert data into barang table
				$this->Barang_model->insert_barang($data_barang);

				// Get the inserted id_barang
				$id_barang = $this->db->insert_id();

				// Insert data into harga_pengajuan table
				$data_harga_pengajuan = array(
					'id_barang' => $id_barang,
					'harga_pengajuan' => 0
				);

				$this->Barang_model->insert_harga_pengajuan($data_harga_pengajuan);
			}

			$this->session->set_flashdata('success', 'Data has been successfully imported.');
			redirect('barang');
		} else {
			$this->session->set_flashdata('error', 'Invalid file format.');
			redirect('barang');
		}
	}


	public function download_excel()
	{
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
		$sheet->setCellValue('A1', 'Kd Barang');
		$sheet->setCellValue('B1', 'Nama Barang');
		$sheet->setCellValue('C1', 'Jenis Barang');
		$sheet->setCellValue('D1', 'Satuan');
		$sheet->setCellValue('E1', 'Stok');
		$sheet->setCellValue('F1', 'Harga Satuan');
		// $sheet->setCellValue('G1', 'Harga Pengajuan');

		// Set headers bold
		$sheet->getStyle('A1:F1')->getFont()->setBold(true);

		// Set auto column width
		foreach (range('A', 'F') as $columnID) {
			$sheet->getColumnDimension($columnID)->setAutoSize(true);
		}
		// Prepare the Excel file
		$filename = 'template_barang.xlsx';
		$writer = new Xlsx($spreadsheet);

		// Save Excel file to php://output (browser)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}
}
