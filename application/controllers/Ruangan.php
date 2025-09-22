<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/ApiController.php';

use chriskacerguis\RestServer\RestController;

class Ruangan extends ApiController {
    
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
        
        
        $this->load->model('Ruangan_model');
        
        $this->load->helper('form');
		$this->load->helper('url');
        $this->load->config('rest');
    }

	
	public function ruangan_all_get($kabupaten_id)
    {
        $data_ruangan = $this->Ruangan_model->get_all($kabupaten_id);
        
        if ($data_ruangan) {
                $this->response([
                    'status' => true,
                    'data' => $data_ruangan
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], RESTController::HTTP_OK);
            }
    }

}
