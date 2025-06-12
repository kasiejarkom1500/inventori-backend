<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barang_masuk extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        
        $this->load->model('Barang_masuk_model');
		$this->load->model('Barang_model');
		is_logged_in();
    }
    public function index()
    {
        is_logged_in();
        $data['judul'] = "Barang Masuk";
        

        $data['data_barang_masuk'] = $this->Barang_masuk_model->tampil_data();
       
        $this->load->view('templates/header', $data);
        $this->load->view('admin/barang_masuk/index', $data);
        $this->load->view('templates/footer');
    }

	public function tambah()
    {
        is_logged_in();
        $data['judul']="Barang Masuk";

		
        $this->form_validation->set_rules('tgl_masuk','Tanggal Masuk','required|trim');
        $this->form_validation->set_rules('id_barang','Data Barang','required|trim');
		$this->form_validation->set_rules('jumlah','Jumlah Barang','required|trim');
       
       
        

        if ($this->form_validation->run() == FALSE){
            
        $this->load->view('templates/header',$data);
        $this->load->view('admin/barang_masuk/tambah',$data);
        $this->load->view('templates/footer');
    }
       else{
            $this->Barang_masuk_model->simpan();
            redirect('barang_masuk');
        }
        
    

    }

	public function get_kode_barang() {
        $term = $this->input->get('q');
        $results = $this->Barang_masuk_model->search_barang($term);
        
        echo json_encode($results);
    }

	public function get_barang_details()
    {
		$id_barang = $this->input->post('id_barang');
        $data = $this->Barang_masuk_model->get_barang_details($id_barang);
        if ($data) {
			echo json_encode($data);
		} else {
			echo json_encode(['error' => 'Data not found']); // Menampilkan pesan error jika data tidak ditemukan
		}
    }

	public function hapus($id)
    {
		is_logged_in();
        $this->Barang_masuk_model->hapus($id);
        redirect('barang_masuk');
    }

}
