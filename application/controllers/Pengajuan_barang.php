<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengajuan_barang extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();


		$this->load->model('Pengajuan_barang_model');
		$this->load->model('Barang_model');
		$this->load->model('User_model');
		$this->load->model('ruangan_model');

		is_logged_in();
	}
	public function index()
	{
		is_logged_in();
		$data['judul'] = "Permintaan Barang";


		$data['pengajuan_barang'] = $this->Pengajuan_barang_model->get_grouped_by_date();

		$this->load->view('templates/header1', $data);
		$this->load->view('user/pengajuan_barang/index', $data);
		$this->load->view('templates/footer');
	}

	public function tambah()
	{
		is_logged_in();
		$data['judul'] = "Pengajuan Barang";
		$username = $this->session->userdata('username');
		$data['user'] = $this->User_model->get_user($username);
		$data['barang'] = $this->Barang_model->get_all_barang();
		$data['ruangan'] = $this->ruangan_model->get_ruangan();

		$data['tanggal'] = date('Y-m-d');

		$this->load->view('templates/header1', $data);
		$this->load->view('user/pengajuan_barang/tambah', $data);
		$this->load->view('templates/footer');
	}

	public function simpan()
	{
		is_logged_in();

		$data = $this->input->post('data');
		$tanggal = date('Y-m-d');

		$data['tanggal'] = $tanggal;
		$data['status'] = 'Pending';
		$this->Pengajuan_barang_model->insert($data);


		echo json_encode(['status' => 'success']);
	}

	public function detail($tanggal, $id_user)
	{
		$data['detail'] = $this->Pengajuan_barang_model->get_by_date_and_user($tanggal, $id_user);
		$data['tanggal'] = $tanggal;
		$data['id_user'] = $id_user;
		$this->load->view('templates/header1', $data);
		$this->load->view('user/pengajuan_barang/detail', $data);
		$this->load->view('templates/footer');
	}
	public function hapus($id)
	{
		is_logged_in();
		$this->Barang_keluar_model->hapus($id);
		redirect('barang_keluar');
	}


	public function data_pengajuan()
	{
		is_logged_in();
		$data['judul'] = "Barang Pengajuan";


		$data['pengajuan'] = $this->Pengajuan_barang_model->tampil_data();

		$this->load->view('templates/header2', $data);
		$this->load->view('admin_pengajuan/pengajuan_barang/index', $data);
		$this->load->view('templates/footer');
	}


	public function data_pengajuan_update()
	{
		$id_pengajuan = $this->input->post('id_pengajuan');
		$id_barang = $this->input->post('id_barang');
		$jumlah = $this->input->post('jumlah');
		$status = $this->input->post('status');

		if (empty($id_pengajuan) || empty($status) || empty($jumlah) || empty($id_barang)) {
			$response = array('status' => 'error', 'message' => 'Missing data.');
			echo json_encode($response);
			return;
		}

		$result = $this->db->where('id_barang', $id_barang)
		->get('harga_pengajuan')
		->result();


		$hargaPengajuan = $result[0]->harga_pengajuan;

		$total = $hargaPengajuan * $jumlah;

		$update_data = array(
			'jumlah' => $jumlah,
			'status' => $status,
			'total_harga' => $total
		);

		// Update pengajuan
		$result_update = $this->Pengajuan_barang_model->update_pengajuan($id_pengajuan, $update_data);

		if ($result_update) {
			// Prepare data for insert into barang_masuk
			if ($status == 'Approved') {
				$data_barang_masuk = array(
					'tgl_masuk' => date('Y-m-d'),
					'id_barang' => $id_barang,
					'jumlah' => $jumlah
				);

				// Insert into barang_masuk and update stock
				$result_insert = $this->Pengajuan_barang_model->insert_barang_masuk($data_barang_masuk);

				if ($result_insert) {
					$response = array('status' => 'success', 'message' => 'Data updated and stock adjusted successfully.');
				} else {
					$response = array('status' => 'error', 'message' => 'Failed to insert into barang_masuk or update stock.');
				}
			}else{
				$response = array('status' => 'success', 'message' => 'Data updated.');
			}
		} else {
			$response = array('status' => 'error', 'message' => 'Failed to update pengajuan.');
		}

		echo json_encode($response);
	}

	public function update_status()
	{
		$id_pengajuan = $this->input->post('id_pengajuan');
		$status = $this->input->post('status');



		$this->Pengajuan_barang_model->update_status($id_pengajuan, $status);

		if ($status == 'Approved') {
			$jumlah = $this->Pengajuan_barang_model->get_jumlah_pengajuan($id_pengajuan);
			$id_barang = $this->Pengajuan_barang_model->get_id_barang($id_pengajuan);

			// Kurangi stok barang
			$this->Barang_model->tambah_stok_barang($id_barang, $jumlah);
		}
		// Response success
		echo json_encode(['status' => 'success']);
	}
}
