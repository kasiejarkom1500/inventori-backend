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

        $this->load->model('Kabupaten_model');
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
        $signerGudang = $this->User_model->get_user_by_satker_role($this->decoded_token['id_satker'],1);
        $signerApprover = $this->User_model->get_user_by_satker_role($this->decoded_token['id_satker'],7);
        $headerSurat = $this->Kabupaten_model->get_all($this->decoded_token['id_satker']);


        if ($permintaan_barang) {
            $this->response([
                'status' => true,
                'data' => $permintaan_barang,
                'signerGudang' => $signerGudang,
                'signerApprover' => $signerApprover,
                'headerSurat' => $headerSurat
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
            'id_user' =>  $data['0']['id_user'],
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
            // var_dump($item);
            // die;
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


    private function cek_butuh_transaksi($data)
    {

        foreach ($data as $item) {
            // var_dump($item);
            // die;
            if ($item['status'] === 'Approved Gudang') {
                $permintaan = $this->Permintaan_barang_model->get_permintaan($item['id_permintaan']);
                if (!empty($item['is_buat_pengajuan'])) {
                    return true;
                }
            }
        }
        return false;
    }

    private function buat_transaksi_permintaan($item)
    {
        $permintaan = $this->Permintaan_barang_model->get_permintaan($item['id_permintaan']);
        return $this->Permintaan_barang_model->insert_transaksi_permintaan([
            'id_ruangan' => $permintaan['id_ruangan'],
            'id_user' => $this->decoded_token['id_user'],
            'approved' => '1'
        ]);
    }

    private function response_error($message)
    {
        $this->response(['status' => 'Gagal', 'message' => $message], 200);
    }


    private function handle_approved($item, $permintaan, $id_transaksi_permintaan)
    {
        $id_user = $this->decoded_token['id_user'];
        $id_permintaan = $item['id_permintaan'];
        $jumlah_approved = $item['jumlah_approved'];

        $this->db->trans_begin();

        $permintaan = $this->Permintaan_barang_model->get_permintaan($id_permintaan); // refresh

        $jumlah_permintaan = $permintaan['jumlah'];
        $id_barang = $this->Permintaan_barang_model->get_id_barang($id_permintaan);
        $barang = $this->Barang_model->get_id($id_barang);

        // Jika barang baru (temp)
        if ($barang['temp'] == 1) {
            $barang['kd_barang'] = $this->Barang_model->get_next_kode_barang($item['kd_subkategori']);
            $barang['temp'] = 0;
            // $barang['harga_satuan'] = $item['harga_satuan'];
            $barang['kategori'] = 'ATK';
            $barang['kd_subkategori'] = $item['kd_subkategori'];

            $this->Barang_model->ubah_api($barang['id_barang'], $barang);
        }

        // Validasi stok
        if ($barang['stok'] < $jumlah_approved) {
            $this->db->trans_rollback();
            return $this->response_error("Jumlah Approved {$barang['nama_barang']} melebihi stok yang tersedia.");
        }

        if ($jumlah_approved > $jumlah_permintaan) {
            $this->db->trans_rollback();
            return $this->response_error("Jumlah Approved melebihi jumlah permintaan.");
        }

        // Update status permintaan
        $update_data = [
            'jumlah_approved' => $jumlah_approved,
            'status' => 'Approved',
            'is_buat_pengajuan' => $item['is_buat_pengajuan'],
        ];

        // Jika buat pengajuan baru
        if (!empty($item['is_buat_pengajuan'])) {
            $jumlah_belum_dipenuhi = $permintaan['jumlah'] - $jumlah_approved;

            $pengajuan_data = [
                'id_barang' => $permintaan['id_barang'],
                'id_user' => $id_user,
                'id_ruangan' => $permintaan['id_ruangan'],
                'tanggal' => date('Y-m-d'),
                'jumlah' => $jumlah_belum_dipenuhi,
                'harga_pengajuan' => $barang['harga_satuan'],
                'kd_program' => $permintaan['kd_program'],
                'kd_kegiatan' => $permintaan['kd_kegiatan'],
                'kd_kro' => $permintaan['kd_kro'],
                'kd_ro' => $permintaan['kd_ro'],
                'kd_komponen' => $permintaan['kd_komponen'],
                'status' => 'Pending'
            ];

            $id_pengajuan = $this->Pengajuan_barang_model->insert($pengajuan_data);

            $id_permintaan_baru = $this->Permintaan_barang_model->insert([
                'id_transaksi' => $id_transaksi_permintaan,
                'id_barang' => $permintaan['id_barang'],
                'id_user' => $id_user,
                'id_ruangan' => $permintaan['id_ruangan'],
                'tanggal' => date('Y-m-d'),
                'jumlah' => $jumlah_belum_dipenuhi,
                'status' => 'Pengadaan',
                'jumlah_approved' => $jumlah_belum_dipenuhi,
                'kd_program' => $permintaan['kd_program'],
                'kd_kegiatan' => $permintaan['kd_kegiatan'],
                'kd_kro' => $permintaan['kd_kro'],
                'kd_ro' => $permintaan['kd_ro'],
                'kd_komponen' => $permintaan['kd_komponen'],
            ]);

            if (!$id_pengajuan || !$id_permintaan_baru) {
                $this->db->trans_rollback();
                return $this->response_error("Gagal membuat pengajuan atau permintaan lanjutan.");
            }

            $update_data['id_pengajuan_lanjutan'] = $id_pengajuan;
            $update_data['id_permintaan_lanjutan'] = $id_permintaan_baru;
        }

        // Simpan status
        $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);

        // Kurangi stok dari barang masuk (FIFO)
        $stok_masuk = $this->Barang_masuk_model->get_stok_masuk_fifo($id_barang);
        $sisa = $jumlah_approved;

        foreach ($stok_masuk as $stok) {
            if ($sisa <= 0) break;

            $id_masuk = $stok['id_barang_masuk'];
            $tersedia = $stok['sisa_stok'];

            if ($tersedia == 0) continue;

            $ambil = min($sisa, $tersedia);
            $sisa -= $ambil;

            if (!$this->Barang_masuk_model->kurangi_barang_masuk($id_masuk, $ambil)) {
                $this->db->trans_rollback();
                return $this->response_error("Gagal mengurangi stok barang masuk.");
            }

            if (!$this->Permintaan_barang_model->catat_keluar_fifo([
                'id_permintaan' => $id_permintaan,
                'id_barang_masuk' => $id_masuk,
                'jumlah_keluar' => $ambil,
                'harga_satuan' => $stok['harga']
            ])) {
                $this->db->trans_rollback();
                return $this->response_error("Gagal mencatat transaksi keluar FIFO.");
            }
        }

        // Kurangi stok akhir dan catat
        $this->Barang_model->kurangi_stok_barang($id_barang, $jumlah_approved);
        $updated_barang = $this->Barang_model->get_id($id_barang);

        // var_dump($updated_barang);
        // die;

        if ($this->Permintaan_barang_model->simpan_current_stok($id_permintaan, $updated_barang['stok'])) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
            return $this->response_error("Gagal menyimpan catatan stok akhir.");
        }
    }

    private function handle_approved_ketua($item, $id_transaksi_permintaan)
    {
        $id_user = $this->decoded_token['id_user'];
        $id_permintaan = $item['id_permintaan'];
        $jumlah_approved = $item['jumlah_approved'];

        $this->db->trans_begin();

        $permintaan = $this->Permintaan_barang_model->get_permintaan($id_permintaan); // refresh

        $jumlah_permintaan = $permintaan['jumlah'];
        $id_barang = $this->Permintaan_barang_model->get_id_barang($id_permintaan);
        $barang = $this->Barang_model->get_id($id_barang);

        $update_data = [
            'status' => 'Approved',
        ];

        // Jika buat pengajuan baru
        if (!empty($item['is_buat_pengajuan'])) {
            $jumlah_belum_dipenuhi = $permintaan['jumlah'] - $jumlah_approved;

            $pengajuan_data = [
                'id_barang' => $permintaan['id_barang'],
                'id_user' => $id_user,
                'id_ruangan' => $permintaan['id_ruangan'],
                'tanggal' => date('Y-m-d'),
                'jumlah' => $jumlah_belum_dipenuhi,
                'harga_pengajuan' => $barang['harga_satuan'],
                'kd_program' => $permintaan['kd_program'],
                'kd_kegiatan' => $permintaan['kd_kegiatan'],
                'kd_kro' => $permintaan['kd_kro'],
                'kd_ro' => $permintaan['kd_ro'],
                'kd_komponen' => $permintaan['kd_komponen'],
                'status' => 'Pending'
            ];

            $id_pengajuan = $this->Pengajuan_barang_model->insert($pengajuan_data);

            $id_permintaan_baru = $this->Permintaan_barang_model->insert([
                'id_transaksi' => $id_transaksi_permintaan,
                'id_barang' => $permintaan['id_barang'],
                'id_user' => $id_user,
                'id_ruangan' => $permintaan['id_ruangan'],
                'tanggal' => date('Y-m-d'),
                'jumlah' => $jumlah_belum_dipenuhi,
                'status' => 'Pengadaan',
                'jumlah_approved' => $jumlah_belum_dipenuhi,
                'kd_program' => $permintaan['kd_program'],
                'kd_kegiatan' => $permintaan['kd_kegiatan'],
                'kd_kro' => $permintaan['kd_kro'],
                'kd_ro' => $permintaan['kd_ro'],
                'kd_komponen' => $permintaan['kd_komponen'],
            ]);

            if (!$id_pengajuan || !$id_permintaan_baru) {
                $this->db->trans_rollback();
                return $this->response_error("Gagal membuat pengajuan atau permintaan lanjutan.");
            }

            $update_data['id_pengajuan_lanjutan'] = $id_pengajuan;
            $update_data['id_permintaan_lanjutan'] = $id_permintaan_baru;

            // Simpan permintaan dan pengajuan baru
            
        }

        $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);

        // $this->Permintaan_barang_model->update_transaksi_permintaan($permintaan['id_transaksi'], [
        //     'approve_gudang' => '1',
        // ]);

        // Kurangi stok dari barang masuk (FIFO)
        $stok_masuk = $this->Barang_masuk_model->get_stok_masuk_fifo($id_barang);
        $sisa = $jumlah_approved;

        foreach ($stok_masuk as $stok) {
            if ($sisa <= 0) break;

            $id_masuk = $stok['id_barang_masuk'];
            $tersedia = $stok['sisa_stok'];

            if ($tersedia == 0) continue;

            $ambil = min($sisa, $tersedia);
            $sisa -= $ambil;

            if (!$this->Barang_masuk_model->kurangi_barang_masuk($id_masuk, $ambil)) {
                $this->db->trans_rollback();
                return $this->response_error("Gagal mengurangi stok barang masuk.");
            }

            if (!$this->Permintaan_barang_model->catat_keluar_fifo([
                'id_permintaan' => $id_permintaan,
                'id_barang_masuk' => $id_masuk,
                'jumlah_keluar' => $ambil,
                'harga_satuan' => $stok['harga']
            ])) {
                $this->db->trans_rollback();
                return $this->response_error("Gagal mencatat transaksi keluar FIFO.");
            }
        }

        // Kurangi stok akhir dan catat
        $this->Barang_model->kurangi_stok_barang($id_barang, $jumlah_approved);
        $updated_barang = $this->Barang_model->get_id($id_barang);

        // var_dump($updated_barang);
        // die;

        if ($this->Permintaan_barang_model->simpan_current_stok($id_permintaan, $updated_barang['stok'])) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
            return $this->response_error("Gagal menyimpan catatan stok akhir.");
        }
    }

    private function handle_rejected_from_approved($item, $permintaan)
    {
        $this->db->trans_begin();

        $update_data = [
            'jumlah_approved' => 0,
            'status' => 'Rejected',
            'is_buat_pengajuan' => false,
        ];

        // Jika ada pengajuan/permintaan lanjutan sebelumnya → hapus
        if (!empty($permintaan['is_buat_pengajuan'])) {
            $hapus_pengajuan = $this->Pengajuan_barang_model->hapus_pengajuan($permintaan['id_pengajuan_lanjutan']);
            $hapus_permintaan = $this->Permintaan_barang_model->hapus_permintaan($permintaan['id_permintaan_lanjutan']);

            if (!$hapus_pengajuan || !$hapus_permintaan) {
                $this->db->trans_rollback();
                return $this->response_error("Gagal rollback pengajuan dan permintaan lanjutan.");
            }

            $update_data['id_pengajuan_lanjutan'] = null;
            $update_data['id_permintaan_lanjutan'] = null;
        }

        // Tambahkan kembali stok barang
        $id_barang = $permintaan['id_barang'];
        $jumlah_approved = $permintaan['jumlah_approved'];

        $this->Barang_model->tambah_stok_barang($id_barang, $jumlah_approved);

        // Ambil log FIFO yang pernah dicatat
        $log_fifo = $this->Permintaan_barang_model->get_log_keluar_by_permintaan($item['id_permintaan']);

        foreach ($log_fifo as $keluar) {
            $id_masuk = $keluar['id_barang_masuk'];
            $jumlah_dikembalikan = $keluar['jumlah_keluar'];
            $id_bk_detail = $keluar['id_bk_detail'];

            // Tambahkan kembali stok masuk
            $this->Barang_masuk_model->tambah_barang_masuk($id_masuk, $jumlah_dikembalikan);

            // Soft delete log keluar
            $this->Permintaan_barang_model->soft_delete_keluar($id_bk_detail);
        }

        // Update status permintaan
        $this->Permintaan_barang_model->update_status($item['id_permintaan'], $update_data);

        // Update stok catatan akhir
        $updated_barang = $this->Barang_model->get_id($id_barang);
        $stok_saved = $this->Permintaan_barang_model->simpan_current_stok($item['id_permintaan'], $updated_barang['stok']);

        if (!$stok_saved) {
            $this->db->trans_rollback();
            return $this->response_error("Gagal update rollback stok keluar.");
        }

        $this->db->trans_commit();
    }

    private function handle_rejected_from_approved_ketua($item, $permintaan)
    {
        $this->db->trans_begin();

        // var_dump($item);
        // die;

        $update_data = [
            'jumlah_approved' => 0,
            'status' => 'Rejected',
            'is_buat_pengajuan' => false,
        ];

        // Jika ada pengajuan/permintaan lanjutan sebelumnya → hapus
        if (!empty($item['is_buat_pengajuan'])) {
            $hapus_pengajuan = $this->Pengajuan_barang_model->hapus_pengajuan($item['id_pengajuan_lanjutan']);
            $hapus_permintaan = $this->Permintaan_barang_model->hapus_permintaan($item['id_permintaan_lanjutan']);

            if (!$hapus_pengajuan || !$hapus_permintaan) {
                $this->db->trans_rollback();
                return $this->response_error("Gagal rollback pengajuan dan permintaan lanjutan.");
            }

            $update_data['id_pengajuan_lanjutan'] = null;
            $update_data['id_permintaan_lanjutan'] = null;
        }

        // Tambahkan kembali stok barang
        $id_barang = $item['id_barang'];
        $jumlah_approved = $item['jumlah_approved'];

        $this->Barang_model->tambah_stok_barang($id_barang, $jumlah_approved);

        // Ambil log FIFO yang pernah dicatat
        $log_fifo = $this->Permintaan_barang_model->get_log_keluar_by_permintaan($item['id_permintaan']);

        foreach ($log_fifo as $keluar) {
            $id_masuk = $keluar['id_barang_masuk'];
            $jumlah_dikembalikan = $keluar['jumlah_keluar'];
            $id_bk_detail = $keluar['id_bk_detail'];

            // Tambahkan kembali stok masuk
            $this->Barang_masuk_model->tambah_barang_masuk($id_masuk, $jumlah_dikembalikan);

            // Soft delete log keluar
            $this->Permintaan_barang_model->soft_delete_keluar($id_bk_detail);
        }

        // Update status permintaan
        $this->Permintaan_barang_model->update_status($item['id_permintaan'], $update_data);



        // Update stok catatan akhir
        $updated_barang = $this->Barang_model->get_id($id_barang);
        $stok_saved = $this->Permintaan_barang_model->simpan_current_stok($item['id_permintaan'], $updated_barang['stok']);

        if (!$stok_saved) {
            $this->db->trans_rollback();
            return $this->response_error("Gagal update rollback stok keluar.");
        }

        $this->db->trans_commit();
    }

    private function handle_pending_from_approved($item, $permintaan)
    {
        $this->db->trans_begin();

        $update_data = [
            'jumlah_approved' => $permintaan['jumlah_approved'],
            'status' => 'Pending',
            'is_buat_pengajuan' => false,
        ];

        // Jika sebelumnya buat pengajuan, hapus pengajuan dan permintaan lanjutan
        if (!empty($permintaan['is_buat_pengajuan'])) {
            $hapus_pengajuan = $this->Pengajuan_barang_model->hapus_pengajuan($permintaan['id_pengajuan_lanjutan']);
            $hapus_permintaan = $this->Permintaan_barang_model->hapus_permintaan($permintaan['id_permintaan_lanjutan']);

            if (!$hapus_pengajuan || !$hapus_permintaan) {
                $this->db->trans_rollback();
                return $this->response_error("Gagal rollback pengajuan dan permintaan.");
            }

            $update_data['id_pengajuan_lanjutan'] = null;
            $update_data['id_permintaan_lanjutan'] = null;
        }

        // Kembalikan stok utama
        $id_barang = $permintaan['id_barang'];
        $jumlah_approved = $permintaan['jumlah_approved'];

        $this->Barang_model->tambah_stok_barang($id_barang, $jumlah_approved);

        // Ambil log FIFO yang harus dikembalikan
        $log_keluar = $this->Permintaan_barang_model->get_log_keluar_by_permintaan($item['id_permintaan']);

        foreach ($log_keluar as $keluar) {
            $id_masuk = $keluar['id_barang_masuk'];
            $jumlah_dikembalikan = $keluar['jumlah_keluar'];
            $id_bk_detail = $keluar['id_bk_detail'];

            $this->Barang_masuk_model->tambah_barang_masuk($id_masuk, $jumlah_dikembalikan);
            $this->Permintaan_barang_model->soft_delete_keluar($id_bk_detail);
        }

        // Update status permintaan
        $this->Permintaan_barang_model->update_status($item['id_permintaan'], $update_data);

        // Update catatan stok terakhir
        $updated_barang = $this->Barang_model->get_id($id_barang);
        $stok_saved = $this->Permintaan_barang_model->simpan_current_stok($item['id_permintaan'], $updated_barang['stok']);

        if (!$stok_saved) {
            $this->db->trans_rollback();
            return $this->response_error("Gagal update rollback stok keluar.");
        }

        $this->db->trans_commit();
    }


    private function handle_rejected_from_pending($item, $permintaan)
    {
        // Update status permintaan saja, tidak perlu transaksi
        $update_data = [
            'jumlah_approved' => 0,
            'status' => 'Rejected',
        ];

        $this->Permintaan_barang_model->update_status($item['id_permintaan'], $update_data);
    }


    public function update_status_put()
    {
        $data = json_decode($this->input->raw_input_stream, true);

        if (!is_array($data) || empty($data)) {
            return $this->response_error('Invalid input. Please provide valid JSON data.');
        }

        $butuh_transaksi = $this->cek_butuh_transaksi($data);
        $id_transaksi = $butuh_transaksi ? $this->buat_transaksi_permintaan($data[0]) : null;

        // var_dump($butuh_transaksi);
        // die;

        foreach ($data as $item) {
            if (!isset($item['id_permintaan'], $item['status'], $item['jumlah_approved'])) {
                return $this->response_error('Invalid data structure. Each item must contain id_permintaan, status, and jumlah.');
            }

            $permintaan = $this->Permintaan_barang_model->get_permintaan($item['id_permintaan']);

            //Cek jika status sebelumnya pengadaan dan sekarang jadi reject atau pending maka gagalkan proses
            if ($permintaan['status'] === 'Pengadaan' && ($item['status'] === 'Rejected' || $item['status'] === 'Pending')) {
                return $this->response(
                    [
                        'status' => 'Gagal',
                        'message' => 'Permintaan yang dalam proses pengadaan tidak dapat dibatalkan'
                    ],
                    200
                );
            }



            if ($item['status'] === 'Approved') {
                $this->handle_approved($item, $permintaan, $id_transaksi);
            } else if ($item['status'] === 'Rejected' && $permintaan['status'] === 'Approved') {
                $this->handle_rejected_from_approved($item, $permintaan);
            } else if ($item['status'] === 'Pending' && $permintaan['status'] === 'Approved') {
                $this->handle_pending_from_approved($item, $permintaan);
            } else if ($item['status'] === 'Rejected' && $permintaan['status'] === 'Pending') {
                $this->handle_rejected_from_pending($item, $permintaan);
            }
        }

        $this->response(['status' => 'success', 'message' => 'Status updated and stock adjusted successfully.'], 200);
    }


    public function approve_transaksi_get($id_transaksi, $status)
    {
        $update_trans = $this->Permintaan_barang_model->approve_transaksi($id_transaksi, $status);
        // var_dump($update_trans);
        // die;

        if ($update_trans) {
            $this->response([
                'status' => 'success',
                'message' => 'Status Permintaan Berhasil Diubah.'
            ], 200);
        } else {
            $this->response([
                'status' => 'Gagal',
                'message' => 'Status Permintaan Gagal Diubah.'
            ], 200);
        }
    }

    public function approve_gudang_get($id_transaksi, $status)
    {

        // var_dump($id_transaksi);
        // die;
        $permintaan = $this->Permintaan_barang_model->get_permintaan_by_id_transaksi($id_transaksi);

        $transaksi_permintaan = $this->Permintaan_barang_model->get_transaksi_permintaan($id_transaksi);

        if ($status === '1') {
            $butuh_transaksi = $this->cek_butuh_transaksi($permintaan);
            $id_transaksi_baru = $butuh_transaksi ? $this->buat_transaksi_permintaan($permintaan[0]) : null;
            foreach ($permintaan as $item) {
                if (!isset($item['id_permintaan'], $item['status'], $item['jumlah_approved'])) {
                    return $this->response_error('Invalid data structure. Each item must contain id_permintaan, status, and jumlah approved.');
                }
                $this->handle_approved_ketua($item, $id_transaksi_baru);
            }
            $this->Permintaan_barang_model->update_transaksi_permintaan($transaksi_permintaan['id'], [
                'approve_gudang' => '1',
            ]);
        } else if ($status === '0' && $transaksi_permintaan['approve_gudang'] === '1') {
            foreach ($permintaan as $item) {
                if (!isset($item['id_permintaan'], $item['status'], $item['jumlah_approved'])) {
                    return $this->response_error('Invalid data structure. Each item must contain id_permintaan, status, and jumlah approved.');
                }
                $this->handle_rejected_from_approved_ketua($item, $permintaan);
            }

            $this->Permintaan_barang_model->update_transaksi_permintaan($transaksi_permintaan['id'], [
                'approve_gudang' => '0',
            ]);
        }

        $this->response(['status' => 'success', 'message' => 'Status updated and stock adjusted successfully.'], 200);
    }


    public function update_status_admin_put()
    {
        $data = json_decode($this->input->raw_input_stream, true);

        if (!is_array($data) || empty($data)) {
            return $this->response_error('Invalid input. Please provide valid JSON data.');
        }

        foreach ($data as $item) {
            if (!isset($item['id_permintaan'], $item['status'], $item['jumlah_approved'])) {
                return $this->response_error('Invalid data structure. Each item must contain id_permintaan, status, and jumlah.');
            }

            $id_user = $this->decoded_token['id_user'];
            $id_permintaan = $item['id_permintaan'];
            $jumlah_approved = $item['jumlah_approved'];

            $permintaan = $this->Permintaan_barang_model->get_permintaan($id_permintaan);

            //Cek jika status sebelumnya pengadaan dan sekarang jadi reject atau pending maka gagalkan proses
            if ($permintaan['status'] === 'Pengadaan' && ($item['status'] === 'Rejected' || $item['status'] === 'Pending')) {
                return $this->response(
                    [
                        'status' => 'Gagal',
                        'message' => 'Permintaan yang dalam proses pengadaan tidak dapat dibatalkan'
                    ],
                    200
                );
            }





            if ($item['status'] === 'Approved Gudang') {
                $jumlah_permintaan = $permintaan['jumlah'];
                $id_barang = $this->Permintaan_barang_model->get_id_barang($id_permintaan);
                $barang = $this->Barang_model->get_id($id_barang);

                // Validasi stok
                if ($barang['stok'] < $jumlah_approved) {
                    $this->db->trans_rollback();
                    return $this->response_error("Jumlah Approved {$barang['nama_barang']} melebihi stok yang tersedia.");
                } else if ($jumlah_approved > $jumlah_permintaan) {
                    $this->db->trans_rollback();
                    return $this->response_error("Jumlah Approved {$barang['nama_barang']} melebihi jumlah permintaan.");
                }

                // Jika barang baru (temp)
                if ($barang['temp'] == 1) {
                    $barang['kd_barang'] = $this->Barang_model->get_next_kode_barang($item['kd_subkategori']);
                    $barang['temp'] = 0;
                    $barang['kabupaten_id'] = $this->decoded_token['id_satker'];
                    $barang['kategori'] = 'ATK';
                    $barang['kd_subkategori'] = $item['kd_subkategori'];

                    $this->Barang_model->ubah_api($barang['id_barang'], $barang);
                }

                // Update status permintaan
                $update_data = [
                    'jumlah_approved' => $jumlah_approved,
                    'status' => 'Approved Gudang',
                    'is_buat_pengajuan' => $item['is_buat_pengajuan'],
                ];
                // Simpan status
                $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);
            } else if ($item['status'] === 'Rejected' && $permintaan['status'] === 'Approved Gudang') {
                $update_data = [
                    'jumlah_approved' => 0,
                    'status' => 'Rejected',
                    'is_buat_pengajuan' => false,
                ];
                // Update status permintaan
                $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);
            } else if ($item['status'] === 'Pending' && $permintaan['status'] === 'Approved Gudang') {
                $update_data = [
                    'jumlah_approved' => $permintaan['jumlah_approved'],
                    'status' => 'Pending',
                    'is_buat_pengajuan' => false,
                ];
                // Update status permintaan
                $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);
            } else if ($item['status'] === 'Rejected' && $permintaan['status'] === 'Pending') {
                $this->handle_rejected_from_pending($item, $permintaan);
            }
        }

        $this->response(['status' => 'success', 'message' => 'Status updated and stock adjusted successfully.'], 200);
    }
}
