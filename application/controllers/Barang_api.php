<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';
require_once APPPATH . 'core/ApiController.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use chriskacerguis\RestServer\RestController;

class Barang_api extends ApiController
{
    public $Barang_model;
    public $barangModel;
    public $form_validation;
    public $db;
    public $session;

    /**
     * Constructor for the Barang API class
     *
     * Loads the necessary models, helpers and config files
     * and sets the CORS headers
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        /**
         * Load the Barang_model and Barang_masuk_model
         */
        $this->load->model('Barang_model');
        $this->load->model('Barang_masuk_model');

        /**
         * Load the form and url helpers
         */
        $this->load->helper(['form', 'url']);

        /**
         * Load the rest config file
         */
        $this->load->config('rest');

        /**
         * Set the CORS headers
         */
        $this->corsHeaders();
    }

    /**
     * Set the CORS headers
     *
     * This function sets the necessary CORS headers to allow
     * cross-origin requests from any domain.
     *
     * It also handles the OPTIONS request method, which is used by
     * the browser to send a preflight request, by returning the
     * necessary headers and a 200 response code.
     *
     * @return void
     */
    private function corsHeaders()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            header('Access-Control-Max-Age: 86400'); // Cache preflight response for 24 hours
            exit(0);
        }
    }

    /* ==============================
       API: GET DATA BARANG
    ============================== */



    public function all_barang_get()
    {
        $barang = $this->Barang_model->tampil_data($this->decoded_token['id_satker']);
        if ($barang) {
            $this->response(['status' => true, 'data' => $barang], RestController::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], RestController::HTTP_OK);
        }
    }

    public function barang_get()
    {
        $barang = $this->Barang_model->tampil_data_all($this->decoded_token['id_satker']);
        if ($barang) {
            $this->response(['status' => true, 'data' => $barang], RestController::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], RestController::HTTP_OK);
        }
    }

    public function barang_BASO_get()
    {
        $barang = $this->Barang_model->baso_data($this->decoded_token['id_satker']);
        if ($barang) {
            $this->response(['status' => true, 'data' => $barang], RestController::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], RestController::HTTP_OK);
        }
    }

    public function all_barang_ark_get()
    {
        $barang = $this->Barang_model->tampil_data_ark($this->decoded_token['id_satker']);
        if ($barang) {
            $this->response(['status' => true, 'data' => $barang], RestController::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], RestController::HTTP_OK);
        }
    }

    public function barang_id_get($id)
    {
        $barang = $this->Barang_model->get_id($id);
        if ($barang) {
            $this->response(['status' => true, 'data' => $barang], RestController::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], RestController::HTTP_OK);
        }
    }

    /* ==============================
       API: INSERT BARANG
    ============================== */

    public function insert_barang_post()
    {
        // Ambil data dari request
        $kd_barang = $this->post('kd_barang');
        $nama_barang = $this->post('nama_barang');
        $kategori = $this->post('kategori');
        $satuan = $this->post('satuan');
        $stok = $this->post('stok');
        $harga_satuan = $this->post('harga_satuan');
        $stok_dasar = $this->post('stok_dasar');
        $kd_subkategori = $this->post('kd_subkategori');
        $kabupaten_id = $this->post('kabupaten_id');

        $data_barang = [
            'kd_subkategori' => $kd_subkategori,
            'kd_barang'      => $kd_barang,
            'kabupaten_id'   => $kabupaten_id,
            'nama_barang'    => $nama_barang,
            'kategori'       => $kategori,
            'satuan'         => $satuan,
            'stok'           => $stok,
            'harga_satuan'   => $harga_satuan,
            'stok_dasar'     => $stok_dasar
        ];

        if ($this->db->insert('barang', $data_barang)) {
            $last_id = $this->db->insert_id();
            $data_harga_pengajuan = ['id_barang' => $last_id, 'harga_pengajuan' => 0];
            $this->Barang_model->insert_harga_pengajuan($data_harga_pengajuan);

            $this->response([
                'status' => TRUE,
                'message' => 'Data barang berhasil ditambahkan',
                'data' => $data_barang
            ], RestController::HTTP_OK);
        } else {
            $error = $this->db->error();
            $this->response([
                'status' => FALSE,
                'message' => 'Data gagal ditambahkan ke barang: ' . $error['message']
            ], RestController::HTTP_OK);
        }
    }

    /* ==============================
       API: DELETE BARANG
    ============================== */
    public function hapus_barang_delete($id)
    {
        if (empty($id) || !is_numeric($id)) {
            $this->response(['status' => FALSE, 'message' => 'ID tidak valid'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if ($this->Barang_model->hapus_api($id)) {
            $this->response(['status' => TRUE, 'message' => "Data dengan ID $id berhasil dihapus"], RestController::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => "Data dengan ID $id gagal dihapus atau tidak ditemukan"], RestController::HTTP_OK);
        }
    }

    /* ==============================
       API: UPDATE BARANG (PUT)
    ============================== */
    public function ubah_put($id = '')
    {
        if (empty($id) || !is_numeric($id)) {
            $this->response(['status' => FALSE, 'message' => 'ID tidak valid'], RestController::HTTP_OK);
            return;
        }

        $input = $this->put();
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('kd_barang', 'Kd Barang', 'required|trim');
        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required|trim');
        $this->form_validation->set_rules('satuan', 'Satuan', 'required|trim');
        $this->form_validation->set_rules('stok', 'Stok', 'required|trim|numeric');
        $this->form_validation->set_rules('stok_dasar', 'Stok Dasar', 'required|trim|numeric');
        $this->form_validation->set_rules('harga_satuan', 'Harga Satuan', 'required|trim|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->response(['status' => FALSE, 'errors' => $this->form_validation->error_array()], RestController::HTTP_OK);
            return;
        }

        $barang = $this->Barang_model->get_id($id);
        if (!$barang) {
            $this->response(['status' => FALSE, 'message' => 'Data tidak ditemukan'], RestController::HTTP_OK);
            return;
        }

        $data_update = [
            'kd_barang'    => $input['kd_barang'],
            'nama_barang'  => $input['nama_barang'],
            'kategori'     => $input['kategori'],
            'satuan'       => $input['satuan'],
            'stok'         => $input['stok'],
            'harga_satuan' => $input['harga_satuan'],
            'stok_dasar'   => $input['stok_dasar']
        ];

        if ($this->Barang_model->ubah_api($id, $data_update)) {
            $this->response(['status' => TRUE, 'message' => 'Data berhasil diupdate'], RestController::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => 'Gagal mengupdate data'], 500);
        }
    }

    /* ==============================
       API: UPDATE HARGA PENGAJUAN
    ============================== */
    public function update_harga_pengajuan_post()
    {
        $harga_pengajuan = $this->input->post('harga_pengajuan');
        if (!$harga_pengajuan) {
            $this->response(['status' => FALSE, 'message' => 'Harga pengajuan tidak ditemukan'], RestController::HTTP_OK);
            return;
        }

        $result = $this->Barang_model->update_harga_pengajuan($harga_pengajuan);
        if ($result) {
            $this->response(['status' => TRUE, 'message' => 'Harga pengajuan berhasil diperbarui'], RestController::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => 'Gagal memperbarui harga pengajuan'], RestController::HTTP_OK);
        }
    }

    /* ==============================
       API: IMPORT EXCEL BARANG MASUK
    ============================== */
    public function import_excel_post()
    {
        $file_mimes = [
            'application/vnd.ms-excel',
            'text/plain',
            'text/csv',
            'text/tsv',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
            $inputFileName = $_FILES['file']['tmp_name'];

            try {
                $spreadsheet = IOFactory::load($inputFileName);
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                return $this->response(['status' => false, 'message' => 'Error loading file: ' . $e->getMessage()], RestController::HTTP_INTERNAL_ERROR);
            }

            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $data_pembelian = [
                'id_satker'   => $this->decoded_token['id_satker'],
                'no_kuitansi' => 'IMPORT-' . date('YmdH'),
                'tgl_masuk'   => date('Y-m-d'),
                'id_sumber'   => '2',
            ];

            $this->db->trans_start();
            $id_pembelian = $this->Barang_masuk_model->simpan_pembelian($data_pembelian);

            foreach ($sheetData as $key => $row) {
                if ($key == 0) continue; // skip header

                $data_barang = [
                    'kd_subkategori' => $row[0],
                    'kd_barang'      => $row[1],
                    'kabupaten_id'   => $row[2],
                    'nama_barang'    => $row[3],
                    'kategori'       => $row[4],
                    'satuan'         => $row[5],
                    'stok'           => $row[6],
                    'harga_satuan'   => $row[7],
                    'stok_dasar'     => $row[8],
                ];

                if (!$this->Barang_model->insert_barang($data_barang)) {
                    $this->db->trans_rollback();
                    return $this->response(['status' => false, 'message' => 'Gagal insert barang pada baris ' . ($key + 1)], RestController::HTTP_INTERNAL_ERROR);
                }

                $id_barang = $this->db->insert_id();
                $data_barang_masuk = [
                    'tgl_masuk'    => date('Y-m-d'),
                    'id_pembelian' => $id_pembelian,
                    'id_barang'    => $id_barang,
                    'jumlah'       => $row[6],
                    'harga'        => $row[7],
                    'sisa_stok'    => $row[6],
                    'current_stok' => $row[6],
                    'catatan'      => null
                ];

                $id_barang_masuk = $this->Barang_masuk_model->simpan($data_barang_masuk);
                if (!$id_barang_masuk) {
                    $this->db->trans_rollback();
                    return $this->response(['status' => false, 'message' => 'Gagal insert barang_masuk pada baris ' . ($key + 1)], RestController::HTTP_INTERNAL_ERROR);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return $this->response(['status' => false, 'message' => 'Terjadi kesalahan saat import, semua perubahan dibatalkan.'], RestController::HTTP_INTERNAL_ERROR);
            }

            return $this->response(['status' => true, 'message' => 'Data berhasil diimport.'], RestController::HTTP_OK);
        } else {
            return $this->response(['status' => false, 'message' => 'Invalid file format.'], RestController::HTTP_BAD_REQUEST);
        }
    }

    /* ==============================
       API: GET SUBKATEGORI
    ============================== */
    public function subkategori_get()
    {
        $subkategori = $this->Barang_model->fetch_subkategori();
        if ($subkategori) {
            $this->response(['status' => true, 'data' => $subkategori], RestController::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], RestController::HTTP_OK);
        }
    }
}
