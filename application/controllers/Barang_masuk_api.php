<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/ApiController.php';

use chriskacerguis\RestServer\RestController;

class Barang_masuk_api extends ApiController

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

        
        
        $this->load->model('Barang_masuk_model');
		$this->load->model('Barang_model');
		
		$this->load->helper('form');
		$this->load->helper('url');
        $this->load->config('rest');
        
    }
    
    public function all_barang_masuk_get() {
        try {
            // Ambil data barang masuk dari model
            $data_barang_masuk = $this->Barang_masuk_model->tampil_data();
            var_dump($data_barang_masuk);

            // Periksa apakah data ditemukan
            if ($data_barang_masuk) {
                $this->response([
                    'status' => true,
                    'data' => $data_barang_masuk
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data barang masuk tidak ditemukan'
                ], RESTController::HTTP_OK);
            }
        } catch (Exception $e) {
            // Tangani kesalahan yang tidak terduga
            $this->response([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], RESTController::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    
    public function all_pembelian_get() {
        try {
            // Ambil data barang masuk dari model
            $data_barang_masuk = $this->Barang_masuk_model->data_pembelian();

            // Periksa apakah data ditemukan
            if ($data_barang_masuk) {
                $this->response([
                    'status' => true,
                    'data' => $data_barang_masuk
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data barang masuk tidak ditemukan'
                ], RESTController::HTTP_OK);
            }
        } catch (Exception $e) {
            // Tangani kesalahan yang tidak terduga
            $this->response([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], RESTController::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function detail_pembelian_get($id_pembelian) {
        try {
            // Ambil data barang masuk dari model
            $data_barang_masuk = $this->Barang_masuk_model->detail_pembelian($id_pembelian);

            // Periksa apakah data ditemukan
            if ($data_barang_masuk) {
                $this->response([
                    'status' => true,
                    'data' => $data_barang_masuk
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data barang masuk tidak ditemukan'
                ], RESTController::HTTP_OK);
            }
        } catch (Exception $e) {
            // Tangani kesalahan yang tidak terduga
            $this->response([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], RESTController::HTTP_INTERNAL_SERVER_ERROR);
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
            ], 200);
            return;
        }
        
        $tanggal = date('Y-m-d');
        
    
        // Loop melalui setiap item dalam data
        foreach ($data as $item) {
            // Validasi setiap item (id_permintaan, status, jumlah harus ada)
            if (!isset($item['no_kuitansi'], $item['id_sumber'], $item['barang'])) {
                $this->response([
                    'status' => 'Error',
                    'message' => 'Invalid data structure. Each item must contain no_kuitansi, id_sumber, and barang.'
                ], 200);
                return;
            }
            
            $data_pembelian = [
                'no_kuitansi' => $item['no_kuitansi'],
                'id_sumber' => $item['id_sumber'],
                'tgl_masuk' => $item['tgl_masuk']
                ];
                
            // Memulai transaksi
            $this->db->trans_begin();

            
            $id_pembelian = $this->Barang_masuk_model->simpan_pembelian($data_pembelian);
            
             if ($id_pembelian < 0) {
                 // Jika penyimpanan pembelian gagal, rollback dan hentikan proses
                 $this->db->trans_rollback();
                 
                 $this->response(
                     [
                         'status' => 'Gagal', 
                         'message' => 'Gagal menyimpan data pembelian'
                     ], 200);
                     
                    return;
                 
             }

            foreach ($item['barang'] as $barang) {
                $data = [
                    'tgl_masuk' => $item['tgl_masuk'],
                    'id_pembelian' => $id_pembelian,
                    'id_barang' => $barang['id_barang'],
                    'jumlah' => $barang['jumlah'],
                    'harga' => $barang['harga'],
                    'sisa_stok' => $barang['jumlah'],
                    'catatan' => $barang['catatan'] ?? null,
                    ];
                    
                $result = $this->Barang_masuk_model->simpan($data);
                
                // var_dump($result);
                
                if (!$result > 0) {
    
                    $this->db->trans_rollback();
                    
                    $this->response(
                         [
                             'status' => false, 
                             'message' => 'Gagal menyimpan data barang'
                         ], 200);
                         
                        return;
                 }
                 
                $barang_db = $this->Barang_model->get_id($barang['id_barang']);
                
                // var_dump($barang_db);
                // die;
                
                $data_barang = [
                    'kd_barang' => $barang_db['kd_barang'],
                    'stok' => $barang_db['stok'] + $barang['jumlah'],
                    'harga_satuan' => $barang['harga']
                    ];
                
                $result_barang = $this->Barang_model->ubah_api($barang['id_barang'],$data_barang);
                
                 if (!$result_barang) {
    
                    $this->db->trans_rollback();
                    
                    $this->response(
                         [
                             'status' => false, 
                             'message' => 'Gagal update stok barang'
                         ], 200);
                         
                        return;
                 }
                 
                //  $bm_db = $this->Barang_masuk_model->get_id($result);
                
                $updated_barang = $this->Barang_model->get_id($barang['id_barang']);
                
                $data_bm =[
                        'current_stok' => $updated_barang['stok']
                    ];
                    
                // var_dump($data_bm);
                 
                 $barang_masuk = $this->Barang_masuk_model->ubah_api($result,$data_bm);
                 
                //  var_dump($barang_masuk);
                
                 if (!$barang_masuk) {
    
                    $this->db->trans_rollback();
                    
                    $this->response(
                         [
                             'status' => false, 
                             'message' => 'Gagal update transaksi stok di barang masuk'
                         ], 200);
                         
                        return;
                 }
            }
    
            $this->db->trans_commit();
        
        }
        
        
    
        // Response success
        $this->response([
            'status' => 'success',
            'message' => 'Pembelian Berhasil Ditambahkan.'
        ], 200);
	    
	}



	public function get_kode_barang() {
        $term = $this->input->get('q');
        $results = $this->Barang_masuk_model->search_barang($term);
        
        echo json_encode($results);
    }

	public function barang_details_get()
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
