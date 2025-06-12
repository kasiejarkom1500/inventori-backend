<?php
class Barang_keluar_model extends CI_Model
{
	

	public function tampil_data()
    {
		$this->db->select('*');
    $this->db->from('permintaan');
    $this->db->join('barang', 'permintaan.id_barang = barang.id_barang'); 
    $this->db->join('admin_ruangan', 'permintaan.id_ruangan = admin_ruangan.id_ruangan'); 
		$this->db->where('permintaan.status', 'Approved');
    $query = $this->db->get();
    return $query->result_array();
        
    }

	
	
}
