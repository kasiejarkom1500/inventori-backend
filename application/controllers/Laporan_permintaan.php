<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_permintaan extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        is_logged_in();
        $this->load->model('Laporan_permintaan_model');
        $this->load->model('Admin_model');
        $this->load->model('Permintaan_barang_model');

    }

    // public function laporan_permintaan()
    // {
    //     $data['permintaan'] = $this->Barang_model->get_all_permintaan();
    //     $this->load->view('laporan_permintaan', $data);
    // }

    public function index()
    {
       
		$data['permintaan'] = [];
        $data['tgl_mulai'] = '';
        $data['tgl_akhir'] = '';
        // $data['id_ruangan'] = '';
        $data['permintaan'] = $this->Permintaan_barang_model->tampil_data();
		// $data['ruangan'] = $this->Ruangan_model->get_ruangan();
        $this->load->view('templates/header');
        $this->load->view('admin/laporan_permintaan/index', $data);
        $this->load->view('templates/footer');
    }

    public function filter()
    {
        $tgl_mulai = $this->input->post('tgl_mulai');
        $tgl_akhir = $this->input->post('tgl_akhir');
        // $id_ruangan = $this->input->post('id_ruangan');

        $data['permintaan'] = $this->Laporan_permintaan_model->get_approved_permintaan($tgl_mulai, $tgl_akhir);
		$data['tgl_mulai'] = $tgl_mulai;
        $data['tgl_akhir'] = $tgl_akhir;
        // $data['id_ruangan'] = $id_ruangan;

        // $data['ruangan'] = $this->Ruangan_model->get_ruangan();
        $this->load->view('templates/header');
        $this->load->view('admin/laporan_permintaan/index', $data);
        $this->load->view('templates/footer');
    }

    public function cetak()
    {
		$tgl_mulai = $this->input->get('tgl_mulai');
        $tgl_akhir = $this->input->get('tgl_akhir');
        // $id_ruangan = $this->input->get('id_ruangan');


        $data['permintaan'] = $this->Laporan_permintaan_model->get_approved_permintaan($tgl_mulai, $tgl_akhir);
        $this->load->view('admin/laporan_permintaan/cetak_laporan_permintaan', $data);
    }

}
