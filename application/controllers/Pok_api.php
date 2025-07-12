<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/ApiController.php';


use chriskacerguis\RestServer\RestController;

class Pok_api extends ApiController
{

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


        // Load necessary libraries and helpers
        $this->load->helper(['url', 'file']);
        $this->load->model('Permintaan_barang_model');
        $this->load->model('Pok_model');
        $this->load->model('User_model');
        $this->load->model('ruangan_model');
    }


    public function program_get()
    {
        $programs = $this->Pok_model->get_program();
        if ($programs) {
            $this->response([
                'status' => true,
                'data' => $programs
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No programs found'
            ], RestController::HTTP_NOT_FOUND);
        }
    }

    public function kegiatan_get($kd_program)
    {
        $kegiatan = $this->Pok_model->get_kegiatan($kd_program);
        if ($kegiatan) {
            $this->response([
                'status' => true,
                'data' => $kegiatan
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No activities found for this program'
            ], RestController::HTTP_NOT_FOUND);
        }
    }

    public function kro_get($kd_kegiatan)
    {
        $kro = $this->Pok_model->get_kro($kd_kegiatan);
        if ($kro) {
            $this->response([
                'status' => true,
                'data' => $kro
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No KRO found for this activity'
            ], RestController::HTTP_NOT_FOUND);
        }
    }

    public function ro_get($kd_kro)
    {
        $ro = $this->Pok_model->get_ro($kd_kro);
        if ($ro) {
            $this->response([
                'status' => true,
                'data' => $ro
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No RO found for this KRO'
            ], RestController::HTTP_NOT_FOUND);
        }
    }

    public function komponen_get($kd_ro)
    {
        $komponen = $this->Pok_model->get_komponen($kd_ro);
        if ($komponen) {
            $this->response([
                'status' => true,
                'data' => $komponen
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No components found for this RO'
            ], RestController::HTTP_NOT_FOUND);
        }
    }

    public function komponen_all_get($kd_ro)
    {
        $komponen = $this->Pok_model->get_komponen($kd_ro);
        if ($komponen) {
            $this->response([
                'status' => true,
                'data' => $komponen
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'No components found for this RO'
            ], RestController::HTTP_NOT_FOUND);
        }
    }


    public function gabungan_pok_post($kd_program = null, $kd_kegiatan = null, $kd_kro = null, $kd_ro = null, $kd_komponen = null)
    {
        //ambil parameter dari post body
        $kd_program = $this->post('kd_program') ?? $kd_program;
        $kd_kegiatan = $this->post('kd_kegiatan') ?? $kd_kegiatan;
        $kd_kro = $this->post('kd_kro') ?? $kd_kro;
        $kd_ro = $this->post('kd_ro') ?? $kd_ro;
        $kd_komponen = $this->post('kd_komponen') ?? $kd_komponen;

        // var_dump($kd_program, $kd_kegiatan, $kd_kro, $kd_ro, $kd_komponen);
        // die();
        // Get the combined POK data based on the provided parameters
        $gabungan_pok = $this->Pok_model->get_gabungan_pok($kd_program, $kd_kegiatan, $kd_kro, $kd_ro, $kd_komponen);
        
        if ($gabungan_pok) {
            $this->response([
                'status' => 'Berhasil',
                'data' => $gabungan_pok
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => 'Gagal',
                'message' => 'No combined POK data found'
            ], RestController::HTTP_OK);
        }
    }
    

    



}
