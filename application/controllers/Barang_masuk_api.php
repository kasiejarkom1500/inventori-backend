<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/ApiController.php';

use chriskacerguis\RestServer\RestController;

class Barang_masuk_api extends ApiController

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

        
        
        $this->load->model('Barang_masuk_model');
		$this->load->model('Barang_model');
		
		$this->load->helper('form');
		$this->load->helper('url');
        $this->load->config('rest');
        
    }
    
    private function response_error($message)
    {
        return $this->response(['status' => false, 'message' => $message], RestController::HTTP_OK);
    }
    
    public function all_barang_masuk_get() {
        try {
            // Ambil data barang masuk dari model
            $data_barang_masuk = $this->Barang_masuk_model->tampil_data();
            var_dump($data_barang_masuk);

            // Periksa apakah data ditemukan
            if ($data_barang_masuk) {
                $this->response([
                    'status' => true,
                    'data' => $data_barang_masuk
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data barang masuk tidak ditemukan'
                ], RESTController::HTTP_OK);
            }
        } catch (Exception $e) {
            // Tangani kesalahan yang tidak terduga
            $this->response([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], RESTController::HTTP_INTERNAL_ERROR);
        }
    }
    
    
    public function all_pembelian_get() {
        try {
            // Ambil data barang masuk dari model
            $data_barang_masuk = $this->Barang_masuk_model->data_pembelian($this->decoded_token['id_satker']);

            // Periksa apakah data ditemukan
            if ($data_barang_masuk) {
                $this->response([
                    'status' => true,
                    'data' => $data_barang_masuk
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data barang masuk tidak ditemukan'
                ], RESTController::HTTP_OK);
            }
        } catch (Exception $e) {
            // Tangani kesalahan yang tidak terduga
            $this->response([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], RESTController::HTTP_INTERNAL_ERROR);
        }
    }
    
    public function detail_pembelian_get($id_pembelian) {
        try {
            // Ambil data barang masuk dari model
            $data_barang_masuk = $this->Barang_masuk_model->detail_pembelian($id_pembelian);

            // Periksa apakah data ditemukan
            if ($data_barang_masuk) {
                $this->response([
                    'status' => true,
                    'data' => $data_barang_masuk
                ], RESTController::HTTP_OK);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Data barang masuk tidak ditemukan'
                ], RESTController::HTTP_OK);
            }
        } catch (Exception $e) {
            // Tangani kesalahan yang tidak terduga
            $this->response([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], RESTController::HTTP_INTERNAL_ERROR);
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
            ], 200);
            return;
        }
        
        $tanggal = date('Y-m-d');
        
    
        // Loop melalui setiap item dalam data
        foreach ($data as $item) {
            // Validasi setiap item (id_permintaan, status, jumlah harus ada)
            if (!isset($item['no_kuitansi'], $item['id_sumber'], $item['barang'])) {
                $this->response([
                    'status' => 'Error',
                    'message' => 'Invalid data structure. Each item must contain no_kuitansi, id_sumber, and barang.'
                ], 200);
                return;
            }
            
            $data_pembelian = [
                'id_satker' => $this->decoded_token['id_satker'],
                'no_kuitansi' => $item['no_kuitansi'],
                'id_sumber' => $item['id_sumber'],
                'tgl_masuk' => $item['tgl_masuk']
                ];
                
            // Memulai transaksi
            $this->db->trans_begin();

            
            $id_pembelian = $this->Barang_masuk_model->simpan_pembelian($data_pembelian);
            
             if ($id_pembelian < 0) {
                 // Jika penyimpanan pembelian gagal, rollback dan hentikan proses
                 $this->db->trans_rollback();
                 
                 $this->response(
                     [
                         'status' => 'Gagal', 
                         'message' => 'Gagal menyimpan data pembelian'
                     ], 200);
                     
                    return;
                 
             }

            foreach ($item['barang'] as $barang) {
                $data = [
                    'tgl_masuk' => $item['tgl_masuk'],
                    'id_pembelian' => $id_pembelian,
                    'id_barang' => $barang['id_barang'],
                    'jumlah' => $barang['jumlah'],
                    'harga' => $barang['harga'],
                    'sisa_stok' => $barang['jumlah'],
                    'catatan' => $barang['catatan'] ?? null,
                    ];
                    
                $result = $this->Barang_masuk_model->simpan($data);
                
                // var_dump($result);
                
                if (!$result > 0) {
    
                    $this->db->trans_rollback();
                    
                    $this->response(
                         [
                             'status' => false, 
                             'message' => 'Gagal menyimpan data barang'
                         ], 200);
                         
                        return;
                 }
                 
                $barang_db = $this->Barang_model->get_id($barang['id_barang']);
                
                // var_dump($barang_db);
                // die;
                
                $data_barang = [
                    'kd_barang' => $barang_db['kd_barang'],
                    'stok' => $barang_db['stok'] + $barang['jumlah'],
                    'harga_satuan' => $barang['harga']
                    ];
                
                $result_barang = $this->Barang_model->ubah_api($barang['id_barang'],$data_barang);
                
                 if (!$result_barang) {
    
                    $this->db->trans_rollback();
                    
                    $this->response(
                         [
                             'status' => false, 
                             'message' => 'Gagal update stok barang'
                         ], 200);
                         
                        return;
                 }
                 
                //  $bm_db = $this->Barang_masuk_model->get_id($result);
                
                $updated_barang = $this->Barang_model->get_id($barang['id_barang']);
                
                $data_bm =[
                        'current_stok' => $updated_barang['stok']
                    ];
                    
                // var_dump($data_bm);
                 
                 $barang_masuk = $this->Barang_masuk_model->ubah_api($result,$data_bm);
                 
                //  var_dump($barang_masuk);
                
                 if (!$barang_masuk) {
    
                    $this->db->trans_rollback();
                    
                    $this->response(
                         [
                             'status' => false, 
                             'message' => 'Gagal update transaksi stok di barang masuk'
                         ], 200);
                         
                        return;
                 }
            }
    
            $this->db->trans_commit();
        
        }
        
        
    
        // Response success
        $this->response([
            'status' => 'success',
            'message' => 'Pembelian Berhasil Ditambahkan.'
        ], 200);
	    
	}



	public function get_kode_barang() {
        $term = $this->input->get('q');
        $results = $this->Barang_masuk_model->search_barang($term);
        
        echo json_encode($results);
    }

    /**
     * Update barang_masuk jika stok belum terpakai (sisa_stok == jumlah dan belum ada log keluar).
     * Body JSON: { jumlah, harga, catatan, tgl_masuk }
     */
    public function update_barang_masuk_put($id_barang_masuk)
    {
        $payload = json_decode($this->input->raw_input_stream, true);
        if (!is_array($payload) || empty($payload)) {
            // fallback jika client mengirim form-encoded/put data
            $payload = $this->put();
        }
        if (!is_array($payload)) {
            return $this->response_error('Invalid payload');
        }

        $bm = $this->Barang_masuk_model->get_id($id_barang_masuk);
        if (!$bm) return $this->response_error('Data barang masuk tidak ditemukan');

        // Cek apakah stok sudah terpakai
        $sudah_terpakai = ($bm['sisa_stok'] < $bm['jumlah']) || $this->Barang_masuk_model->has_keluar_log($id_barang_masuk);
        // Jika sudah terpakai, hanya boleh update catatan
        $hanya_catatan =
            isset($payload['catatan']) &&
            !isset($payload['jumlah']) &&
            !isset($payload['harga']) &&
            !isset($payload['tgl_masuk']);
        if ($sudah_terpakai && !$hanya_catatan) {
            return $this->response_error('Tidak dapat diubah karena stok sudah terpakai. Gunakan penyesuaian.');
        }

        $jumlah_baru = isset($payload['jumlah']) ? (int)$payload['jumlah'] : (int)$bm['jumlah'];
        $harga_baru = isset($payload['harga']) ? (int)$payload['harga'] : (int)$bm['harga'];
        $catatan_baru = array_key_exists('catatan', $payload) ? $payload['catatan'] : $bm['catatan'];
        $tgl_baru = isset($payload['tgl_masuk']) ? $payload['tgl_masuk'] : $bm['tgl_masuk'];

        // Jika hanya catatan dan stok terpakai, cukup update catatan
        if ($sudah_terpakai && $hanya_catatan) {
            $this->Barang_masuk_model->ubah_api($id_barang_masuk, ['catatan' => $catatan_baru]);
            return $this->response(['status' => true, 'message' => 'Catatan diperbarui'], RestController::HTTP_OK);
        }

        $delta = $jumlah_baru - (int)$bm['jumlah'];

        $this->db->trans_begin();

        // Update stok barang (sesuaikan dengan delta)
        $barang = $this->Barang_model->get_id($bm['id_barang']);
        if (!$barang) {
            $this->db->trans_rollback();
            return $this->response_error('Barang tidak ditemukan.');
        }

        $stok_baru = $barang['stok'] + $delta;
        if ($stok_baru < 0) {
            $this->db->trans_rollback();
            return $this->response_error('Stok tidak mencukupi untuk perubahan ini.');
        }

        // Update stok & harga_satuan tanpa ubah kd_barang/kabupaten_id
        $this->db->where('id_barang', $bm['id_barang']);
        if (!$this->db->update('barang', ['stok' => $stok_baru, 'harga_satuan' => $harga_baru])) {
            $this->db->trans_rollback();
            return $this->response_error('Gagal mengupdate stok barang.');
        }

        // Update barang_masuk
        $update_bm = [
            'jumlah' => $jumlah_baru,
            'sisa_stok' => $jumlah_baru, // karena belum terpakai
            'harga' => $harga_baru,
            'catatan' => $catatan_baru,
            'tgl_masuk' => $tgl_baru,
            'current_stok' => $stok_baru,
        ];

        if (!$this->Barang_masuk_model->ubah_api($id_barang_masuk, $update_bm)) {
            $this->db->trans_rollback();
            return $this->response_error('Gagal mengupdate data barang masuk.');
        }

        $this->db->trans_commit();
        return $this->response(['status' => true, 'message' => 'Data barang masuk berhasil diupdate'], RestController::HTTP_OK);
    }

    /**
     * Hapus barang_masuk jika stok belum terpakai.
     */
    public function delete_barang_masuk_delete($id_barang_masuk)
    {
        $bm = $this->Barang_masuk_model->get_id($id_barang_masuk);
        if (!$bm) return $this->response_error('Data barang masuk tidak ditemukan');

        $sudah_terpakai = ($bm['sisa_stok'] < $bm['jumlah']) || $this->Barang_masuk_model->has_keluar_log($id_barang_masuk);
        if ($sudah_terpakai) {
            return $this->response_error('Tidak dapat dihapus karena stok sudah terpakai. Gunakan penyesuaian.');
        }

        $this->db->trans_begin();

        // Kurangi stok barang sesuai jumlah masuk
        $barang = $this->Barang_model->get_id($bm['id_barang']);
        if (!$barang) {
            $this->db->trans_rollback();
            return $this->response_error('Barang tidak ditemukan.');
        }

        $stok_baru = $barang['stok'] - (int)$bm['jumlah'];
        if ($stok_baru < 0) {
            $this->db->trans_rollback();
            return $this->response_error('Stok tidak mencukupi untuk penghapusan ini.');
        }

        if (!$this->Barang_model->ubah_api($bm['id_barang'], ['stok' => $stok_baru])) {
            $this->db->trans_rollback();
            return $this->response_error('Gagal mengupdate stok barang.');
        }

        // Hapus record barang_masuk
        $this->db->where('id_barang_masuk', $id_barang_masuk);
        $this->db->delete('barang_masuk');

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return $this->response_error('Gagal menghapus data barang masuk.');
        }

        $this->db->trans_commit();
        return $this->response(['status' => true, 'message' => 'Data barang masuk berhasil dihapus'], RestController::HTTP_OK);
    }

    /**
     * Penyesuaian stok (tambah/kurangi) untuk barang_masuk.
     * Body JSON: { id_barang_masuk, jumlah_delta, catatan }
     * - jumlah_delta > 0 : tambah stok (jumlah & sisa_stok bertambah, stok barang naik)
     * - jumlah_delta < 0 : kurangi stok jika sisa_stok mencukupi
     */
    public function adjust_barang_masuk_post()
    {
        $payload = json_decode($this->input->raw_input_stream, true);
        if (!is_array($payload) || empty($payload)) {
            $payload = $this->post();
        }
        if (!is_array($payload) || !isset($payload['id_barang_masuk'], $payload['jumlah_delta'])) {
            return $this->response_error('Invalid payload. Butuh id_barang_masuk dan jumlah_delta.');
        }

        $id_barang_masuk = (int)$payload['id_barang_masuk'];
        $delta = (int)$payload['jumlah_delta'];
        // Catatan penyesuaian disimpan terpisah agar tidak menimpa catatan utama
        $catatan_penyesuaian = null;
        if (array_key_exists('catatan_penyesuaian', $payload)) {
            $catatan_penyesuaian = $payload['catatan_penyesuaian'];
        } elseif (array_key_exists('catatan', $payload)) {
            // fallback dari payload lama
            $catatan_penyesuaian = $payload['catatan'];
        }

        if ($delta === 0) {
            return $this->response_error('jumlah_delta tidak boleh 0');
        }

        $bm = $this->Barang_masuk_model->get_id($id_barang_masuk);
        if (!$bm) return $this->response_error('Data barang masuk tidak ditemukan');

        $barang = $this->Barang_model->get_id($bm['id_barang']);
        if (!$barang) return $this->response_error('Barang tidak ditemukan.');

        $this->db->trans_begin();

        if ($delta > 0) {
            // Tambah stok
            $jumlah_baru = $bm['jumlah'] + $delta;
            $sisa_baru = $bm['sisa_stok'] + $delta;
            $stok_barang_baru = $barang['stok'] + $delta;

            // Update stok barang secara langsung
            $this->db->where('id_barang', $bm['id_barang']);
            if (!$this->db->update('barang', ['stok' => $stok_barang_baru])) {
                $this->db->trans_rollback();
                return $this->response_error('Gagal update stok barang.');
            }

            $update_bm = [
                'jumlah' => $jumlah_baru,
                'sisa_stok' => $sisa_baru,
                'current_stok' => $stok_barang_baru,
            ];
            if ($catatan_penyesuaian !== null) {
                $update_bm['catatan_penyesuaian'] = $catatan_penyesuaian;
            }

        if (!$this->Barang_masuk_model->ubah_api($id_barang_masuk, $update_bm)) {
            // debug: tampilkan error DB

            $this->db->trans_rollback();
            return $this->response_error('Gagal update penyesuaian barang masuk.');
        }
        } else {
            // Kurangi stok
            $kurang = abs($delta);
            if ($bm['sisa_stok'] < $kurang) {
                $this->db->trans_rollback();
                return $this->response_error('Sisa stok tidak mencukupi untuk pengurangan ini.');
            }

            $jumlah_baru = $bm['jumlah'] - $kurang;
            $sisa_baru = $bm['sisa_stok'] - $kurang;
            $stok_barang_baru = $barang['stok'] - $kurang;
            if ($stok_barang_baru < 0) {
                $this->db->trans_rollback();
                return $this->response_error('Stok barang menjadi negatif.');
            }

            $this->db->where('id_barang', $bm['id_barang']);
            if (!$this->db->update('barang', ['stok' => $stok_barang_baru])) {
                $this->db->trans_rollback();
                return $this->response_error('Gagal update stok barang.');
            }

            $update_bm = [
                'jumlah' => $jumlah_baru,
                'sisa_stok' => $sisa_baru,
                'current_stok' => $stok_barang_baru,
            ];
            if ($catatan_penyesuaian !== null) {
                $update_bm['catatan_penyesuaian'] = $catatan_penyesuaian;
            }

            if (!$this->Barang_masuk_model->ubah_api($id_barang_masuk, $update_bm)) {
                $this->db->trans_rollback();
                return $this->response_error('Gagal update penyesuaian barang masuk.');
            }
        }

        $this->db->trans_commit();
        return $this->response(['status' => true, 'message' => 'Penyesuaian stok berhasil'], RestController::HTTP_OK);
    }

	public function barang_details_get()
    {
		$id_barang = $this->input->post('id_barang');
        $data = $this->Barang_masuk_model->get_barang_details($id_barang);
        if ($data) {
			echo json_encode($data);
		} else {
			echo json_encode(['error' => 'Data not found']); // Menampilkan pesan error jika data tidak ditemukan
		}
    }

	public function hapus($id)
    {
		is_logged_in();
        $this->Barang_masuk_model->hapus($id);
        redirect('barang_masuk');
    }

}
