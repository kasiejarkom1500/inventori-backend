<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/ApiController.php';

use chriskacerguis\RestServer\RestController;

class Sumber extends ApiController {
    
    public function __construct(){
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
        
        
        $this->load->model('Sumber_model');
        
        $this->load->helper('form');
		$this->load->helper('url');
        $this->load->config('rest');
    }

	
	public function sumber_all_get()
    {
        $data_sumber = $this->Sumber_model->get_sumber($this->decoded_token['id_satker']);
        
        if ($data_sumber) {
                $this->response([
                    'status' => true,
                    'data' => $data_sumber
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], RESTController::HTTP_OK);
            }
    }

    public function add_sumber_post()
    {
        $nama_sumber = $this->post('nama_sumber');
        $id_satker = $this->decoded_token['id_satker'];

        if (empty($nama_sumber)) {
            return $this->response([
                'status' => false,
                'message' => 'nama_sumber wajib diisi'
            ], RestController::HTTP_OK);
        }

        $insert = $this->Sumber_model->insert_sumber([
            'nama_sumber' => $nama_sumber,
            'id_satker' => $id_satker,
        ]);

        if ($insert) {
            return $this->response([
                'status' => true,
                'message' => 'Sumber berhasil ditambahkan'
            ], RestController::HTTP_OK);
        }

        return $this->response([
            'status' => false,
            'message' => 'Gagal menambahkan sumber'
        ], RestController::HTTP_OK);
    }

    public function update_sumber_post()
    {
        $id_sumber = $this->post('id_sumber');
        $nama_sumber = $this->post('nama_sumber');
        $id_satker = $this->decoded_token['id_satker'];

        if (empty($id_sumber) || empty($nama_sumber)) {
            return $this->response([
                'status' => false,
                'message' => 'id_sumber dan nama_sumber wajib diisi'
            ], RestController::HTTP_OK);
        }

        $update = $this->Sumber_model->update_sumber($id_sumber, $id_satker, [
            'nama_sumber' => $nama_sumber
        ]);

        if ($update) {
            return $this->response([
                'status' => true,
                'message' => 'Sumber berhasil diperbarui'
            ], RestController::HTTP_OK);
        }

        return $this->response([
            'status' => false,
            'message' => 'Gagal memperbarui sumber'
        ], RestController::HTTP_OK);
    }

    public function delete_sumber_delete($id_sumber)
    {
        $id_satker = $this->decoded_token['id_satker'];

        if (empty($id_sumber)) {
            return $this->response([
                'status' => false,
                'message' => 'id_sumber wajib diisi'
            ], RestController::HTTP_OK);
        }

        $delete = $this->Sumber_model->delete_sumber($id_sumber, $id_satker);

        if ($delete) {
            return $this->response([
                'status' => true,
                'message' => 'Sumber berhasil dihapus'
            ], RestController::HTTP_OK);
        }

        return $this->response([
            'status' => false,
            'message' => 'Gagal menghapus sumber'
        ], RestController::HTTP_OK);
    }

}
