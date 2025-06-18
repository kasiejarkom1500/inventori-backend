<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/ApiController.php';

use chriskacerguis\RestServer\RestController;

class Permintaan_barang_api extends ApiController
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


        $this->load->model('Permintaan_barang_model');
        $this->load->model('Barang_model');
        $this->load->model('Barang_masuk_model');
        $this->load->model('User_model');
        $this->load->model('ruangan_model');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->config('rest');
    }

    public function transaksi_permintaan_get()
    {

        $permintaan_barang = $this->Permintaan_barang_model->get_grouped_by_date();

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

    public function permintaan_detail_get($id_ruangan, $tanggal)
    {

        // Validasi apakah tanggal sesuai format (opsional)
        if (!DateTime::createFromFormat('Y-m-d', $tanggal)) {
            // Jika tidak valid, kembalikan error
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_NOT_FOUND);
        }

        $permintaan_barang = $this->Permintaan_barang_model->get_by_date($id_ruangan, $tanggal);

        if ($permintaan_barang) {
            $this->response([
                'status' => true,
                'data' => $permintaan_barang
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_NOT_FOUND);
        }
    }

    public function cetak_permintaan_get($id_ruangan, $tanggal)
    {

        // Validasi apakah tanggal sesuai format (opsional)
        if (!DateTime::createFromFormat('Y-m-d', $tanggal)) {
            // Jika tidak valid, kembalikan error
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_NOT_FOUND);
        }

        $permintaan_barang = $this->Permintaan_barang_model->get_approved_by_date($id_ruangan, $tanggal);

        if ($permintaan_barang) {
            $this->response([
                'status' => true,
                'data' => $permintaan_barang
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_NOT_FOUND);
        }
    }

    public function simpan_post()
    {

        // Ambil data dari request body dalam bentuk JSON
        $data = json_decode($this->input->raw_input_stream, true);

        if (!is_array($data) || empty($data)) {
            // Jika data tidak valid, kembalikan error response
            $this->response([
                'status' => 'error',
                'message' => 'Invalid input. Please provide valid JSON data.'
            ], 400);
            return;
        }

        $tanggal = date('Y-m-d');

        // Loop melalui setiap item dalam data
        foreach ($data as $item) {
            // Validasi setiap item (id_permintaan, status, jumlah harus ada)
            if (!isset($item['jumlah'], $item['id_ruangan'], $item['id_user'])) {
                $this->response([
                    'status' => 'error',
                    'message' => 'Invalid data structure. Each item must contain jumlah, id_ruangan, and id_user.'
                ], 400);
                return;
            }

            // // Validasi apakah id_barang ada(jika barang baru, id_barang harus 0 sehingga bisa di insert dulu)
            // if ($item['id_barang'] == '0') {
            //     $data = [
            //         'nama_barang' => $item['nama_barang'],
            //         'kd_barang' => $item['nama_barang']. '-' . $tanggal,
            //         'kategori' => $item['kategori'],
            //         'satuan' => $item['satuan'],
            //         'kategori' => $item['kategori'],
            //         'stok' => $item['stok'],
            //     ];
            //     $this->Barang_model->insert_barang($data);
            // }

            $item['tanggal'] = $tanggal;
            $item['status'] = 'Pending';
            $item['jumlah_approved'] = $item['jumlah'];
            $this->Permintaan_barang_model->insert($item);
        }

        // Response success
        $this->response([
            'status' => 'success',
            'message' => 'Permintaan Berhasil Ditambahkan.'
        ], 200);
    }

    public function update_status_put()
    {
        // Ambil data dari request body dalam bentuk JSON
        $data = json_decode($this->input->raw_input_stream, true);

        if (!is_array($data) || empty($data)) {
            // Jika data tidak valid, kembalikan error response
            $this->response([
                'status' => 'Error',
                'message' => 'Invalid input. Please provide valid JSON data.'
            ], 200);
            return;
        }

        // Loop melalui setiap item dalam data
        foreach ($data as $item) {
            // Validasi setiap item (id_permintaan, status, jumlah harus ada)
            if (!isset($item['id_permintaan'], $item['status'], $item['jumlah'])) {
                $this->response([
                    'status' => 'Error',
                    'message' => 'Invalid data structure. Each item must contain id_permintaan, status, and jumlah.'
                ], 200);
                return;
            }

            $id_permintaan = $item['id_permintaan'];
            $status = $item['status'];
            $jumlah = $item['jumlah'];

            // Update status permintaan


            $database_permintaan = $this->Permintaan_barang_model->get_permintaan($id_permintaan);


            if ($status == 'Approved' && ($database_permintaan['status'] == 'Pending' || $database_permintaan['status'] == 'Rejected')) {
                // Ambil jumlah permintaan dan id_barang

                $this->db->trans_begin();

                $this->Permintaan_barang_model->update_status($id_permintaan, $status, $jumlah);

                $database_permintaan = $this->Permintaan_barang_model->get_permintaan($id_permintaan);

                $jumlah_permintaan = $database_permintaan['jumlah_approved'];

                $id_barang = $this->Permintaan_barang_model->get_id_barang($id_permintaan);

                // Kurangi stok barang
                $barang = $this->Barang_model->get_id($id_barang);

                if ($barang['stok'] < $jumlah_permintaan) {
                    $this->response([
                        'status' => 'Error',
                        'message' => 'Jumlah permintaan melebihi sisa stok'
                    ], 200);
                    return;
                } else {

                    $sisa_permintaan = $jumlah_permintaan;

                    // Ambil stok masuk yang tersedia secara FIFO
                    $stok_masuk = $this->Barang_masuk_model->get_stok_masuk_fifo($id_barang);

                    // var_dump($stok_masuk);
                    // die; // urut ASC

                    foreach ($stok_masuk as $stok) {
                        if ($sisa_permintaan <= 0) break;

                        $id_masuk = $stok['id_barang_masuk'];
                        $jumlah_tersedia = $stok['sisa_stok'];

                        if ($jumlah_tersedia == 0) continue;

                        if ($jumlah_tersedia >= $sisa_permintaan) {
                            // cukup ambil dari entri ini saja
                            $jumlah_diambil = $sisa_permintaan;
                            $sisa_permintaan = 0;
                        } else {
                            // ambil semua dari entri ini
                            $jumlah_diambil = $jumlah_tersedia;
                            $sisa_permintaan -= $jumlah_tersedia;
                        }

                        // Kurangi entri barang_masuk
                        // Kurangi entri barang_masuk
                        $kurangi_result = $this->Barang_masuk_model->kurangi_barang_masuk($id_masuk, $jumlah_diambil);

                        if (!$kurangi_result) {
                            $this->db->trans_rollback();

                            $this->response([
                                'status' => 'Gagal',
                                'message' => 'Gagal mengurangi stok barang masuk'
                            ], 200);
                            return;
                        }

                        // (Opsional) Simpan transaksi keluar berdasarkan id_masuk
                        $catat_fifo_result = $this->Permintaan_barang_model->catat_keluar_fifo([
                            'id_permintaan' => $id_permintaan,
                            'id_barang_masuk' => $id_masuk,
                            'jumlah_keluar' => $jumlah_diambil,
                            'harga_satuan' => $stok['harga']
                        ]);

                        if (!$catat_fifo_result) {
                            $this->db->trans_rollback();
                            $this->response([
                                'status' => 'Gagal',
                                'message' => 'Gagal Input Transaksi Keluar FIFO: '
                            ], 200);
                            return;
                        }
                    }

                    // Update stok akhir
                    $this->Barang_model->kurangi_stok_barang($id_barang, $jumlah_permintaan);

                    $updated_barang = $this->Barang_model->get_id($id_barang);

                    $update_transaksi_stok = $this->Permintaan_barang_model->simpan_current_stok($id_permintaan, $updated_barang['stok']);

                    if ($update_transaksi_stok) {

                        $this->db->trans_rollback();

                        $this->response(
                            [
                                'status' => 'Gagal',
                                'message' => 'Gagal update '. $updated_barang['nama_barang'] .' transaksi keluar stok'
                            ],
                            200
                        );

                        return;
                    }

                    $this->db->trans_commit();
                }
            } else if (($status == 'Pending' || $status == 'Rejected') && $database_permintaan['status'] == 'Approved') {
                // Hanya update status permintaan
                $this->db->trans_begin();

                // Ambil jumlah yang sudah di-approved sebelumnya
                $jumlah_approved = $database_permintaan['jumlah_approved'];

                // Ambil id_barang dari permintaan
                $id_barang = $database_permintaan['id_barang'];

                // Tambahkan kembali stok utama
                $this->Barang_model->tambah_stok_barang($id_barang, $jumlah_approved);

                // Ambil data barang keluar dari FIFO sebelumnya jika ada
                $barang_keluar = $this->Permintaan_barang_model->get_log_keluar_by_permintaan($id_permintaan); // butuh log catatan sebelumnya

                foreach ($barang_keluar as $keluar) {
                    $id_masuk = $keluar['id_barang_masuk'];
                    $jumlah_dikembalikan = $keluar['jumlah'];

                    // Tambahkan kembali ke entri barang_masuk
                    $this->Barang_masuk_model->tambah_sisa_stok($id_masuk, $jumlah_dikembalikan);
                }

                // Update status permintaan
                $this->Permintaan_barang_model->update_status($id_permintaan, $status);

                // Update catatan stok setelah rollback
                $updated_barang = $this->Barang_model->get_id($id_barang);
                $update_transaksi_stok = $this->Permintaan_barang_model->simpan_current_stok($id_permintaan, $updated_barang['stok']);

                if ($update_transaksi_stok) {
                    $this->db->trans_rollback();
                    $this->response([
                        'status' => 'Gagal',
                        'message' => 'Gagal update rollback stok keluar'
                    ], 200);
                    return;
                }

                $this->db->trans_commit();
            } else if ($status == 'Rejected' && $database_permintaan['status'] == 'Pending') {
                // Jika status diubah menjadi Rejected, tidak perlu mengurangi stok
                // Hanya update status permintaan
                $this->Permintaan_barang_model->update_status($id_permintaan, $status, 0);
            }
        }

        // Response success
        $this->response([
            'status' => 'success',
            'message' => 'Status updated and stock adjusted successfully.'
        ], 200);
    }
}
