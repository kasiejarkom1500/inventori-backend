<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_stok extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        is_logged_in();
        $this->load->model('Laporan_stok_model');
        $this->load->model('Admin_model');
        $this->load->model('Barang_model');
        $this->load->model('Ruangan_model');

    }

    // public function laporan_permintaan()
    // {
    //     $data['permintaan'] = $this->Barang_model->get_all_permintaan();
    //     $this->load->view('laporan_permintaan', $data);
    // }

    public function index()
    {
       
		$data['permintaan'] = [];
        $data['id_barang'] = '';
        $data['id_ruangan'] = '';
        $data['permintaan'] = $this->Laporan_stok_model->tampil_data();
		$data['ruangan'] = $this->Ruangan_model->get_ruangan();
        $this->load->view('templates/header');
        $this->load->view('admin/laporan_stok/index', $data);
        $this->load->view('templates/footer');
    }

    public function filter()
    {
        $id_barang = $this->input->post('jenis_barang');
        $id_ruangan = $this->input->post('id_ruangan');
        $nama = $this->input->post('nama_barang');


		$data['ruangan'] = $this->Ruangan_model->get_ruangan();
		$data['permintaan'] = $this->Laporan_stok_model->filterByRuangan($id_barang , $id_ruangan , $nama);
		$data['kategori'] = $id_barang;
		$data['ruangan_id'] = $id_ruangan;
		$data['nama_barang'] = $nama;

        $this->load->view('templates/header');
        $this->load->view('admin/laporan_stok/index', $data);
        $this->load->view('templates/footer');
    }

    public function cetak()
    {
		$id_barang = $this->input->post('kategori');
        $id_ruangan = $this->input->post('id_ruangan');
        $nama = $this->input->post('nama_barang');
		

        $data['permintaan'] = $this->Laporan_stok_model->filterByRuangan($id_barang, $id_ruangan , $nama);
        $this->load->view('admin/laporan_stok/cetak_laporan_stok', $data);
    }

}
