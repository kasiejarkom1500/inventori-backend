<?php
class Laporan_permintaan_model extends CI_Model{

    

	public function get_approved_permintaan($tgl_mulai, $tgl_akhir)
    {
        $this->db->select('*');
        $this->db->from('permintaan');
        $this->db->join('barang', 'permintaan.id_barang = barang.id_barang');
        $this->db->join('admin_ruangan', 'admin_ruangan.id_ruangan = permintaan.id_ruangan');
        $this->db->where('permintaan.status', 'Approved');
        // $this->db->where('permintaan.id_ruangan = ', $id_ruangan);
        $this->db->where('permintaan.tanggal >=', $tgl_mulai);
        $this->db->where('permintaan.tanggal <=', $tgl_akhir);
        return $this->db->get()->result_array();
    }
}
