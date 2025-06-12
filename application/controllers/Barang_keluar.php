<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barang_keluar extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        
        $this->load->model('Barang_keluar_model');
		$this->load->model('Barang_model');
        $this->load->model('ruangan_model');
		is_logged_in();
    }
    public function index()
    {
        is_logged_in();
        $data['judul'] = "Barang Keluar";
        

        $data['data_barang_keluar'] = $this->Barang_keluar_model->tampil_data();
       
        $this->load->view('templates/header', $data);
        $this->load->view('admin/barang_keluar/index', $data);
        $this->load->view('templates/footer');
    }

	


}
