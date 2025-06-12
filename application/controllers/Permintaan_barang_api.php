<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/ApiController.php';

use chriskacerguis\RestServer\RestController;

class Permintaan_barang_api extends ApiController
{
	public function __construct()
	{
		parent::__construct();

		header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE,PUT');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            header('Access-Control-Max-Age: 86400'); // Cache preflight response for 24 hours
            exit(0); // Hentikan eksekusi lebih lanjut
        }


		$this->load->model('Permintaan_barang_model');
		$this->load->model('Barang_model');
		$this->load->model('User_model');
		$this->load->model('ruangan_model');
		$this->load->helper('form');
		$this->load->helper('url');
        $this->load->config('rest');
	}
	
	public function transaksi_permintaan_get()
	{

		$permintaan_barang = $this->Permintaan_barang_model->get_grouped_by_date();
		
		if ($permintaan_barang) {
                $this->response([
                    'status' => true,
                    'data' => $permintaan_barang
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                    'data' => []
                ], RESTController::HTTP_OK);
            }
	}
	
	public function permintaan_detail_get($id_ruangan,$tanggal)
	{
	    
	    // Validasi apakah tanggal sesuai format (opsional)
        if (!DateTime::createFromFormat('Y-m-d', $tanggal)) {
            // Jika tidak valid, kembalikan error
            $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], RESTController::HTTP_NOT_FOUND);
        }

		$permintaan_barang = $this->Permintaan_barang_model->get_by_date($id_ruangan,$tanggal);
		
		if ($permintaan_barang) {
                $this->response([
                    'status' => true,
                    'data' => $permintaan_barang
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], RESTController::HTTP_NOT_FOUND);
            }
	}
	
	public function cetak_permintaan_get($id_ruangan,$tanggal)
	{
	    
	    // Validasi apakah tanggal sesuai format (opsional)
        if (!DateTime::createFromFormat('Y-m-d', $tanggal)) {
            // Jika tidak valid, kembalikan error
            $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], RESTController::HTTP_NOT_FOUND);
        }

		$permintaan_barang = $this->Permintaan_barang_model->get_approved_by_date($id_ruangan,$tanggal);
		
		if ($permintaan_barang) {
                $this->response([
                    'status' => true,
                    'data' => $permintaan_barang
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], RESTController::HTTP_NOT_FOUND);
            }
	}

	public function simpan_post()
	{
	    
	    // Ambil data dari request body dalam bentuk JSON
        $data = json_decode($this->input->raw_input_stream, true);
    
        if (!is_array($data) || empty($data)) {
            // Jika data tidak valid, kembalikan error response
            $this->response([
                'status' => 'error',
                'message' => 'Invalid input. Please provide valid JSON data.'
            ], 400);
            return;
        }
        
        $tanggal = date('Y-m-d');
    
        // Loop melalui setiap item dalam data
        foreach ($data as $item) {
            // Validasi setiap item (id_permintaan, status, jumlah harus ada)
            if (!isset($item['jumlah'], $item['id_ruangan'], $item['id_user'])) {
                $this->response([
                    'status' => 'error',
                    'message' => 'Invalid data structure. Each item must contain jumlah, id_ruangan, and id_user.'
                ], 400);
                return;
            }
            
            if ($item['id_barang'] == '0') {
                $data = [
                    'nama_barang' => $item['nama_barang'],
                    'kd_barang' => $item['nama_barang'],
                    'kategori' => $item['kategori'],
                    'satuan' => $item['satuan'],
                    'kategori' => $item['kategori'],
                    'stok' => $item['stok'],
                    ];
                $this->Barang_model->insert_barang($data);
            }
            
            $item['tanggal'] = $tanggal;
			$item['status'] = 'Pending';
			$this->Permintaan_barang_model->insert($item);
    
    
        }
    
        // Response success
        $this->response([
            'status' => 'success',
            'message' => 'Permintaan Berhasil Ditambahkan.'
        ], 200);
	    
	}

    public function update_status_put()
    {
        // Ambil data dari request body dalam bentuk JSON
        $data = json_decode($this->input->raw_input_stream, true);
    
        if (!is_array($data) || empty($data)) {
            // Jika data tidak valid, kembalikan error response
            $this->response([
                'status' => 'Error',
                'message' => 'Invalid input. Please provide valid JSON data.'
            ], 200);
            return;
        }
    
        // Loop melalui setiap item dalam data
        foreach ($data as $item) {
            // Validasi setiap item (id_permintaan, status, jumlah harus ada)
            if (!isset($item['id_permintaan'], $item['status'], $item['jumlah'])) {
                $this->response([
                    'status' => 'Error',
                    'message' => 'Invalid data structure. Each item must contain id_permintaan, status, and jumlah.'
                ], 200);
                return;
            }
    
            $id_permintaan = $item['id_permintaan'];
            $status = $item['status'];
            $jumlah = $item['jumlah'];
    
            // Update status permintaan
            $this->Permintaan_barang_model->update_status($id_permintaan, $status, $jumlah);
    
            if ($status == 'Approved') {
                // Ambil jumlah permintaan dan id_barang
                $jumlah_permintaan = $this->Permintaan_barang_model->get_jumlah_permintaan($id_permintaan);
                $id_barang = $this->Permintaan_barang_model->get_id_barang($id_permintaan);
    
                // Kurangi stok barang
                $barang = $this->Barang_model->get_id($id_barang);
                
                if ($barang['stok'] < $jumlah_permintaan) {
                    $this->response([
                        'status' => 'Error',
                        'message' => 'Jumlah pemintaan melebihi sisa stok'
                    ], 200);
                    return;
                } else {
                    
                    $this->db->trans_begin();
                    
                    $kurangi_stok = $this->Barang_model->kurangi_stok_barang($id_barang, $jumlah_permintaan);
                    
                    if ($kurangi_stok) {
                        
                         $this->db->trans_rollback();
                         
                         $this->response(
                             [
                                 'status' => 'Gagal', 
                                 'message' => 'Gagal mengurangi data stok'
                             ], 200);
                             
                            return;
                    }
                    
                    $updated_barang = $this->Barang_model->get_id($id_barang);
                    
                    $update_transaksi_stok = $this->Permintaan_barang_model->simpan_current_stok($id_permintaan, $updated_barang['stok']);
                    
                    if ($update_transaksi_stok) {
                        
                         $this->db->trans_rollback();
                         
                         $this->response(
                             [
                                 'status' => 'Gagal', 
                                 'message' => 'Gagal update transaksi keluar stok'
                             ], 200);
                             
                            return;
                    }
                    
                    $this->db->trans_commit();
                }
            }
        }
    
        // Response success
        $this->response([
            'status' => 'success',
            'message' => 'Status updated and stock adjusted successfully.'
        ], 200);
    }
    
    

}
