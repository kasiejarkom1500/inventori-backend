<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/ApiController.php';

use chriskacerguis\RestServer\RestController;

class Pengajuan_barang_api extends ApiController
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


		$this->load->model('Pengajuan_barang_model');
		$this->load->model('Barang_model');
		$this->load->model('User_model');
		$this->load->model('ruangan_model');
		$this->load->helper('form');
		$this->load->helper('url');
        $this->load->config('rest');
        
	}

	
	public function transaksi_pengajuan_get()
	{

		$permintaan_barang = $this->Pengajuan_barang_model->get_grouped_by_date($this->decoded_token['id_satker']);
		
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
	
	public function pengajuan_detail_get($id_ruangan,$tanggal)
	{
	    
	    // Validasi apakah tanggal sesuai format (opsional)
        if (!DateTime::createFromFormat('Y-m-d', $tanggal)) {
            // Jika tidak valid, kembalikan error
            $this->response([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], RESTController::HTTP_NOT_FOUND);
        }

		$permintaan_barang = $this->Pengajuan_barang_model->get_by_date($id_ruangan,$tanggal);
		
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
	
	public function update_status_put()
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
    
        // Loop melalui setiap item dalam data
        foreach ($data as $item) {
            // Validasi setiap item (id_pengajuan, status, jumlah harus ada)
            if (!isset($item['id_pengajuan'], $item['status'], $item['jumlah'], $item['kategori'])) {
                $this->response([
                    'status' => 'error',
                    'message' => 'Invalid data structure. Each item must contain id_permintaan, status, and jumlah.'
                ], 400);
                return;
            }
    
            $id_pengajuan = $item['id_pengajuan'];
            $status = $item['status'];
            $jumlah = $item['jumlah'];
    

            
            $id_barang = $this->Pengajuan_barang_model->get_id_barang($id_pengajuan);
            $barang = $this->Barang_model->get_id($id_barang);
            
            
    
            if ($status == 'Approved' && $barang['temp'] == 1) {
                //Ambil jumlah permintaan dan id_barang
                
                $last_kd_barang =  $this->Barang_model->get_next_kode_barang($item['kd_subkategori']);

                // var_dump($last_kd_barang);
                // die;
                
                $kd_barang = $last_kd_barang;
                $barang['kd_barang'] = $kd_barang;
                $barang['temp'] = 0;
                $barang['harga_satuan'] = $item['harga_satuan'];
                $barang['kategori'] = $item['kategori'];
                $barang['kd_subkategori'] = $item['kd_subkategori'];
                
                $this->Barang_model->ubah_api($barang['id_barang'],$barang);
                
                
    
                //Kurangi stok barang
            }
            
            $this->Pengajuan_barang_model->update_pengajuan($id_pengajuan, ['total_harga' => $item['harga_satuan']*$jumlah, 'harga_pengajuan' => $item['harga_satuan']]);
            
            // Update status permintaan
            $this->Pengajuan_barang_model->update_status($id_pengajuan, $status, $jumlah);
        }
    
        // Response success
        $this->response([
            'status' => 'success',
            'message' => 'Status updated'
        ], 200);
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
            // var_dump($data);
            // die;
            if (!isset($item['jumlah'], $item['id_ruangan'], $item['id_user'])) {
                $this->response([
                    'status' => 'error',
                    'message' => 'Invalid data structure. Each item must contain jumlah, id_ruangan, and id_user.'
                ], 400);
                return;
            }
            
            if ($item['id_barang'] == '0' ||  $item['id_barang'] == '') {
                
                $random_number = rand(1000, 9999);
                $kd_barang = $random_number;
                
                $data = [
                        'kd_barang' => $kd_barang,
                        'nama_barang' => $item['nama_barang'],
                        'kategori' => $item['kategori'],
                        'satuan' => $item['satuan'],
                        'stok' => 0,
                        'temp' => 1,
                        'id_satker' => $this->decoded_token['id_satker'],
                    ];

                $this->db->trans_begin();
                
                $this->Barang_model->insert_barang($data);

                if ($this->db->trans_status() === FALSE) {
                    $db_error = $this->db->error();
                    $this->db->trans_rollback();
                    $this->response([
                        'status' => 'Gagal',
                        'message' => 'Gagal simpan barang '.$item['nama_barang'],
                        // 'db_error' => $db_error // Tambahkan ini untuk debug
                    ], 200);
                    return;
                }


                
                $last_id = $this->db->insert_id();

                $item['id_barang'] = $last_id;
                
                // var_dump($last_id);
                // die;
                $barang =  $this->Barang_model->get_id($item['id_barang']);

			    $item['harga_pengajuan'] = $barang['harga_satuan'];
                
                
                $item['tanggal'] = $tanggal;
    			$item['status'] = 'Pending';
    			$data_pengajuan = [
    			        'id_barang' => $item['id_barang'],
    			        'id_user' => $item['id_user'],
                        'id_satker' => $this->decoded_token['id_satker'],
    			        'id_ruangan' => $item['id_ruangan'],
    			        'tanggal' => $tanggal,
    			        'jumlah' => $item['jumlah'],
    			        'harga_pengajuan' => $item['harga_pengajuan'],
    			        'status' => $item['status']
    			    
    			    ];
    			$this->Pengajuan_barang_model->insert($data_pengajuan);

                if ($this->db->trans_status() === FALSE) {
                    $db_error = $this->db->error();
                    $this->db->trans_rollback();
                    $this->response([
                        'status' => 'Gagal',
                        'message' => 'Gagal menambahkan pengajuan barang '.$item['nama_barang'],
                        // 'db_error' => $db_error // Tambahkan ini untuk debug
                    ], 200);
                    return;
                }

                $this->db->trans_commit();
			    
			} else {
			    
			$item['tanggal'] = $tanggal;
			$item['status'] = 'Pending';
			$barang =  $this->Barang_model->get_id($item['id_barang']);

			$item['harga_pengajuan'] = $barang['harga_satuan'];
			
			
			
			$data_pengajuan = [
			        'id_barang' => $item['id_barang'],
			        'id_user' => $item['id_user'],
                    'id_satker' => $this->decoded_token['id_satker'],
			        'id_ruangan' => $item['id_ruangan'],
			        'tanggal' => $tanggal,
			        'jumlah' => $item['jumlah'],
			        'harga_pengajuan' => $item['harga_pengajuan'],
			        'status' => $item['status']
			    
			    ];
			$this->Pengajuan_barang_model->insert($data_pengajuan);
			
			    
			}
            
        }
    
        // Response success
        $this->response([
            'status' => 'success',
            'message' => 'Pengajuan Berhasil Ditambahkan.'
        ], 200);
	    
	}

	public function data_pengajuan_update()
	{
		$id_pengajuan = $this->input->post('id_pengajuan');
		$id_barang = $this->input->post('id_barang');
		$jumlah = $this->input->post('jumlah');
		$status = $this->input->post('status');

		if (empty($id_pengajuan) || empty($status) || empty($jumlah) || empty($id_barang)) {
			$response = array('status' => 'error', 'message' => 'Missing data.');
			echo json_encode($response);
			return;
		}

		$result = $this->db->where('id_barang', $id_barang)
		->get('harga_pengajuan')
		->result();


		$hargaPengajuan = $result[0]->harga_pengajuan;

		$total = $hargaPengajuan * $jumlah;

		$update_data = array(
			'jumlah' => $jumlah,
			'status' => $status,
			'total_harga' => $total
		);

		// Update pengajuan
		$result_update = $this->Pengajuan_barang_model->update_pengajuan($id_pengajuan, $update_data);

		if ($result_update) {
			// Prepare data for insert into barang_masuk
			if ($status == 'Approved') {
				$data_barang_masuk = array(
					'tgl_masuk' => date('Y-m-d'),
					'id_barang' => $id_barang,
					'jumlah' => $jumlah
				);

				// Insert into barang_masuk and update stock
				$result_insert = $this->Pengajuan_barang_model->insert_barang_masuk($data_barang_masuk);

				if ($result_insert) {
					$response = array('status' => 'success', 'message' => 'Data updated and stock adjusted successfully.');
				} else {
					$response = array('status' => 'error', 'message' => 'Failed to insert into barang_masuk or update stock.');
				}
			}else{
				$response = array('status' => 'success', 'message' => 'Data updated.');
			}
		} else {
			$response = array('status' => 'error', 'message' => 'Failed to update pengajuan.');
		}

		echo json_encode($response);
	}
	
	
}
