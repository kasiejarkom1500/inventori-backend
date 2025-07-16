<?php
class Laporan_pengajuan_model extends CI_Model{

    

    
    public function get_approved_pengajuan_api($tgl_mulai, $tgl_akhir,$kabupaten_id = null)
    {
        $this->db->select('pengajuan.tanggal, barang.kd_barang, barang.nama_barang, barang.kategori, barang.satuan, pengajuan.harga_pengajuan as harga_satuan, pengajuan.jumlah, pengajuan.total_harga , pengajuan.status');
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
        $this->db->select('barang.kd_barang, barang.nama_barang, barang.kategori ,barang.satuan, pengajuan.harga_pengajuan as harga_satuan,barang.stok,barang.stok_dasar, sum(pengajuan.jumlah) as jumlah, sum(pengajuan.total_harga) as total_harga, mst_program.nama as program, mst_kegiatan.nama as kegiatan, mst_kro.nama as kro, mst_ro.nama as ro, v_komponen_fix.nama as komponen');
        $this->db->from('pengajuan');
        $this->db->join('mst_program', 'pengajuan.kd_program = mst_program.kd_program');
        $this->db->join('mst_kegiatan', 'pengajuan.kd_kegiatan = mst_kegiatan.kd_kegiatan');
        $this->db->join('mst_kro', 'pengajuan.kd_kro = mst_kro.kd_kro');
        $this->db->join('mst_ro', 'pengajuan.kd_ro = mst_ro.kd_ro');
        $this->db->join('v_komponen_fix', 'pengajuan.kd_ro = v_komponen_fix.kd_ro AND pengajuan.kd_komponen = v_komponen_fix.kd_komponen');
        $this->db->join('barang', 'pengajuan.id_barang = barang.id_barang');
        $this->db->where('pengajuan.status', 'Approved');
        $this->db->where('pengajuan.tanggal >=', $tgl_mulai);
        $this->db->where('pengajuan.tanggal <=', $tgl_akhir);
        $this->db->group_by('barang.kd_barang, barang.nama_barang, barang.kategori, barang.satuan, pengajuan.harga_pengajuan,barang.stok,barang.stok_dasar, mst_program.nama , mst_kegiatan.nama, mst_kro.nama , mst_ro.nama , v_komponen_fix.nama ');
        $query = $this->db->get();

        // $check = $this->db->last_query();
        // $error = $this->db->error();
        // echo $check;
        // echo $error['message'];
        // die;

        
        return $query->result_array();
    }
}
