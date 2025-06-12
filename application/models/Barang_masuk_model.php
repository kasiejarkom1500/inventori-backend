<?php
class Barang_masuk_model extends CI_Model
{
	

	public function tampil_data()
    {
		$this->db->select('*');
    $this->db->from('barang_masuk');
    $this->db->join('barang', 'barang_masuk.id_barang = barang.id_barang');
		$this->db->join('pengajuan' , 'barang.id_barang = pengajuan.id_barang' );
		$this->db->join('admin_ruangan' , 'pengajuan.id_ruangan = admin_ruangan.id_ruangan' );

    $query = $this->db->get();
    return $query->result_array();
        
    }
    
    public function data_pembelian()
    {
	$this->db->select('pembelian.*,mst_sumber.nama_sumber');
    $this->db->from('pembelian');
    $this->db->join('mst_sumber', 'pembelian.id_sumber = mst_sumber.id_sumber');

    $query = $this->db->get();
    return $query->result_array();
        
    }
    
    public function ubah_api($id,$data)
	{
		$this->db->where('id_barang_masuk', $id);
        return $this->db->update('barang_masuk', $data);
	}
    
    public function detail_pembelian($id_pembelian)
    {
        
        
	$this->db->select('barang_masuk.*,barang.nama_barang');
    $this->db->from('barang_masuk');
    $this->db->join('pembelian', 'pembelian.id_pembelian = barang_masuk.id_pembelian');
    $this->db->join('barang', 'barang_masuk.id_barang = barang.id_barang');
    $this->db->where('pembelian.id_pembelian',$id_pembelian);

    $query = $this->db->get();
    // $error = $this->db->error();
    // var_dump($error);
    // die;
    return $query->result_array();
        
    }
    
    public function get_id($id)
    {
        return $this->db->get_where('barang_masuk', ['id_barang_masuk' => $id])->row_array();
    }



	public function simpan($data)
    {
        
        $this->db->insert('barang_masuk', $data);
        
        return $this->db->insert_id();

    }
    
    public function simpan_pembelian($data)
    {
        $this->db->insert('pembelian', $data);
        return $this->db->insert_id();
    }

	public function get_barang_details($id_barang)
    {
        $query = $this->db->select('id_barang, kd_barang, nama_barang, kategori, satuan,stok, harga_satuan, id_harga_pengajuan')
                          ->from('barang')
                          ->where('id_barang', $id_barang)
                          ->get();
        
        return $query->row_array();
    }

    public function search_barang($term) {
      $term = is_null($term) ? '' : $term; // Default to empty string if null
      $term = $this->db->escape_like_str($term);

      if ($term === '') {
          $query = $this->db->get('barang_masuk'); // Fetch all records if search is empty
      } else {
          $this->db->like('nama_barang', $term);
          $this->db->or_like('kd_barang', $term);
          $query = $this->db->select('id_barang as id, kd_barang, CONCAT(kd_barang, " - ", nama_barang) as text, kategori, satuan, stok, harga_satuan, id_harga_pengajuan')
                            ->from('barang')
                            ->get();
      }
      
      return $query->result_array();
  }
  

	public function hapus($id)
    {
        $this->db->where('id_barang_masuk', $id);
        $this->db->delete('barang_masuk');
        $this->session->set_flashData('message', '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Data Berhasil Dihapus</strong> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>');
    }
	
}
