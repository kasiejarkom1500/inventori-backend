<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';
require_once APPPATH . 'core/ApiController.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use chriskacerguis\RestServer\RestController;

class Dashboard_api extends ApiController
{
	public $Barang_model;
	public $barangModel;
	public $form_validation;
	public $db;
	public $session;


	public function __construct()
	{
		parent::__construct();
		
		header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE,PUT');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            header('Access-Control-Max-Age: 86400'); // Cache preflight response for 24 hours
            exit(0); // Hentikan eksekusi lebih lanjut
        }



		$this->load->model('Barang_model');
		$this->load->helper('form');
		$this->load->helper('url');
        $this->load->config('rest');
        // is_logged_in();
	}

    public function total_barang_get($kategori)
    {
        $barang = $this->Barang_model->get_sum($kategori);
		if ($barang) {
                $this->response([
                    'status' => true,
                    'total_item' => $barang
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], RESTController::HTTP_OK);
            }
    }
    
    public function total_stok_get($kategori)
    {
        $barang = $this->Barang_model->get_sum_stok($kategori);
		if ($barang) {
                $this->response([
                    'status' => true,
                    'total_stok_tersedia' => $barang
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], RESTController::HTTP_OK);
            }
    }
    
    
}
