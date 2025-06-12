<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use chriskacerguis\RestServer\RestController;

class ApiController extends RestController
{
    public $decoded_token;

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
        
        
        $this->load->helper('jwt');

        // Ambil token dari header Authorization
        $headers = $this->input->request_headers();
        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $decoded = validate_jwt($token);
            if ($decoded) {
                $this->decoded_token = $decoded;
            } else {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid or expired token.'
                ], RestController::HTTP_UNAUTHORIZED);
                exit;
            }
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Authorization header not found.'
            ], RestController::HTTP_UNAUTHORIZED);
            exit;
        }
    }
}
