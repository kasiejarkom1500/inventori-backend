<?php
class Laporan_pengajuan_model extends CI_Model{

    

    
    public function get_approved_pengajuan_api($tgl_mulai, $tgl_akhir,$kabupaten_id = null)
    {
        $this->db->select('pengajuan.tanggal, barang.kd_barang, barang.nama_barang, barang.kategori, barang.satuan, barang.harga_satuan, pengajuan.jumlah, pengajuan.total_harga , pengajuan.status');
        $this->db->from('pengajuan');
        $this->db->join('barang', 'pengajuan.id_barang = barang.id_barang');
        $this->db->where('pengajuan.status', 'Approved');
        $this->db->where('pengajuan.tanggal >=', $tgl_mulai);
        $this->db->where('pengajuan.tanggal <=', $tgl_akhir);
        
        if ($kabupaten_id !== null) {
            $this->db->join('admin_ruangan', 'pengajuan.id_ruangan = admin_ruangan.id_ruangan');
            $this->db->where('admin_ruangan.id_satker', $kabupaten_id);
        }
        
        return $this->db->get()->result_array();
    }

    
    	public function get_approved_pengajuan_groupid($tgl_mulai, $tgl_akhir)
    {
        $this->db->select('barang.kd_barang, barang.nama_barang, barang.kategori, barang.satuan, barang.harga_satuan,barang.stok,barang.stok_dasar, sum(pengajuan.jumlah) as jumlah, sum(pengajuan.total_harga) as total_harga');
        $this->db->from('pengajuan');
        $this->db->join('barang', 'pengajuan.id_barang = barang.id_barang');
        $this->db->where('pengajuan.status', 'Approved');
        $this->db->where('pengajuan.tanggal >=', $tgl_mulai);
        $this->db->where('pengajuan.tanggal <=', $tgl_akhir);
        $this->db->group_by('barang.kd_barang, barang.nama_barang, barang.kategori, barang.satuan, barang.harga_satuan,barang.stok,barang.stok_dasar');
        return $this->db->get()->result_array();
    }
}
