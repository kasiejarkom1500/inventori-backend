<?php
class Laporan_stok_model extends CI_Model
{


	public function tampil_data()
	{
		$this->db->select('permintaan.*, barang.kd_barang, barang.nama_barang, barang.kategori, barang.satuan, user.username, admin_ruangan.ruangan');
		$this->db->from('permintaan');
		$this->db->join('barang', 'permintaan.id_barang = barang.id_barang');
		$this->db->join('user', 'permintaan.id_user = user.id_user'); // Menggunakan join untuk mengambil data yang relevan
		$this->db->join('admin_ruangan', 'permintaan.id_ruangan = admin_ruangan.id_ruangan');
		$this->db->where('permintaan.status', 'Approved');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_approved_permintaan($tgl_mulai, $tgl_akhir)
	{
		$this->db->select('*');
		$this->db->from('permintaan');
		$this->db->join('barang', 'permintaan.id_barang = barang.id_barang');
		$this->db->where('permintaan.status', 'Approved');
		$this->db->where('permintaan.tanggal >=', $tgl_mulai);
		$this->db->where('permintaan.tanggal <=', $tgl_akhir);
		return $this->db->get()->result_array();
	}


	public function filterByRuangan($kategori, $ruangan, $nama = null)
	{
		$this->db->select('permintaan.*, barang.*, admin_ruangan.*');
		$this->db->from('permintaan');
		$this->db->join('barang', 'permintaan.id_barang = barang.id_barang');
		$this->db->join('admin_ruangan', 'permintaan.id_ruangan = admin_ruangan.id_ruangan');
		if (isset($nama)) {
			$this->db->where('permintaan.status', 'Approved');
			$this->db->like('barang.nama_barang', trim($nama));
		}
		if ($kategori != "") {
			$this->db->where('permintaan.status', 'Approved');
			$this->db->where('barang.kategori', $kategori);
		}

		if ($ruangan != "") {
			$this->db->where('permintaan.status', 'Approved');
			$this->db->where('permintaan.id_ruangan', $ruangan);
		}

		$query = $this->db->get();

		log_message('debug', 'Executed Query: ' . $this->db->last_query());

		// Check for query errors
		if ($this->db->error()['code'] !== 0) {
			log_message('error', 'Database Query Error: ' . $this->db->error()['message']);
			return array(); // Return an empty array or handle the error appropriately
		}

		// Check if the query was successful
		if ($query) {
			$result = $query->result_array();
			return $result;
		} else {
			log_message('error', 'Failed Query: ' . $this->db->last_query());
			return array(); // Return an empty array if no results
		}
	}
}
