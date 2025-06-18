<?php
class Barang_model extends CI_Model
{
	// public function tampil_data()
	// {
	// 	return $this->db->get('barang')->result_array();

	// }

	
	
	public function tampil_data_all($kabupaten_id = null)
	{
		$this->db->select('barang.*, harga_pengajuan.harga_pengajuan');
		$this->db->from('barang');
		$this->db->join('harga_pengajuan', 'barang.id_barang = harga_pengajuan.id_barang', 'left');
		$this->db->where('temp',0);
		if ($kabupaten_id !== null) {
			$this->db->where('barang.kabupaten_id', $kabupaten_id);
		}
		$query = $this->db->get();
		return $query->result_array();
	}
	
    public function get_sum($kategori, $kabupaten_id = null)
    {
        $this->db->select('COUNT(*) as total');
        $this->db->from('barang');
        $this->db->where('kategori', $kategori);
        $this->db->where('temp', 0);
        if ($kabupaten_id !== null) {
			$this->db->where('kabupaten_id', $kabupaten_id);
		}
        $query = $this->db->get();
        return $query->row()->total; // Mengembalikan angka, bukan objek
    }
    
    public function get_sum_stok($kategori, $kabupaten_id = null)
    {
        $this->db->select('SUM(stok) as total_stok');
        $this->db->from('barang');
        $this->db->where('kategori', $kategori);
        $this->db->where('temp', 0);
        if ($kabupaten_id !== null) {
			$this->db->where('kabupaten_id', $kabupaten_id);
		}
        $query = $this->db->get();
        return $query->row()->total_stok; // Mengembalikan angka, bukan objek
    }

	// mendapatkan data barang yang stoknya 5% dibandingkan dengan stok_dasar
	public function get_barang_stok_minimal($kabupaten_id = null)
	{
		$this->db->select('barang.id_barang, barang.nama_barang, stok, stok_dasar as "stok minimum"');
		$this->db->from('barang');
		$this->db->where('barang.stok < (barang.stok_dasar * 0.05)');
		$this->db->where('temp',0);
		if ($kabupaten_id !== null) {
			$this->db->where('barang.kabupaten_id', $kabupaten_id);
		}
		$query = $this->db->get();
		return $query->result_array();
	}


