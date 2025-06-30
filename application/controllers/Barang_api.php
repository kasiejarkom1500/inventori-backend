<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . 'vendor/autoload.php';
require_once APPPATH . 'core/ApiController.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use chriskacerguis\RestServer\RestController;

class Barang_api extends ApiController
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
    public function index()
    {
        $data['judul'] = "Barang";


        $data['barang'] = $this->Barang_model->tampil_data();

        $this->load->view('templates/header', $data);
        $this->load->view('admin/barang/index', $data);
        $this->load->view('templates/footer');
    }

    public function all_barang_get()
    {

        // var_dump($this->decoded_token);
        // die;

        $barang = $this->Barang_model->tampil_data($this->decoded_token['id_satker']);
        if ($barang) {
            $this->response([
                'status' => true,
                'data' => $barang
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_OK);
        }
    }

    public function barang_get()
    {

        $barang = $this->Barang_model->tampil_data_all();
        if ($barang) {
            $this->response([
                'status' => true,
                'data' => $barang
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_OK);
        }
    }

    public function all_barang_ark_get()
    {

        $barang = $this->Barang_model->tampil_data_ark($this->decoded_token['id_satker']);
        if ($barang) {
            $this->response([
                'status' => true,
                'data' => $barang
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_OK);
        }
    }

    public function barang_id_get($id)
    {

        $barang = $this->Barang_model->get_id($id);
        if ($barang) {
            $this->response([
                'status' => true,
                'data' => $barang
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_OK);
        }
    }

    public function insert_barang_post()
    {
        // Validasi input
        $this->form_validation->set_rules('kd_barang', 'Kode Barang', 'trim|required');
        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'trim|required');
        $this->form_validation->set_rules('kategori', 'Kategori', 'trim|required');
        $this->form_validation->set_rules('satuan', 'Satuan', 'trim|required');
        $this->form_validation->set_rules('stok', 'Stok', 'trim|required|numeric');
        $this->form_validation->set_rules('harga_satuan', 'Harga Satuan', 'trim|required|numeric');

        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
            ], RestController::HTTP_BAD_REQUEST);
        } else {
            // Ambil data dari request
            $kd_barang = $this->post('kd_barang');
            $nama_barang = $this->post('nama_barang');
            $kategori = $this->post('kategori');
            $satuan = $this->post('satuan');
            $stok = $this->post('stok');
            $harga_satuan = $this->post('harga_satuan');
            $stok_dasar = $this->post('stok_dasar');

            // Persiapkan data untuk insert ke tabel barang
            $data_barang = array(
                'kd_barang' => $kd_barang,
                'nama_barang' => $nama_barang,
                'kategori' => $kategori,
                'satuan' => $satuan,
                'stok' => $stok,
                'harga_satuan' => $harga_satuan,
                'stok_dasar' => $stok_dasar
            );

            // Coba insert data ke tabel barang
            if ($this->db->insert('barang', $data_barang)) {
                // Ambil ID terakhir yang dimasukkan
                $last_id = $this->db->insert_id();

                // Persiapkan data untuk tabel harga_pengajuan
                $data_harga_pengajuan = array(
                    'id_barang' => $last_id,
                    'harga_pengajuan' => 0
                );

                // Insert ke tabel harga_pengajuan
                $this->Barang_model->insert_harga_pengajuan($data_harga_pengajuan);

                // Kirim response sukses
                $this->response([
                    'status' => TRUE,
                    'message' => 'Data barang berhasil ditambahkan',
                    'data' => $data_barang
                ], RestController::HTTP_OK);
            } else {
                // Jika insert barang gagal
                $error = $this->db->error();
                $this->response([
                    'status' => FALSE,
                    'message' => 'Data gagal ditambahkan ke barang: ' . $error['message']
                ], RestController::HTTP_OK);
            }
        }
    }


    public function hapus_barang_delete($id)
    {
        // Periksa apakah ID valid
        if (empty($id) || !is_numeric($id)) {
            $this->response([
                'status' => FALSE,
                'message' => 'ID tidak valid'
            ], RestController::HTTP_BAD_REQUEST);
            return;
        }

        // Coba hapus data dari database
        if ($this->Barang_model->hapus_api($id)) {
            $this->response([
                'status' => TRUE,
                'message' => "Data dengan ID $id berhasil dihapus"
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => "Data dengan ID $id gagal dihapus atau tidak ditemukan"
            ], RestController::HTTP_OK);
        }
    }


    public function ubah_put($id = '')
    {
        // Validasi apakah ID disediakan
        if (empty($id) || !is_numeric($id)) {
            $this->response([
                'status' => FALSE,
                'message' => 'ID tidak valid'
            ], RestController::HTTP_OK);
            return;
        }

        // Ambil data dari PUT request
        $input = $this->put();

        // Set aturan validasi
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('kd_barang', 'Kd Barang', 'required|trim');
        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required|trim');
        $this->form_validation->set_rules('satuan', 'Satuan', 'required|trim');
        $this->form_validation->set_rules('stok', 'Stok', 'required|trim|numeric');
        $this->form_validation->set_rules('stok_dasar', 'Stok Dasar', 'required|trim|numeric');
        $this->form_validation->set_rules('harga_satuan', 'Harga Satuan', 'required|trim|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->response([
                'status' => FALSE,
                'errors' => $this->form_validation->error_array()
            ], RestController::HTTP_OK);
            return;
        }

        // Cek apakah data dengan ID tersebut ada
        $barang = $this->Barang_model->get_id($id);
        if (!$barang) {
            $this->response([
                'status' => FALSE,
                'message' => 'Data tidak ditemukan'
            ], RestController::HTTP_OK);
            return;
        }

        // Update data
        $data_update = [
            'kd_barang' => $input['kd_barang'],
            'nama_barang' => $input['nama_barang'],
            'kategori' => $input['kategori'],
            'satuan' => $input['satuan'],
            'stok' => $input['stok'],
            'harga_satuan' => $input['harga_satuan'],
            'stok_dasar' => $input['stok_dasar']
        ];

        if ($this->Barang_model->ubah_api($id, $data_update)) {
            $this->response([
                'status' => TRUE,
                'message' => 'Data berhasil diupdate'
            ], RestController::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Gagal mengupdate data'
            ], 500);
        }
    }


    public function update_harga_pengajuan_post()
    {
        // Ambil data harga_pengajuan dari POST
        $harga_pengajuan = $this->input->post('harga_pengajuan');

        // Cek apakah harga_pengajuan ada dan valid
        if (!$harga_pengajuan) {
            // Kirim respons error jika harga_pengajuan tidak ditemukan
            $this->response([
                'status' => FALSE,
                'message' => 'Harga pengajuan tidak ditemukan'
            ], RestController::HTTP_OK); // 400
            return;
        }

        // Panggil model untuk memperbarui harga pengajuan
        $result = $this->Barang_model->update_harga_pengajuan($harga_pengajuan);

        if ($result) {
            // Kirim respons sukses jika update berhasil
            $this->response([
                'status' => TRUE,
                'message' => 'Harga pengajuan berhasil diperbarui'
            ], RestController::HTTP_OK); // 200
        } else {
            // Kirim respons gagal jika update gagal
            $this->response([
                'status' => FALSE,
                'message' => 'Gagal memperbarui harga pengajuan'
            ], RestController::HTTP_OK); // 500
        }
    }


    public function import_excel_post()
    {
        $file_mimes = array(
            'application/vnd.ms-excel',
            'text/plain',
            'text/csv',
            'text/tsv',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
            $inputFileName = $_FILES['file']['tmp_name'];

            try {
                $spreadsheet = IOFactory::load($inputFileName);
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                $this->response([
                    'status' => false,
                    'message' => 'Error loading file: ' . $e->getMessage()
                ], RESTController::HTTP_INTERNAL_ERROR);
                return;
            }

            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            // Extract Kd Barang column
            $kd_barang = array_column($sheetData, 0);

            // Check for duplicate values in the uploaded file
            if (count($kd_barang) !== count(array_unique($kd_barang))) {
                $this->response([
                    'status' => false,
                    'message' => 'Duplicate Kd Barang found in the uploaded file.'
                ], RESTController::HTTP_BAD_REQUEST);
                return;
            }

            foreach ($sheetData as $key => $row) {
                if ($key == 0) continue; // Skip the header row

                // Check if kd_barang already exists in the database
                if ($this->Barang_model->exists_kd_barang($row[0])) {
                    $this->response([
                        'status' => false,
                        'message' => 'Kd Barang ' . $row[0] . ' already exists in the database.'
                    ], RESTController::HTTP_CONFLICT);
                    return;
                }

                $data_barang = array(
                    'kd_barang' => $row[0],
                    'nama_barang' => $row[1],
                    'kategori' => $row[2],
                    'satuan' => $row[3],
                    'stok' => $row[4],
                    'stok_dasar' => $row[5],
                    'harga_satuan' => $row[6],
                );

                // Insert data into barang table
                $this->Barang_model->insert_barang($data_barang);

                // Get the inserted id_barang
                $id_barang = $this->db->insert_id();

                // Insert data into harga_pengajuan table
                $data_harga_pengajuan = array(
                    'id_barang' => $id_barang,
                    'harga_pengajuan' => 0
                );

                $this->Barang_model->insert_harga_pengajuan($data_harga_pengajuan);
            }

            $this->response([
                'status' => true,
                'message' => 'Data has been successfully imported.'
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Invalid file format.'
            ], RESTController::HTTP_OK);
        }
    }

    public function subkategori_get()
    {
        $subkategori = $this->Barang_model->fetch_subkategori();
        if ($subkategori) {
            $this->response([
                'status' => true,
                'data' => $subkategori
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_OK);
        }
    }
}
