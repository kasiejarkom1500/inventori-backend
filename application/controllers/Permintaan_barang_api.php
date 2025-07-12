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
        $this->load->model('Pengajuan_barang_model');
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

        $permintaan_barang = $this->Permintaan_barang_model->get_grouped_by_date($this->decoded_token['id_satker']);

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

    public function permintaan_detail_get($id_ruangan, $tanggal, $id_transaksi)
    {

        // Validasi apakah tanggal sesuai format (opsional)
        if (!DateTime::createFromFormat('Y-m-d', $tanggal)) {
            // Jika tidak valid, kembalikan error
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], RESTController::HTTP_NOT_FOUND);
        }

        // $permintaan_barang = $this->Permintaan_barang_model->get_by_date($id_ruangan, $tanggal);
        $permintaan_barang = $this->Permintaan_barang_model->get_by_id_transaksi($id_transaksi, $id_ruangan, $tanggal);

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

        $transaksi_permintaan = [
            'id_ruangan' => $data['0']['id_ruangan'],
            'id_user' => $data['0']['id_user']
        ];

        $id_transaksi_permintaan = $this->Permintaan_barang_model->insert_transaksi_permintaan($transaksi_permintaan);

        // Loop melalui setiap item dalam data
        foreach ($data as $item) {
            $this->db->trans_begin();
            // Validasi minimal
            if (!isset($item['jumlah'], $item['id_ruangan'], $item['id_user'])) {
                $this->response([
                    'status' => 'error',
                    'message' => 'Invalid data structure. Each item must contain jumlah, id_ruangan, and id_user.'
                ], 400);
                return;
            }

            // Jika barang baru
            if (strpos($item['id_barang'], 'custom-') === 0) {


                // Buat kode unik
                $kd_barang = rand(1000, 9999);

                $barang_baru = [
                    'kd_barang'  => $kd_barang,
                    'nama_barang' => $item['nama_barang'],
                    'kategori'   => 'ATK',
                    'satuan'     => 'PCs',
                    'stok'       => 0,
                    'temp'       => 1,
                    'kabupaten_id'  => $this->decoded_token['id_satker'],
                ];

                // Simpan barang baru
                $this->Barang_model->insert_barang($barang_baru);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->response([
                        'status' => 'Gagal',
                        'message' => 'Gagal menyimpan barang baru: ' . $item['nama_barang']
                    ], 500);
                    return;
                }

                // Ambil ID baru
                $new_id = $this->db->insert_id();
                $item['id_barang'] = $new_id;
            }

            // Atur data permintaan
            $item['tanggal'] = $tanggal;
            $item['status'] = 'Pending';

            $barang = $this->Barang_model->get_id($item['id_barang']);

            if (!$barang) {
                $this->response([
                    'status' => 'error',
                    'message' => 'Barang tidak ditemukan dengan ID: ' . $item['id_barang']
                ], 404);
                return;
            }

            // Hitung jumlah approved
            $item['jumlah_approved'] = ($barang['stok'] < $item['jumlah'])
                ? $barang['stok']
                : $item['jumlah'];

            // var_dump($item);
            // die;

            //hapus nama_barang dari item sebelum di insert
            unset($item['nama_barang']);

            //simpan ke transkasi permintaan


            $item['id_transaksi'] = $id_transaksi_permintaan;

            // Simpan permintaan
            $this->Permintaan_barang_model->insert($item);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->response([
                    'status' => 'Gagal',
                    'message' => 'Gagal menyimpan permintaan barang: ' . $item['nama_barang']
                ], 500);
            }

            $this->db->trans_commit();
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
                'status' => 'Gagal',
                'message' => 'Invalid input. Please provide valid JSON data.'
            ], 200);
            return;
        }

        $butuh_transaksi = false;

        foreach ($data as $item) {
            if ($item['status'] === 'Approved') {
                $database_permintaan = $this->Permintaan_barang_model->get_permintaan($item['id_permintaan']);
                if ($database_permintaan['status'] === 'Pending' || $database_permintaan['status'] === 'Rejected' || $database_permintaan['status'] === 'Pengadaan') {
                    if (isset($item['is_buat_pengajuan']) && $item['is_buat_pengajuan']) {
                        $butuh_transaksi = true;
                        break;
                    }
                }
            }
        }


        $id_transaksi_permintaan = null;
        if ($butuh_transaksi) {
            $id_transaksi_permintaan = $this->Permintaan_barang_model->insert_transaksi_permintaan([
                'id_ruangan' => $database_permintaan['id_ruangan'], // asumsi semua item ruangan sama, kalau beda, sesuaikan
                'id_user' => $this->decoded_token['id_user']
            ]);
        }



        // Loop melalui setiap item dalam data
        foreach ($data as $item) {
            // Validasi setiap item (id_permintaan, status, jumlah harus ada)
            if (!isset($item['id_permintaan'], $item['status'], $item['jumlah_approved'])) {
                $this->response([
                    'status' => 'Gagal',
                    'message' => 'Invalid data structure. Each item must contain id_permintaan, status, and jumlah.'
                ], 200);
                return;
            }

            $id_permintaan = $item['id_permintaan'];
            $status = $item['status'];
            $jumlah_approved = $item['jumlah_approved'];
            $id_user = $this->decoded_token['id_user'];



            $database_permintaan = $this->Permintaan_barang_model->get_permintaan($id_permintaan);

            if ($status == 'Approved' && ($database_permintaan['status'] == 'Pending' || $database_permintaan['status'] == 'Rejected' || $database_permintaan['status'] == 'Pengadaan')) {
                // Ambil jumlah permintaan dan id_barang


                $this->db->trans_begin();

                $database_permintaan = $this->Permintaan_barang_model->get_permintaan($id_permintaan);

                $jumlah_permintaan = $database_permintaan['jumlah'];

                // $jumlah_approved = $database_permintaan['jumlah_approved'];

                $id_barang = $this->Permintaan_barang_model->get_id_barang($id_permintaan);


                $barang = $this->Barang_model->get_id($id_barang);

                if ($barang['temp'] == 1) {

                    $last_kd_barang =  $this->Barang_model->get_next_kode_barang($item['kd_subkategori']);

                    $kd_barang = $last_kd_barang;
                    $barang['kd_barang'] = $kd_barang;
                    $barang['temp'] = 0;
                    $barang['harga_satuan'] = $item['harga_satuan'];
                    $barang['kategori'] = 'ATK';
                    $barang['kd_subkategori'] = $item['kd_subkategori'];

                    $this->Barang_model->ubah_api($barang['id_barang'], $barang);
                }

                if ($barang['stok'] < $jumlah_approved) {
                    $this->response([
                        'status' => 'Gagal',
                        'message' => 'Jumlah Approved ' . $barang['nama_barang'] . ' melebihi sisa stok, jika ingin memenuhi kebutuhan melebihi stok, silahkan centang Pengajuan Otomatis.'
                    ], 200);
                    return;
                } else if ($jumlah_approved > $jumlah_permintaan) {
                    $this->response([
                        'status' => 'Gagal',
                        'message' => 'Jumlah Approved melebihi jumlah permintaan'
                    ], 200);
                    return;
                } else {

                    $update_data = [
                        'jumlah_approved' => $jumlah_approved,
                        'status' => $status,
                        'is_buat_pengajuan' => $item['is_buat_pengajuan'],
                    ];

                    if ($item['is_buat_pengajuan']) {
                        // Jika is_buat_pengajuan adalah true, buat pengajuan terlebih dahulu
                        $jumlah_belum_dipenuhi = $database_permintaan['jumlah'] - $jumlah_approved;

                        $barang = $this->Barang_model->get_id($database_permintaan['id_barang']);



                        $pengajuan_data = [
                            'id_barang' => $database_permintaan['id_barang'],
                            'id_user' => $id_user,
                            'id_ruangan' => $database_permintaan['id_ruangan'],
                            'tanggal' => date('Y-m-d'),
                            'jumlah' => $jumlah_belum_dipenuhi,
                            'harga_pengajuan' => $barang['harga_satuan'],
                            'kd_program' => $database_permintaan['kd_program'],
                            'kd_kegiatan' => $database_permintaan['kd_kegiatan'],
                            'kd_kro' => $database_permintaan['kd_kro'],
                            'kd_ro' => $database_permintaan['kd_ro'],
                            'kd_komponen' => $database_permintaan['kd_komponen'],
                            'status' => 'Pending'
                        ];
                        $inserted_id_pengajuan = $this->Pengajuan_barang_model->insert($pengajuan_data);



                        $inserted_id_transaksi_permintaan = $id_transaksi_permintaan;


                        $permintaan_data = [
                            'id_transaksi' => $inserted_id_transaksi_permintaan,
                            'id_barang' => $database_permintaan['id_barang'],
                            'id_user' => $id_user,
                            'id_ruangan' => $database_permintaan['id_ruangan'],
                            'tanggal' => date('Y-m-d'),
                            'jumlah' => $jumlah_belum_dipenuhi,
                            'status' => 'Pengadaan',
                            'jumlah_approved' => $jumlah_belum_dipenuhi
                        ];
                        $inserted_id_permintaan = $this->Permintaan_barang_model->insert($permintaan_data);

                        if (!$inserted_id_pengajuan || !$inserted_id_permintaan) {
                            // Jika gagal insert pengajuan atau permintaan, rollback transaksi
                            $this->db->trans_rollback();
                            $this->response([
                                'status' => 'Gagal',
                                'message' => 'Gagal membuat pengajuan atau permintaan lanjutan'
                            ], 200);
                        }



                        $update_data['id_pengajuan_lanjutan'] = $inserted_id_pengajuan;
                        $update_data['id_permintaan_lanjutan'] = $inserted_id_permintaan;
                    }

                    $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);

                    $sisa_permintaan = $jumlah_approved;

                    // Ambil stok masuk yang tersedia secara FIFO
                    $stok_masuk = $this->Barang_masuk_model->get_stok_masuk_fifo($id_barang);

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
                    $this->Barang_model->kurangi_stok_barang($id_barang, $jumlah_approved);

                    $updated_barang = $this->Barang_model->get_id($id_barang);

                    $update_transaksi_stok = $this->Permintaan_barang_model->simpan_current_stok($id_permintaan, $updated_barang['stok']);

                    if ($update_transaksi_stok) {

                        $this->db->trans_rollback();

                        $this->response(
                            [
                                'status' => 'Gagal',
                                'message' => 'Gagal update ' . $updated_barang['nama_barang'] . ' transaksi keluar stok'
                            ],
                            200
                        );

                        return;
                    }

                    $this->db->trans_commit();
                }
            } else if ($status == 'Rejected' && $database_permintaan['status'] == 'Approved') {
                // Hanya update status permintaan
                $this->db->trans_begin();

                $update_data = [
                    'jumlah_approved' => 0,
                    'status' => $status,
                    'is_buat_pengajuan' => false,
                ];

                if ($database_permintaan['is_buat_pengajuan']) {
                    // Jika permintaan ini sudah dibuat pengajuan, rollback pengajuan juga
                    $hapus_pengajuan = $this->Pengajuan_barang_model->hapus_pengajuan($database_permintaan['id_pengajuan_lanjutan']);
                    $hapus_permintaan = $this->Permintaan_barang_model->hapus_permintaan($database_permintaan['id_permintaan_lanjutan']);

                    if (!$hapus_pengajuan && !$hapus_permintaan) {
                        $this->db->trans_rollback();
                        $this->response([
                            'status' => 'Gagal',
                            'message' => 'Gagal rollback pengajuan dan permintaan'
                        ], 200);
                        return;
                    }

                    $update_data['id_pengajuan_lanjutan'] = null;
                    $update_data['id_permintaan_lanjutan'] = null;
                }

                // Ambil jumlah yang sudah di-approved sebelumnya
                $jumlah_approved = $database_permintaan['jumlah_approved'];

                // Ambil id_barang dari permintaan
                $id_barang = $database_permintaan['id_barang'];

                // Tambahkan kembali stok utama
                $this->Barang_model->tambah_stok_barang($id_barang, $jumlah_approved);

                // Ambil data barang keluar dari FIFO sebelumnya jika ada
                $barang_keluar = $this->Permintaan_barang_model->get_log_keluar_by_permintaan($id_permintaan); // butuh log catatan sebelumnya
                // var_dump($barang_keluar);
                // die;


                foreach ($barang_keluar as $keluar) {
                    $id_masuk = $keluar['id_barang_masuk'];
                    $jumlah_dikembalikan = $keluar['jumlah_keluar'];
                    $id_bk_detail = $keluar['id_bk_detail'];

                    // Tambahkan kembali ke entri barang_masuk
                    $this->Barang_masuk_model->tambah_barang_masuk($id_masuk, $jumlah_dikembalikan);
                    // Hapus log keluar FIFO
                    $this->Permintaan_barang_model->soft_delete_keluar($id_bk_detail);
                }

                // Update status permintaan
                $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);

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
            } else if ($status == 'Pending' && $database_permintaan['status'] == 'Approved') {
                // Hanya update status permintaan
                $this->db->trans_begin();

                $update_data = [
                    'jumlah_approved' => $database_permintaan['jumlah_approved'],
                    'status' => $status,
                    'is_buat_pengajuan' => false,
                ];

                if ($database_permintaan['is_buat_pengajuan']) {
                    // Jika permintaan ini sudah dibuat pengajuan, rollback pengajuan juga
                    $hapus_pengajuan = $this->Pengajuan_barang_model->hapus_pengajuan($database_permintaan['id_pengajuan_lanjutan']);
                    $hapus_permintaan = $this->Permintaan_barang_model->hapus_permintaan($database_permintaan['id_permintaan_lanjutan']);

                    if (!$hapus_pengajuan && !$hapus_permintaan) {
                        $this->db->trans_rollback();
                        $this->response([
                            'status' => 'Gagal',
                            'message' => 'Gagal rollback pengajuan dan permintaan'
                        ], 200);
                        return;
                    }

                    $update_data['id_pengajuan_lanjutan'] = null;
                    $update_data['id_permintaan_lanjutan'] = null;
                }

                // Ambil jumlah yang sudah di-approved sebelumnya
                $jumlah_approved = $database_permintaan['jumlah_approved'];

                // Ambil id_barang dari permintaan
                $id_barang = $database_permintaan['id_barang'];

                // Tambahkan kembali stok utama
                $this->Barang_model->tambah_stok_barang($id_barang, $jumlah_approved);

                // Ambil data barang keluar dari FIFO sebelumnya jika ada
                $barang_keluar = $this->Permintaan_barang_model->get_log_keluar_by_permintaan($id_permintaan); // butuh log catatan sebelumnya
                // var_dump($barang_keluar);
                // die;


                foreach ($barang_keluar as $keluar) {
                    $id_masuk = $keluar['id_barang_masuk'];
                    $jumlah_dikembalikan = $keluar['jumlah_keluar'];
                    $id_bk_detail = $keluar['id_bk_detail'];

                    // Tambahkan kembali ke entri barang_masuk
                    $this->Barang_masuk_model->tambah_barang_masuk($id_masuk, $jumlah_dikembalikan);
                    // Hapus log keluar FIFO
                    $this->Permintaan_barang_model->soft_delete_keluar($id_bk_detail);
                }

                // Update status permintaan
                $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);

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
                $update_data = [
                    'jumlah_approved' => 0,
                    'status' => $status,
                ];
                $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);
            }
        }

        // // Response success
        // $this->response([
        //     'status' => 'success',
        //     'message' => 'Status updated and stock adjusted successfully.'
        // ], 200);
    }
    
}
