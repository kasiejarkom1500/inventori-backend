<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';
require_once APPPATH . 'core/ApiController.php';


use chriskacerguis\RestServer\RestController;

class Dashboard_api extends ApiController
{
	public $Barang_model;
	public $barangModel;
	public $form_validation;
	public $db;
	public $session;
    public $satker;


    public function __construct()
    {
        parent::__construct();

        $this->satker = $this->decoded_token['id_satker'];
        
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
        $this->load->model('Permintaan_barang_model');
        $this->load->model('Barang_masuk_model');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->config('rest');
        // is_logged_in();
    }

    public function total_barang_get($kategori = null)
    {
        if ($kategori === null) {
            $this->response([
                'status' => false,
                'message' => 'Parameter kategori diperlukan.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }

        $barang = $this->Barang_model->get_sum($kategori, $this->decoded_token['id_satker']);
        // Ensure $barang is treated as a number, it might be a string from DB
        $total_item = $barang !== null ? (int)$barang : 0;

		if ($barang) {
                $this->response([
                    'status' => true,
                    'total_item' => $barang
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                    'total_item' => 0
                ], RESTController::HTTP_OK);
            }
    }
    
    public function total_stok_get($kategori = null)
    {
        if ($kategori === null) {
            $this->response([
                'status' => false,
                'message' => 'Parameter kategori diperlukan.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }

        $barang = $this->Barang_model->get_sum_stok($kategori, $this->decoded_token['id_satker']);
        // Ensure $barang is treated as a number, it might be a string from DB
        $total_stok_tersedia = $barang !== null ? (int)$barang : 0;
		if ($barang) {
                $this->response([
                    'status' => true,
                    'total_stok_tersedia' => $total_stok_tersedia
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                    'total_stok_tersedia' => 0
                ], RESTController::HTTP_OK);
            }
    }

    public function jumlah_permintaan_get($bulanTahun = null)
	{
        if ($bulanTahun === null) {
            $this->response([
                'status' => false,
                'message' => 'Parameter bulan dan tahun (YYYY-MM) diperlukan.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }
        // Validasi format YYYY-MM
        if (!preg_match('/^\d{4}-\d{2}$/', $bulanTahun) || !DateTime::createFromFormat('Y-m', $bulanTahun)) {
            $this->response([
                'status' => false,
                'message' => 'Format parameter tidak valid. Gunakan YYYY-MM.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }

		$permintaan_barang = $this->Permintaan_barang_model->get_grouped_filter_by_date($bulanTahun, $this->decoded_token['id_satker']);
		
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

    public function total_pemakaian_bulanan_get($tahun = null, $kategori = null)
    {
        if ($tahun === null || $kategori === null) {
            $this->response([
                'status' => false,
                'message' => 'Parameter tahun (YYYY) dan kategori diperlukan.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }

        // Validasi format YYYY
        if (!preg_match('/^\d{4}$/', $tahun) || !DateTime::createFromFormat('Y', $tahun)) {
            $this->response([
                'status' => false,
                'message' => 'Format parameter tahun tidak valid. Gunakan YYYY.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }

		$pemakaian_barang = $this->Permintaan_barang_model->get_pemakaian_bulanan($tahun, $kategori);
        // jadikan bulan dan tahun yang terpisah menjadi format YYYY-MM
        if (is_array($pemakaian_barang)) {
            foreach ($pemakaian_barang as &$item) {
                $bulanTahun = $item['tahun'] . '-' . str_pad($item['bulan'], 2, '0', STR_PAD_LEFT);
                $item['bulan_tahun'] = date('Y-m', strtotime($bulanTahun));
            }
        }
		
		if ($pemakaian_barang) {
                $this->response([
                    'status' => true,
                    'data' => $pemakaian_barang
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                    'data' => []
                ], RESTController::HTTP_OK);
            }
	}

    //total pemakaian bulanan setiap ruangan parameter tahun tanpa kategori
    public function total_pemakaian_bulanan_ruangan_get($tahun = null)
    {
        if ($tahun === null) {
            $this->response([
                'status' => false,
                'message' => 'Parameter tahun (YYYY) diperlukan.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }

        // Validasi format YYYY
        if (!preg_match('/^\d{4}$/', $tahun) || !DateTime::createFromFormat('Y', $tahun)) {
            $this->response([
                'status' => false,
                'message' => 'Format parameter tahun tidak valid. Gunakan YYYY.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }
        $pemakaian_barang = $this->Permintaan_barang_model->get_pemakaian_tahunan_ruangan($tahun);

        if ($pemakaian_barang) {
                $this->response([
                    'status' => true,
                    'data' => $pemakaian_barang
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                    'data' => []
                ], RESTController::HTTP_OK);
            }
    }

    // Method untuk mendapatkan pemakaian barang terbanyak per tahun tanpa kategori per tahun tanpa kategori
    public function barang_terbanyak_get($tahun = null)
    {
        if ($tahun === null) {
            $this->response([
                'status' => false,
                'message' => 'Parameter tahun (YYYY) diperlukan.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }

        // Validasi format YYYY
        if (!preg_match('/^\d{4}$/', $tahun) || !DateTime::createFromFormat('Y', $tahun)) {
            $this->response([
                'status' => false,
                'message' => 'Format parameter tahun tidak valid. Gunakan YYYY.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }
    
        $pemakaian_barang = $this->Permintaan_barang_model->get_barang_terbanyak($tahun);
        if ($pemakaian_barang) {
            $this->response([
                'status' => true,
                'data' => $pemakaian_barang
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'data' => []
            ], RESTController::HTTP_OK);
        }
    }

    // method mendapatkan barang dengan stok minimal
    public function barang_stok_minimal_get()
    {
        $barang_stok_minimal = $this->Barang_model->get_barang_stok_minimal();
        
        if ($barang_stok_minimal) {
            $this->response([
                'status' => true,
                'data' => $barang_stok_minimal
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'data' => []
            ], RESTController::HTTP_OK);
        }
    }

    public function pembelian_bulan_get($bulanTahun = null)
    {
        if ($bulanTahun === null) {
            $this->response([
                'status' => false,
                'message' => 'Parameter bulan dan tahun (YYYY-MM) diperlukan.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }
        // Validasi format YYYY-MM
        if (!preg_match('/^\d{4}-\d{2}$/', $bulanTahun) || !DateTime::createFromFormat('Y-m', $bulanTahun)) {
            $this->response([
                'status' => false,
                'message' => 'Format parameter tidak valid. Gunakan YYYY-MM.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }

        $pembelian_bulan = $this->Barang_masuk_model->total_pembelian_bulan($bulanTahun, $this->decoded_token['id_satker']);
        
        if ($pembelian_bulan) {
            $this->response([
                'status' => true,
                'data' => $pembelian_bulan
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'data' => []
            ], RESTController::HTTP_OK);
        }
    }

    public function pembelian_detail_get($bulanTahun = null)
    {
        if ($bulanTahun === null) {
            $this->response([
                'status' => false,
                'message' => 'Parameter bulan dan tahun (YYYY-MM) diperlukan.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }
        // Validasi format YYYY-MM
        if (!preg_match('/^\d{4}-\d{2}$/', $bulanTahun) || !DateTime::createFromFormat('Y-m', $bulanTahun)) {
            $this->response([
                'status' => false,
                'message' => 'Format parameter tidak valid. Gunakan YYYY-MM.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }

        $pembelian_detail = $this->Barang_masuk_model->get_list_barang_masuk($bulanTahun, $this->decoded_token['id_satker']);
        
        if ($pembelian_detail) {
            $this->response([
                'status' => true,
                'data' => $pembelian_detail
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'data' => []
            ], RESTController::HTTP_OK);
        }
    }

    
}
