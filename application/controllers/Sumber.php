<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Sumber extends RestController {
    
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
        $data_sumber = $this->Sumber_model->get_sumber();
        
        if ($data_sumber) {
                $this->response([
                    'status' => true,
                    'data' => $data_sumber
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], RESTController::HTTP_NOT_FOUND);
            }
    }

}
