<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_pengajuan extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        is_logged_in();
        $this->load->model('Laporan_pengajuan_model');
    }

    public function index()
    {
       
		$data['pengajuan'] = [];
        $data['tgl_mulai'] = '';
        $data['tgl_akhir'] = '';
        $this->load->view('templates/header2');
        $this->load->view('admin_pengajuan/laporan_pengajuan/index', $data);
        $this->load->view('templates/footer');
    }

    public function filter()
    {
        $tgl_mulai = $this->input->post('tgl_mulai');
        $tgl_akhir = $this->input->post('tgl_akhir');
        $data['pengajuan'] = $this->Laporan_pengajuan_model->get_approved_pengajuan($tgl_mulai, $tgl_akhir);
		$data['tgl_mulai'] = $tgl_mulai;
        $data['tgl_akhir'] = $tgl_akhir;
        $this->load->view('templates/header2');
        $this->load->view('admin_pengajuan/laporan_pengajuan/index', $data);
        $this->load->view('templates/footer');
    }

    public function cetak()
    {
		$tgl_mulai = $this->input->get('tgl_mulai');
        $tgl_akhir = $this->input->get('tgl_akhir');

        $data['pengajuan'] = $this->Laporan_pengajuan_model->get_approved_pengajuan_groupid($tgl_mulai, $tgl_akhir);
        
        foreach ($data['pengajuan'] as &$barang) {
            if (!($barang['stok'] + $barang['jumlah'] > $barang['stok_dasar'])) {
                $barang['jumlah'] = $barang['stok_dasar'] - $barang['stok'];
            }
        }

        $this->load->view('admin_pengajuan/laporan_pengajuan/cetak_laporan_pengajuan', $data);
    }

	public function lap_pengajuan()
    {
       
		$data['pengajuan'] = [];
        $data['tgl_mulai'] = '';
        $data['tgl_akhir'] = '';
        $this->load->view('templates/header');
        $this->load->view('admin/laporan_pengajuan/index', $data);
        $this->load->view('templates/footer');
    }

    public function filter_pengajuan()
    {
        $tgl_mulai = $this->input->post('tgl_mulai');
        $tgl_akhir = $this->input->post('tgl_akhir');
        $data['pengajuan'] = $this->Laporan_pengajuan_model->get_approved_pengajuan($tgl_mulai, $tgl_akhir);
		$data['tgl_mulai'] = $tgl_mulai;
        $data['tgl_akhir'] = $tgl_akhir;
        $this->load->view('templates/header');
        $this->load->view('admin/laporan_pengajuan/index', $data);
        $this->load->view('templates/footer');
    }

    public function cetak_pengajuan()
    {
		$tgl_mulai = $this->input->get('tgl_mulai');
        $tgl_akhir = $this->input->get('tgl_akhir');

        $data['pengajuan'] = $this->Laporan_pengajuan_model->get_approved_pengajuan($tgl_mulai, $tgl_akhir);
        $this->load->view('admin/laporan_pengajuan/cetak_laporan_pengajuan', $data);
    }
	

}
