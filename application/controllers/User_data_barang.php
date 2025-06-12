<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_Data_Barang extends CI_Controller
{
	public $db;
	public $session;
	public $BarangModel;

	public function __construct()
	{
		parent::__construct();
		is_logged_in();

		$this->load->model('Barang_model', 'BarangModel');
	}

	public function index()
	{
		is_logged_in();
		$data['data_barang'] = $this->BarangModel->tampil_data();
		$this->load->view('templates/header1');
		$this->load->view('user/data_barang/index', $data);
		$this->load->view('templates/footer');
	}
}
