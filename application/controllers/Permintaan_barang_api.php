<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/ApiController.php';

use chriskacerguis\RestServer\RestController;

class Permintaan_barang_api extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->set_cors_headers();

        // Hentikan eksekusi untuk preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Max-Age: 86400'); // Cache preflight response for 24 hours
            exit(0);
        }

        // Load models, helpers, config
        $this->load->model([
            'Kabupaten_model',
            'Permintaan_barang_model',
            'Pengajuan_barang_model',
            'Barang_model',
            'Barang_masuk_model',
            'User_model',
            'ruangan_model',
        ]);

        $this->load->helper(['form', 'url']);
        $this->load->config('rest');
    }

    /**
     * Set common CORS headers
     */
    private function set_cors_headers()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    }

    public function transaksi_permintaan_get()
    {
        $permintaan_barang = $this->Permintaan_barang_model->get_grouped_by_date($this->decoded_token['id_satker']);

        if ($permintaan_barang) {
            $this->response(['status' => true, 'data' => $permintaan_barang], RestController::HTTP_OK);
        } else {
            $this->response(['status' => false, 'message' => 'Data tidak ditemukan', 'data' => []], RestController::HTTP_OK);
        }
    }

    public function permintaan_detail_get($id_ruangan, $tanggal, $id_transaksi)
    {
        // Validasi format tanggal
        if (!DateTime::createFromFormat('Y-m-d', $tanggal)) {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], RestController::HTTP_NOT_FOUND);
        }

        $permintaan_barang = $this->Permintaan_barang_model->get_by_id_transaksi($id_transaksi, $id_ruangan, $tanggal);
        $signerGudang = $this->User_model->get_user_by_satker_role($this->decoded_token['id_satker'], 1);
        $signerApprover = $this->User_model->get_user_by_satker_role($this->decoded_token['id_satker'], 7);
        $headerSurat = $this->Kabupaten_model->get_all($this->decoded_token['id_satker']);

        if ($permintaan_barang) {
            $this->response([
                'status' => true,
                'data' => $permintaan_barang,
                'signerGudang' => $signerGudang,
                'signerApprover' => $signerApprover,
                'headerSurat' => $headerSurat,
            ], RestController::HTTP_OK);
        }

        return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], RestController::HTTP_NOT_FOUND);
    }

    public function cetak_permintaan_get($id_ruangan, $tanggal)
    {
        if (!DateTime::createFromFormat('Y-m-d', $tanggal)) {
            return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], RestController::HTTP_NOT_FOUND);
        }

        $permintaan_barang = $this->Permintaan_barang_model->get_approved_by_date($id_ruangan, $tanggal);

        if ($permintaan_barang) {
            return $this->response(['status' => true, 'data' => $permintaan_barang], RestController::HTTP_OK);
        }

        return $this->response(['status' => false, 'message' => 'Data tidak ditemukan'], RestController::HTTP_NOT_FOUND);
    }

    public function simpan_post()
    {
        $data = json_decode($this->input->raw_input_stream, true);

        if (!is_array($data) || empty($data)) {
            return $this->response(['status' => 'error', 'message' => 'Invalid input. Please provide valid JSON data.'], RestController::HTTP_BAD_REQUEST);
        }

        $tanggal = date('Y-m-d');

        $transaksi_permintaan = [
            'id_ruangan' => $data[0]['id_ruangan'],
            'id_user' => $data[0]['id_user'],
        ];

        $id_transaksi_permintaan = $this->Permintaan_barang_model->insert_transaksi_permintaan($transaksi_permintaan);

        foreach ($data as $item) {
            $this->db->trans_begin();

            if (!isset($item['jumlah'], $item['id_ruangan'], $item['id_user'])) {
                return $this->response(['status' => 'error', 'message' => 'Invalid data structure. Each item must contain jumlah, id_ruangan, and id_user.'], RestController::HTTP_BAD_REQUEST);
            }

            // Tangani barang custom (baru)
            if (strpos($item['id_barang'], 'custom-') === 0) {
                $kd_barang = rand(1000, 9999);

                $barang_baru = [
                    'kd_barang' => $kd_barang,
                    'nama_barang' => $item['nama_barang'],
                    'kategori' => 'ATK',
                    'satuan' => 'PCs',
                    'stok' => 0,
                    'temp' => 1,
                    'kabupaten_id' => $this->decoded_token['id_satker'],
                ];

                $this->Barang_model->insert_barang($barang_baru);

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    return $this->response(['status' => 'Gagal', 'message' => 'Gagal menyimpan barang baru: ' . $item['nama_barang']], RestController::HTTP_INTERNAL_ERROR);
                }

                $new_id = $this->db->insert_id();
                $item['id_barang'] = $new_id;
            }

            $item['tanggal'] = $tanggal;
            $item['status'] = 'Pending';

            $barang = $this->Barang_model->get_id($item['id_barang']);

            if (!$barang) {
                return $this->response(['status' => 'error', 'message' => 'Barang tidak ditemukan dengan ID: ' . $item['id_barang']], RestController::HTTP_NOT_FOUND);
            }

            $item['jumlah_approved'] = ($barang['stok'] < $item['jumlah']) ? $barang['stok'] : $item['jumlah'];

            // Hapus nama_barang sebelum insert
            if (isset($item['nama_barang'])) {
                unset($item['nama_barang']);
            }

            $item['id_transaksi'] = $id_transaksi_permintaan;

            $this->Permintaan_barang_model->insert($item);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return $this->response(['status' => 'Gagal', 'message' => 'Gagal menyimpan permintaan barang.'], RestController::HTTP_INTERNAL_ERROR);
            }

            $this->db->trans_commit();
        }

        return $this->response(['status' => 'success', 'message' => 'Permintaan Berhasil Ditambahkan.'], RestController::HTTP_OK);
    }

    private function cek_butuh_transaksi($data)
    {
        foreach ($data as $item) {
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
            'approved' => '1',
        ]);
    }

    private function response_error($message)
    {
        $this->response(['status' => 'Gagal', 'message' => $message], RestController::HTTP_OK);
    }

    // ------------------ HANDLER STATUS ------------------
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
            return $this->response_error('Jumlah Approved melebihi jumlah permintaan.');
        }

        $update_data = [
            'jumlah_approved' => $jumlah_approved,
            'status' => 'Approved',
            'is_buat_pengajuan' => $item['is_buat_pengajuan'],
        ];

        // Buat pengajuan lanjutan jika diperlukan
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
                'status' => 'Pending',
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
                return $this->response_error('Gagal membuat pengajuan atau permintaan lanjutan.');
            }

            $update_data['id_pengajuan_lanjutan'] = $id_pengajuan;
            $update_data['id_permintaan_lanjutan'] = $id_permintaan_baru;
        }

        // Simpan status
        $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);

        // Kurangi stok FIFO
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
                return $this->response_error('Gagal mengurangi stok barang masuk.');
            }

            if (!$this->Permintaan_barang_model->catat_keluar_fifo([
                'id_permintaan' => $id_permintaan,
                'id_barang_masuk' => $id_masuk,
                'jumlah_keluar' => $ambil,
                'harga_satuan' => $stok['harga'],
            ])) {
                $this->db->trans_rollback();
                return $this->response_error('Gagal mencatat transaksi keluar FIFO.');
            }
        }

        // Kurangi stok akhir dan simpan catatan
        $this->Barang_model->kurangi_stok_barang($id_barang, $jumlah_approved);
        $updated_barang = $this->Barang_model->get_id($id_barang);

        if ($this->Permintaan_barang_model->simpan_current_stok($id_permintaan, $updated_barang['stok'])) {
            $this->db->trans_commit();
        } else {
            $this->db->trans_rollback();
            return $this->response_error('Gagal menyimpan catatan stok akhir.');
        }
    }

    // handle_approved_ketua is similar to handle_approved but keeps status 'Approved' and updates transaksi
    private function handle_approved_ketua($item, $id_transaksi_permintaan)
    {
        $id_user = $this->decoded_token['id_user'];
        $id_permintaan = $item['id_permintaan'];
        $jumlah_approved = $item['jumlah_approved'];

        $this->db->trans_begin();

        $permintaan = $this->Permintaan_barang_model->get_permintaan($id_permintaan); // refresh

        $id_barang = $this->Permintaan_barang_model->get_id_barang($id_permintaan);
        $barang = $this->Barang_model->get_id($id_barang);

        $update_data = ['status' => 'Approved'];

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
                'status' => 'Pending',
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
                return $this->response_error('Gagal membuat pengajuan atau permintaan lanjutan.');
            }

            $update_data['id_pengajuan_lanjutan'] = $id_pengajuan;
            $update_data['id_permintaan_lanjutan'] = $id_permintaan_baru;
        }

        $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);

        // // Kurangi stok FIFO (sama seperti handle_approved)
        // $stok_masuk = $this->Barang_masuk_model->get_stok_masuk_fifo($id_barang);
        // $sisa = $jumlah_approved;

        // foreach ($stok_masuk as $stok) {
        //     if ($sisa <= 0) break;

        //     $id_masuk = $stok['id_barang_masuk'];
        //     $tersedia = $stok['sisa_stok'];

        //     if ($tersedia == 0) continue;

        //     $ambil = min($sisa, $tersedia);
        //     $sisa -= $ambil;

        //     if (!$this->Barang_masuk_model->kurangi_barang_masuk($id_masuk, $ambil)) {
        //         $this->db->trans_rollback();
        //         return $this->response_error('Gagal mengurangi stok barang masuk.');
        //     }

        //     if (!$this->Permintaan_barang_model->catat_keluar_fifo([
        //         'id_permintaan' => $id_permintaan,
        //         'id_barang_masuk' => $id_masuk,
        //         'jumlah_keluar' => $ambil,
        //         'harga_satuan' => $stok['harga'],
        //     ])) {
        //         $this->db->trans_rollback();
        //         return $this->response_error('Gagal mencatat transaksi keluar FIFO.');
        //     }
        // }

        // $this->Barang_model->kurangi_stok_barang($id_barang, $jumlah_approved);
        // $updated_barang = $this->Barang_model->get_id($id_barang);

        // if ($this->Permintaan_barang_model->simpan_current_stok($id_permintaan, $updated_barang['stok'])) {
        $this->db->trans_commit();
        // } else {
        //     $this->db->trans_rollback();
        //     return $this->response_error('Gagal menyimpan catatan stok akhir.');
        // }
    }

    // ------------------ ROLLBACK HANDLERS ------------------
    private function handle_rejected_from_approved($item, $permintaan)
    {
        $this->db->trans_begin();

        $update_data = [
            'jumlah_approved' => 0,
            'status' => 'Rejected',
            'is_buat_pengajuan' => false,
        ];

        if (!empty($permintaan['is_buat_pengajuan'])) {
            $hapus_pengajuan = $this->Pengajuan_barang_model->hapus_pengajuan($permintaan['id_pengajuan_lanjutan']);
            $hapus_permintaan = $this->Permintaan_barang_model->hapus_permintaan($permintaan['id_permintaan_lanjutan']);

            if (!$hapus_pengajuan || !$hapus_permintaan) {
                $this->db->trans_rollback();
                return $this->response_error('Gagal rollback pengajuan dan permintaan lanjutan.');
            }

            $update_data['id_pengajuan_lanjutan'] = null;
            $update_data['id_permintaan_lanjutan'] = null;
        }

        // Tambahkan kembali stok utama
        $id_barang = $permintaan['id_barang'];
        $jumlah_approved = $permintaan['jumlah_approved'];

        $this->Barang_model->tambah_stok_barang($id_barang, $jumlah_approved);

        // Kembalikan stok FIFO
        $log_fifo = $this->Permintaan_barang_model->get_log_keluar_by_permintaan($item['id_permintaan']);

        foreach ($log_fifo as $keluar) {
            $id_masuk = $keluar['id_barang_masuk'];
            $jumlah_dikembalikan = $keluar['jumlah_keluar'];
            $id_bk_detail = $keluar['id_bk_detail'];

            $this->Barang_masuk_model->tambah_barang_masuk($id_masuk, $jumlah_dikembalikan);
            $this->Permintaan_barang_model->soft_delete_keluar($id_bk_detail);
        }

        $this->Permintaan_barang_model->update_status($item['id_permintaan'], $update_data);

        $updated_barang = $this->Barang_model->get_id($id_barang);
        $stok_saved = $this->Permintaan_barang_model->simpan_current_stok($item['id_permintaan'], $updated_barang['stok']);

        if (!$stok_saved) {
            $this->db->trans_rollback();
            return $this->response_error('Gagal update rollback stok keluar.');
        }

        $this->db->trans_commit();
    }

    private function handle_rejected_from_approved_ketua($item, $permintaan)
    {
        $this->db->trans_begin();

        $update_data = [
            'jumlah_approved' => 0,
            'status' => 'Rejected',
            'is_buat_pengajuan' => false,
        ];

        if (!empty($item['is_buat_pengajuan'])) {
            $hapus_pengajuan = $this->Pengajuan_barang_model->hapus_pengajuan($item['id_pengajuan_lanjutan']);
            $hapus_permintaan = $this->Permintaan_barang_model->hapus_permintaan($item['id_permintaan_lanjutan']);

            if (!$hapus_pengajuan || !$hapus_permintaan) {
                $this->db->trans_rollback();
                return $this->response_error('Gagal rollback pengajuan dan permintaan lanjutan.');
            }

            $update_data['id_pengajuan_lanjutan'] = null;
            $update_data['id_permintaan_lanjutan'] = null;
        }

        $id_barang = $item['id_barang'];
        $jumlah_approved = $item['jumlah_approved'];

        $this->Barang_model->tambah_stok_barang($id_barang, $jumlah_approved);

        $log_fifo = $this->Permintaan_barang_model->get_log_keluar_by_permintaan($item['id_permintaan']);

        foreach ($log_fifo as $keluar) {
            $id_masuk = $keluar['id_barang_masuk'];
            $jumlah_dikembalikan = $keluar['jumlah_keluar'];
            $id_bk_detail = $keluar['id_bk_detail'];

            $this->Barang_masuk_model->tambah_barang_masuk($id_masuk, $jumlah_dikembalikan);
            $this->Permintaan_barang_model->soft_delete_keluar($id_bk_detail);
        }

        $this->Permintaan_barang_model->update_status($item['id_permintaan'], $update_data);

        $updated_barang = $this->Barang_model->get_id($id_barang);
        $stok_saved = $this->Permintaan_barang_model->simpan_current_stok($item['id_permintaan'], $updated_barang['stok']);

        if (!$stok_saved) {
            $this->db->trans_rollback();
            return $this->response_error('Gagal update rollback stok keluar.');
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

        if (!empty($permintaan['is_buat_pengajuan'])) {
            $hapus_pengajuan = $this->Pengajuan_barang_model->hapus_pengajuan($permintaan['id_pengajuan_lanjutan']);
            $hapus_permintaan = $this->Permintaan_barang_model->hapus_permintaan($permintaan['id_permintaan_lanjutan']);

            if (!$hapus_pengajuan || !$hapus_permintaan) {
                $this->db->trans_rollback();
                return $this->response_error('Gagal rollback pengajuan dan permintaan.');
            }

            $update_data['id_pengajuan_lanjutan'] = null;
            $update_data['id_permintaan_lanjutan'] = null;
        }

        $id_barang = $permintaan['id_barang'];
        $jumlah_approved = $permintaan['jumlah_approved'];

        $this->Barang_model->tambah_stok_barang($id_barang, $jumlah_approved);

        $log_keluar = $this->Permintaan_barang_model->get_log_keluar_by_permintaan($item['id_permintaan']);

        foreach ($log_keluar as $keluar) {
            $id_masuk = $keluar['id_barang_masuk'];
            $jumlah_dikembalikan = $keluar['jumlah_keluar'];
            $id_bk_detail = $keluar['id_bk_detail'];

            $this->Barang_masuk_model->tambah_barang_masuk($id_masuk, $jumlah_dikembalikan);
            $this->Permintaan_barang_model->soft_delete_keluar($id_bk_detail);
        }

        $this->Permintaan_barang_model->update_status($item['id_permintaan'], $update_data);

        $updated_barang = $this->Barang_model->get_id($id_barang);
        $stok_saved = $this->Permintaan_barang_model->simpan_current_stok($item['id_permintaan'], $updated_barang['stok']);

        if (!$stok_saved) {
            $this->db->trans_rollback();
            return $this->response_error('Gagal update rollback stok keluar.');
        }

        $this->db->trans_commit();
    }

    private function handle_rejected_from_pending($item, $permintaan)
    {
        $update_data = ['jumlah_approved' => 0, 'status' => 'Rejected'];
        $this->Permintaan_barang_model->update_status($item['id_permintaan'], $update_data);
    }

    // ------------------ END HANDLERS ------------------

    public function update_status_put()
    {
        $data = json_decode($this->input->raw_input_stream, true);

        if (!is_array($data) || empty($data)) {
            return $this->response_error('Invalid input. Please provide valid JSON data.');
        }

        $butuh_transaksi = $this->cek_butuh_transaksi($data);
        $id_transaksi = $butuh_transaksi ? $this->buat_transaksi_permintaan($data[0]) : null;

        foreach ($data as $item) {
            if (!isset($item['id_permintaan'], $item['status'], $item['jumlah_approved'])) {
                return $this->response_error('Invalid data structure. Each item must contain id_permintaan, status, and jumlah.');
            }

            $permintaan = $this->Permintaan_barang_model->get_permintaan($item['id_permintaan']);

            if ($permintaan['status'] === 'Pengadaan' && in_array($item['status'], ['Rejected', 'Pending'], true)) {
                return $this->response(['status' => 'Gagal', 'message' => 'Permintaan yang dalam proses pengadaan tidak dapat dibatalkan'], RestController::HTTP_OK);
            }

            if ($item['status'] === 'Approved') {
                $this->handle_approved($item, $permintaan, $id_transaksi);
            } elseif ($item['status'] === 'Rejected' && $permintaan['status'] === 'Approved') {
                $this->handle_rejected_from_approved($item, $permintaan);
            } elseif ($item['status'] === 'Pending' && $permintaan['status'] === 'Approved') {
                $this->handle_pending_from_approved($item, $permintaan);
            } elseif ($item['status'] === 'Rejected' && $permintaan['status'] === 'Pending') {
                $this->handle_rejected_from_pending($item, $permintaan);
            }
        }

        return $this->response(['status' => 'success', 'message' => 'Status updated and stock adjusted successfully.'], RestController::HTTP_OK);
    }

    public function approve_transaksi_get($id_transaksi, $status)
    {
        $update_trans = $this->Permintaan_barang_model->approve_transaksi($id_transaksi, $status);

        if ($update_trans) {
            return $this->response(['status' => 'success', 'message' => 'Status Permintaan Berhasil Diubah.'], RestController::HTTP_OK);
        }

        return $this->response(['status' => 'Gagal', 'message' => 'Status Permintaan Gagal Diubah.'], RestController::HTTP_OK);
    }

    public function approve_gudang_get($id_transaksi, $status)
    {
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

            $this->Permintaan_barang_model->update_transaksi_permintaan($transaksi_permintaan['id'], ['approve_gudang' => '1']);
        } elseif ($status === '0' && $transaksi_permintaan['approve_gudang'] === '1') {
            foreach ($permintaan as $item) {
                if (!isset($item['id_permintaan'], $item['status'], $item['jumlah_approved'])) {
                    return $this->response_error('Invalid data structure. Each item must contain id_permintaan, status, and jumlah approved.');
                }
                $this->handle_rejected_from_approved_ketua($item, $permintaan);
            }

            $this->Permintaan_barang_model->update_transaksi_permintaan($transaksi_permintaan['id'], ['approve_gudang' => '0']);
        }

        return $this->response(['status' => 'success', 'message' => 'Status updated and stock adjusted successfully.'], RestController::HTTP_OK);
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

            $id_permintaan = $item['id_permintaan'];
            $jumlah_approved = $item['jumlah_approved'];

            $permintaan = $this->Permintaan_barang_model->get_permintaan($id_permintaan);

            if ($permintaan['status'] === 'Pengadaan' && in_array($item['status'], ['Rejected', 'Pending'], true)) {
                return $this->response(['status' => 'Gagal', 'message' => 'Permintaan yang dalam proses pengadaan tidak dapat dibatalkan'], RestController::HTTP_OK);
            }

            if ($item['status'] === 'Approved Gudang') {

                $this->db->trans_begin();

                $jumlah_permintaan = $permintaan['jumlah'];
                $id_barang = $this->Permintaan_barang_model->get_id_barang($id_permintaan);
                $barang = $this->Barang_model->get_id($id_barang);

                if ($barang['stok'] < $jumlah_approved) {
                    $this->db->trans_rollback();
                    return $this->response_error("Jumlah Approved {$barang['nama_barang']} melebihi stok yang tersedia.");
                } elseif ($jumlah_approved > $jumlah_permintaan) {
                    $this->db->trans_rollback();
                    return $this->response_error("Jumlah Approved {$barang['nama_barang']} melebihi jumlah permintaan.");
                }

                if ($barang['temp'] == 1) {
                    $barang['kd_barang'] = $this->Barang_model->get_next_kode_barang($item['kd_subkategori']);
                    $barang['temp'] = 0;
                    $barang['kabupaten_id'] = $this->decoded_token['id_satker'];
                    $barang['kategori'] = 'ATK';
                    $barang['kd_subkategori'] = $item['kd_subkategori'];

                    $this->Barang_model->ubah_api($barang['id_barang'], $barang);
                }

                // Kurangi stok FIFO (sama seperti handle_approved)
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
                        return $this->response_error('Gagal mengurangi stok barang masuk.');
                    }

                    if (!$this->Permintaan_barang_model->catat_keluar_fifo([
                        'id_permintaan' => $id_permintaan,
                        'id_barang_masuk' => $id_masuk,
                        'jumlah_keluar' => $ambil,
                        'harga_satuan' => $stok['harga'],
                    ])) {
                        $this->db->trans_rollback();
                        return $this->response_error('Gagal mencatat transaksi keluar FIFO.');
                    }
                }

                $this->Barang_model->kurangi_stok_barang($id_barang, $jumlah_approved);


                $update_data = [
                    'jumlah_approved' => $jumlah_approved,
                    'status' => 'Approved Gudang',
                    'is_buat_pengajuan' => $item['is_buat_pengajuan'],
                ];

                $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);

                $this->db->trans_commit();
            } elseif ($item['status'] === 'Rejected' && $permintaan['status'] === 'Approved Gudang') {
                // $update_data = ['jumlah_approved' => 0, 'status' => 'Rejected', 'is_buat_pengajuan' => false];
                // $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);
                $this->handle_rejected_from_approved($item, $permintaan);
            } elseif ($item['status'] === 'Pending' && $permintaan['status'] === 'Approved Gudang') {
                $update_data = ['jumlah_approved' => $permintaan['jumlah_approved'], 'status' => 'Pending', 'is_buat_pengajuan' => false];
                $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);
            } elseif ($item['status'] === 'Rejected' && $permintaan['status'] === 'Pending') {
                $this->handle_rejected_from_pending($item, $permintaan);
            } elseif ($item['status'] === 'Pending' && $permintaan['status'] === 'Rejected') {
                $update_data = ['jumlah_approved' => $item['jumlah_permintaan'], 'status' => 'Pending'];
                $this->Permintaan_barang_model->update_status($id_permintaan, $update_data);

            }
        }

        return $this->response(['status' => 'success', 'message' => 'Status updated and stock adjusted successfully.'], RestController::HTTP_OK);
    }
}