	public function tampil_data($kabupaten_id = null)
	{
		$this->db->select('barang.*, harga_pengajuan.harga_pengajuan');
		$this->db->from('barang');
		$this->db->join('harga_pengajuan', 'barang.id_barang = harga_pengajuan.id_barang', 'left');
		$this->db->where('barang.kategori', "ATK");
		$this->db->where('temp',0);
		if ($kabupaten_id !== null) {
			$this->db->where('barang.kabupaten_id', $kabupaten_id);
		}
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function tampil_data_ark($kabupaten_id = null)
	{
		$this->db->select('barang.*, harga_pengajuan.harga_pengajuan');
		$this->db->from('barang');
		$this->db->join('harga_pengajuan', 'barang.id_barang = harga_pengajuan.id_barang', 'left');
		$this->db->where('barang.kategori', "ARK");
		$this->db->where('temp',0);
		if ($kabupaten_id !== null) {
			$this->db->where('barang.kabupaten_id', $kabupaten_id);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function exists_kd_barang($kd_barang, $kabupaten_id = null)
	{
		$this->db->where('kd_barang', $kd_barang);
		if ($kabupaten_id !== null) {
			$this->db->where('kabupaten_id', $kabupaten_id);
		}
		$query = $this->db->get('barang');

		return $query->num_rows() > 0;

	}

	public function insert_harga_pengajuan($data)
	{
		$this->db->insert('harga_pengajuan', $data);
	}

	public function get_all_barang()
	{
		$this->db->select('barang.*, harga_pengajuan.harga_pengajuan AS harga_pengajuan_harga');
		$this->db->from('barang');
		$this->db->join('harga_pengajuan', 'barang.id_barang = harga_pengajuan.id_barang'); // Assuming the join key is 'id_harga_pengajuan'
		$query = $this->db->get();

		if ($query === false) {
			log_message('error', 'SQL Query: ' . $this->db->last_query());
			log_message('error', 'Error Message: ' . print_r($this->db->error(), true));
			return false;
		}

		return $query->result();
	}


	public function get_all_permintaan()
	{
		$this->db->select('tanggal, kd_barang, nama_barang, kategori, satuan, jumlah, ruangan');
		return $this->db->get('permintaan')->result_array();
	}

	public function insert_barang($data)
	{
		$this->db->insert('barang', $data);
	}
	public function simpan()
	{
		$kd_barang = $this->input->post('kd_barang');

		// Check if kd_barang already exists
		if ($this->exists_kd_barang($kd_barang)) {
			$this->session->set_flashData('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Data Gagal Ditambahkan</strong> Kode Barang sudah ada.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>');
			redirect('barang');
		}

		$data = [
			"kd_barang" => $kd_barang,
			"nama_barang" => $this->input->post('nama_barang'),
			"kategori" => $this->input->post('kategori'),
			"satuan" => $this->input->post('satuan'),
			"stok" => $this->input->post('stok'),
			"harga_satuan" => $this->input->post('harga_satuan'),
			"id_harga_pengajuan" => $this->input->post('id_harga_pengajuan'),
		];

		$this->db->insert('barang', $data);

		$this->session->set_flashData('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Data Berhasil Ditambahkan</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>');
		redirect('barang');
	}


	public function hapus($id)
	{
		$this->db->where('id_barang', $id);
		$this->db->delete('barang');
		$this->session->set_flashData('message', '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Data Berhasil Dihapus</strong> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>');
	}
	
	public function hapus_api($id, $kabupaten_id = null)
	{
		$this->db->where('id_barang', $id);
		if ($kabupaten_id !== null) {
			$this->db->where('kabupaten_id', $kabupaten_id);
		}
		return $this->db->delete('barang');
	}

	public function ubah()
	{
		$kd_barang = $this->input->post('kd_barang');
		$id_barang = $this->input->post('id');
		$kabupaten_id_val = $this->input->post('kabupaten_id'); // Diasumsikan ada input kabupaten_id

		// Check for duplicate kd_barang
		$this->db->select('id_barang');
		$this->db->from('barang');
		$this->db->where('kd_barang', $kd_barang);
		if ($kabupaten_id_val !== null) {
			$this->db->where('kabupaten_id', $kabupaten_id_val);
		}
		$this->db->where('id_barang !=', $id_barang); // Exclude the current record
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			// If duplicate kd_barang found, set flashdata and redirect
			$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Data Gagal Diupdate</strong> Kode Barang sudah digunakan oleh barang lain.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>');
			redirect('barang'); // Ganti dengan URL yang sesuai
			return;
		}

		$data = [
			"kd_barang" => $this->input->post('kd_barang'),
			"nama_barang" => $this->input->post('nama_barang'),
			"kategori" => $this->input->post('kategori'),
			"satuan" => $this->input->post('satuan'),
			"stok" => $this->input->post('stok'),
			"harga_satuan" => $this->input->post('harga_satuan'),
		];

		// Memulai transaksi
		$this->db->trans_start();

		// Melakukan update
		$this->db->where('id_barang', $id_barang);
		$this->db->update('barang', $data);

		// Menyelesaikan transaksi
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			// Jika transaksi gagal, ambil pesan kesalahan
			$error = $this->db->error();

			// Tampilkan pesan kesalahan menggunakan flashdata
			$this->session->set_flashdata('message', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Data Gagal Diupdate</strong> Error: ' . $error['message'] . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>');
		} else {
			// Jika transaksi berhasil, tampilkan pesan sukses
			$this->session->set_flashdata('message', '<div class="alert alert-primary alert-dismissible fade show" role="alert">
            <strong>Data Berhasil Diupdate</strong> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>');
		}

		// Redirect kembali ke halaman yang sesuai
		redirect('barang'); // Ganti dengan URL yang sesuai
	}
	
	public function ubah_api($id,$data)
	{
	    // Check for duplicate kd_barang
		$this->db->select('id_barang');
		$this->db->from('barang');
		$this->db->where('kd_barang',$data['kd_barang']);
		$this->db->where('id_barang !=', $id); // Exclude the current record
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return false;
		}
	    
		$this->db->where('id_barang', $id);
        return $this->db->update('barang', $data);
	}


	public function get_id($id)
	{
		return $this->db->get_where('barang', ['id_barang' => $id])->row_array();
	}

	public function kurangi_stok_barang($id_barang, $jumlah)
	{
		$this->db->set('stok', 'stok - ' . $jumlah, FALSE);
		$this->db->where('id_barang', $id_barang);
		$this->db->update('barang');
	}

	public function tambah_stok_barang($id_barang, $jumlah)
	{
		$this->db->set('stok', 'stok + ' . $jumlah, FALSE);
		$this->db->where('id_barang', $id_barang);
		$this->db->update('barang');
	}


	public function get($id = null)
	{
		$this->db->select('*');
		$this->db->from('barang');
		if ($id != null) {
			$this->db->where('id_barang', $id);
		}
		return $this->db->get();
	}


	public function get_harga_pengajuan($id_barang)
	{
		$this->db->where('id_barang', $id_barang);
		$query = $this->db->get('id_harga_pengajuan');
		if ($query->num_rows() > 0) {
			return $query->row()->id_harga_pengajuan;
		}
		return 0;
	}


    public function get_max_kode()
	{
	    $this->db->select_max('kd_barang');
	    $this->db->where('CHAR_LENGTH(kd_barang) >', 4 );
        $query = $this->db->get('barang');
                
        $result = $query->row();
        return $max_value = $result->kd_barang;
	}
    
	public function update_harga_pengajuan($harga_pengajuan_input)
	{


		$query = $this->db->get('barang')->result();
		if (sizeof($query) > 0) {

			foreach ($query as $barang) {
				// Calculate the new harga_pengajuan
				$harga_satuan = $barang->harga_satuan;
				$harga_pengajuan = $harga_satuan * ($harga_pengajuan_input / 100) + $harga_satuan;

				$data = [
					'harga_pengajuan' => $harga_pengajuan
				];

				$this->db->where('id_barang', $barang->id_barang);
				$test = $this->db->get('harga_pengajuan')->result();

				if (sizeof($test) > 0) {
					$this->db->where('id_barang', $barang->id_barang);
					$this->db->update('harga_pengajuan', $data);
				} else {
					$this->db->insert('harga_pengajuan', $data);
				}
			}
		} else {
			log_message('error', 'Error fetching barang data');
		}
	}

	public function fetch_subkategori()
	{
		$this->db->select('*');
		$this->db->from('sub_kategori');
		return $this->db->get()->result_array();
	}
}
