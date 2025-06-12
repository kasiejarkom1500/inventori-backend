<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Permintaan_barang extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();


		$this->load->model('Permintaan_barang_model');
		$this->load->model('Barang_model');
		$this->load->model('User_model');
		$this->load->model('ruangan_model');

		is_logged_in();
	}
	public function index()
	{
		is_logged_in();
		$data['judul'] = "Permintaan Barang";


		$data['permintaan_barang'] = $this->Permintaan_barang_model->get_grouped_by_date();
		$data['ruangan'] = $this->ruangan_model->get_ruangan();
		

		$this->load->view('templates/header1', $data);
		$this->load->view('user/permintaan_barang/index', $data);
		$this->load->view('templates/footer');
	}

	public function tambah()
	{
		is_logged_in();
		$data['judul'] = "Permintaan Barang";
		$username = $this->session->userdata('username');
		$data['user'] = $this->User_model->get_user($username);
		$data['barang'] = $this->Barang_model->get_all_barang();
		$data['ruangan'] = $this->ruangan_model->get_ruangan();
		$data['tanggal'] = date('Y-m-d');
		$data['user_requests'] = $this->Permintaan_barang_model->get_user_requests($this->session->userdata('id_user'));

		$this->load->view('templates/header1', $data);
		$this->load->view('user/permintaan_barang/tambah', $data);
		$this->load->view('templates/footer');
	}

	public function simpan()
	{
		try {
			//code...
			is_logged_in();
			$data = $this->input->post('data');
			$tanggal = date('Y-m-d');

			foreach ($data as $item) {
				$item['tanggal'] = $tanggal;
				$item['status'] = 'Pending';
				$this->Permintaan_barang_model->insert($item);
			}
		} catch (\Throwable $th) {
			echo json_encode([$th->getMessage()]);
		}
	}

	public function detail($tanggal, $id_user)
	{
		$data['detail'] = $this->Permintaan_barang_model->get_by_date_and_user($tanggal, $id_user);
		$data['tanggal'] = $tanggal;
		$data['id_user'] = $id_user;
		$data['ruangan'] = $this->ruangan_model->get_ruangan();
		$this->load->view('templates/header1', $data);
		$this->load->view('user/permintaan_barang/detail', $data);
		$this->load->view('templates/footer');
	}
	public function hapus($id)
	{
		is_logged_in();
		$this->Barang_keluar_model->hapus($id);
		redirect('barang_keluar');
	}


	public function data_permintaan()
	{
		is_logged_in();
		$data['judul'] = "Barang Permintaan";


		$data['permintaan'] = $this->Permintaan_barang_model->tampil_data();
		$data['ruangan'] = $this->ruangan_model->get_ruangan();

		$this->load->view('templates/header', $data);
		$this->load->view('admin/permintaan_barang/index', $data);
		$this->load->view('templates/footer');
	}

	public function update_status()
	{
		$id_permintaan = $this->input->post('id_permintaan');
		$status = $this->input->post('status');
		$jumlah = $this->input->post('jumlah');


		// Update status permintaan
		$this->Permintaan_barang_model->update_status($id_permintaan, $status  , $jumlah);

		if ($status == 'Approved') {
			$jumlah = $this->Permintaan_barang_model->get_jumlah_permintaan($id_permintaan);
			$id_barang = $this->Permintaan_barang_model->get_id_barang($id_permintaan);

			// Kurangi stok barang
			$this->Barang_model->kurangi_stok_barang($id_barang, $jumlah);
		}
		// Response success
		echo json_encode(['status' => 'success']);
	}
}
