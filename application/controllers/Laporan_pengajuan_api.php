<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/ApiController.php';

use chriskacerguis\RestServer\RestController;

class Laporan_pengajuan_api extends ApiController
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


        $this->load->model('Laporan_pengajuan_model');

        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->config('rest');
    }

    public function index()
    {

        $data['pengajuan'] = [];
        $data['tgl_mulai'] = '';
        $data['tgl_akhir'] = '';
        $this->load->view('templates/header2');
        $this->load->view('admin_pengajuan/laporan_pengajuan/index', $data);
        $this->load->view('templates/footer');
    }


    public function cetak_kak_post()
    {
        $tgl_mulai = $this->post('tgl_mulai');
        $tgl_akhir = $this->post('tgl_akhir');



        // Validasi apakah tanggal sesuai format (opsional)
        if (!DateTime::createFromFormat('Y-m-d', $tgl_mulai)) {
            // Jika tidak valid, kembalikan error
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_NOT_FOUND);
        }

        if (!DateTime::createFromFormat('Y-m-d', $tgl_akhir)) {
            // Jika tidak valid, kembalikan error
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_NOT_FOUND);
        }

        $data_pengajuan = $this->Laporan_pengajuan_model->get_approved_pengajuan_groupid($tgl_mulai, $tgl_akhir);
        $total = 0;

        foreach ($data_pengajuan as &$barang) {
            if (!($barang['stok'] + $barang['jumlah'] > $barang['stok_dasar'])) {
                $barang['jumlah'] = $barang['stok_dasar'] - $barang['stok'];
            }



            $barang['harga_satuan'] = 105 / 100 * $barang['harga_satuan']; // Menghitung harga pengajuan
            $barang['total_harga'] = $barang['jumlah'] * $barang['harga_satuan']; // Menghitung total harga

            $total += $barang['total_harga'];
        }

        $input_array = $data_pengajuan;
        // var_dump($data_pengajuan);
        // die;
        $data = [];

        foreach ($input_array as $item) {
            $key = $item['program'] . '|' . $item['kegiatan'] . '|' . $item['kro'] . '|' . $item['ro'] . '|' . $item['komponen'];

            if (!isset($data[$key])) {
                $data[$key] = [
                    'program' => $item['program'],
                    'kegiatan' => $item['kegiatan'],
                    'kro' => $item['kro'],
                    'ro' => $item['ro'],
                    'komponen' => $item['komponen'],
                    'list_barang' => []
                ];
            }

            $data[$key]['list_barang'][] = [
                'kd_barang' => $item['kd_barang'],
                'nama_barang' => $item['nama_barang'],
                'kategori' => $item['kategori'],
                'satuan' => $item['satuan'],
                'harga_satuan' => $item['harga_satuan'],
                'stok' => $item['stok'],
                'stok_dasar' => $item['stok_dasar'],
                'jumlah' => $item['jumlah'],
                'total_harga' => $item['total_harga']
            ];
        }

        // Reset index agar jadi array numerik
        $data = array_values($data);



        if ($data_pengajuan) {
            $this->response([
                'status' => 'Berhasil',
                'data' => $data
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => 'Gagal',
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_NOT_FOUND);
        }
    }



    public function filter_pengajuan_post()
    {
        $tgl_mulai = $this->post('tgl_mulai');
        $tgl_akhir = $this->post('tgl_akhir');


        // Validasi apakah tanggal sesuai format (opsional)
        if (!DateTime::createFromFormat('Y-m-d', $tgl_mulai)) {
            // Jika tidak valid, kembalikan error
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_NOT_FOUND);
        }

        if (!DateTime::createFromFormat('Y-m-d', $tgl_akhir)) {
            // Jika tidak valid, kembalikan error
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_NOT_FOUND);
        }

        $pengajuan_barang = $this->Laporan_pengajuan_model->get_approved_pengajuan_api($tgl_mulai, $tgl_akhir, $this->decoded_token['id_satker']);

        foreach ($pengajuan_barang as &$barang) {
            $barang['harga_pengajuan'] = 105 / 100 * $barang['harga_satuan'];
        }


        if ($pengajuan_barang) {
            $this->response([
                'status' => true,
                'data' => $pengajuan_barang
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_NOT_FOUND);
        }
    }
}
