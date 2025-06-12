<?php
class Permintaan_barang_model extends CI_Model
{
	
	public function tampil_data()
    {
		$this->db->select('*');
        $this->db->from('permintaan');
        $this->db->join('barang', 'permintaan.id_barang = barang.id_barang');
    	$this->db->join('user', 'permintaan.id_user = user.id_user'); // Menggunakan left join untuk memastikan semua data anak ditampilkan
        $this->db->join('admin_ruangan', 'permintaan.id_ruangan = admin_ruangan.id_ruangan');
        $query = $this->db->get();
        return $query->result_array();
        
    }
    
    public function simpan_current_stok($id_permintaan,$jumlah) 
    {
        $this->db->set('current_stok', $jumlah, FALSE);
		$this->db->where('id_permintaan', $id_permintaan);
		$this->db->update('permintaan');
    }
    
    // public function karken_data($tgl_mulai,$tgl_selesai)
    // {
    //     // 	$this->db->select("barang.id_barang,barang.nama_barang,barang.kd_barang,barang.satuan,permintaan.tanggal as tgl_keluar,pembelian.tgl_masuk, barang_masuk.jumlah as masuk, permintaan.jumlah as keluar, concat(REPLACE(permintaan.tanggal, '-', ''),'-',permintaan.id_ruangan) as faktur_keluar, pembelian.no_kuitansi as faktur_masuk ");
    //     //     $this->db->from('barang');
    //     //     $this->db->join('permintaan', 'permintaan.id_barang = barang.id_barang');
    //     // 	$this->db->join('user', 'permintaan.id_user = user.id_user');
    //     //     $this->db->join('admin_ruangan', 'permintaan.id_ruangan = admin_ruangan.id_ruangan');
    //     //     $this->db->join('barang_masuk', 'barang_masuk.id_barang = barang.id_barang');
    //     //     $this->db->join('pembelian', 'pembelian.id_pembelian = barang_masuk.id_pembelian');
    
    //     $transaksi = 
    // "SELECT 
    //     masuk.id_barang,
    //     barang.nama_barang,
    //     barang.kd_barang,
    //     barang.satuan,
    //     masuk.tgl_masuk AS tanggal,
    //     beli.no_kuitansi as faktur,
    //     SUM(masuk.jumlah) AS jumlah_masuk,
    //     0 AS jumlah_keluar,
    //     current_stok,
    //     sumber.nama_sumber AS uraian,
    //     barang.kategori
    // FROM 
    //     barang_masuk AS masuk
    // JOIN
    //     pembelian AS beli
    // ON
    //     beli.id_pembelian = masuk.id_pembelian
    // JOIN
    //     barang AS barang
    // ON
    //     barang.id_barang = masuk.id_barang
    // JOIN
    //     mst_sumber AS sumber
    // ON
    //     sumber.id_sumber = beli.id_sumber
    // GROUP BY 
    //     masuk.id_barang, masuk.tgl_masuk, beli.no_kuitansi
        
    // UNION
    
    // SELECT 
    //     keluar.id_barang,
    //     barang.nama_barang,
    //     barang.kd_barang,
    //     barang.satuan,
    //     keluar.tanggal AS tanggal,
    //     concat(REPLACE(keluar.tanggal, '-', ''),'-',keluar.id_ruangan) AS faktur,
    //     0 AS jumlah_masuk,
    //     SUM(keluar.jumlah) AS jumlah_keluar,
    //     current_stok,
    //     ruangan.ruangan AS uraian,
    //     barang.kategori
    // FROM 
    //     permintaan AS keluar
    // JOIN
    //     barang AS barang
    // ON
    //     barang.id_barang = keluar.id_barang
    // JOIN
    //     admin_ruangan AS ruangan
    // ON
    //     ruangan.id_ruangan = keluar.id_ruangan
    // GROUP BY 
    //     keluar.id_barang, keluar.tanggal,faktur";
    
    
    // $query = $this->db->query($transaksi);
    
    // // $error = $this->db->error();
    // // echo $error['message'];
    // // die;
    
   
    // return $query->result_array();
        
    // }
    
    public function karken_data($tgl_mulai, $tgl_selesai)
    {
        
        
        $transaksi = "
        SELECT 
            masuk.id_barang,
            barang.nama_barang,
            barang.kd_barang,
            barang.satuan,
            masuk.tgl_masuk AS tanggal,
            beli.no_kuitansi as faktur,
            SUM(masuk.jumlah) AS jumlah_masuk,
            0 AS jumlah_keluar,
            current_stok,
            sumber.nama_sumber AS uraian,
            barang.kategori
        FROM 
            barang_masuk AS masuk
        JOIN
            pembelian AS beli
        ON
            beli.id_pembelian = masuk.id_pembelian
        JOIN
            barang AS barang
        ON
            barang.id_barang = masuk.id_barang
        JOIN
            mst_sumber AS sumber
        ON
            sumber.id_sumber = beli.id_sumber
        WHERE
            masuk.tgl_masuk BETWEEN ? AND ?  -- Parameter binding untuk tanggal masuk
        GROUP BY 
            masuk.id_barang, masuk.tgl_masuk, beli.no_kuitansi
            
        UNION
        
        SELECT 
            keluar.id_barang,
            barang.nama_barang,
            barang.kd_barang,
            barang.satuan,
            keluar.tanggal AS tanggal,
            concat(REPLACE(keluar.tanggal, '-', ''),'-',keluar.id_ruangan) AS faktur,
            0 AS jumlah_masuk,
            SUM(keluar.jumlah) AS jumlah_keluar,
            current_stok,
            ruangan.ruangan AS uraian,
            barang.kategori
        FROM 
            permintaan AS keluar
        JOIN
            barang AS barang
        ON
            barang.id_barang = keluar.id_barang
        JOIN
            admin_ruangan AS ruangan
        ON
            ruangan.id_ruangan = keluar.id_ruangan
        WHERE
            keluar.tanggal BETWEEN ? AND ?  -- Parameter binding untuk tanggal keluar
        GROUP BY 
            keluar.id_barang, keluar.tanggal, faktur";
        
        $query = $this->db->query($transaksi, array($tgl_mulai, $tgl_selesai, $tgl_mulai, $tgl_selesai));
        
        return $query->result_array();
    }
    
    public function summary_karken($tgl_mulai, $tgl_selesai){
        
        //  var_dump($this->db->escape($tgl_mulai));
        //  die;
        
        // Query untuk mendapatkan data
        $this->db->select('combined_data.id_barang, nama_barang, SUM(total_masuk) AS total_masuk, SUM(total_keluar) AS total_keluar, barang.stok, barang.kategori')
                 ->from('( 
                     SELECT id_barang, jumlah AS total_masuk, 0 AS total_keluar 
                     FROM barang_masuk
                     WHERE tgl_masuk BETWEEN '.$this->db->escape($tgl_mulai).' AND '.$this->db->escape($tgl_selesai).'
                     UNION ALL
                     SELECT id_barang, 0 AS total_masuk, jumlah AS total_keluar 
                     FROM permintaan 
                     WHERE tanggal BETWEEN '.$this->db->escape($tgl_mulai).' AND '.$this->db->escape($tgl_selesai).'
                 ) AS combined_data')
                 ->join('barang','barang.id_barang = combined_data.id_barang')
                 ->group_by('combined_data.id_barang, nama_barang, barang.stok, barang.kategori')
                 ->order_by('id_barang', 'ASC');
        
        $query = $this->db->get();
        
        // $error = $this->db->error();
        // echo $error['message'];
        // die;
            

            
            // // $error = $this->db->error();
            // var_dump($query->result_array());
            // die;
            
            return $query->result_array();
    }

	public function get_grouped_by_date() {
        $this->db->select('tanggal, permintaan.id_user,permintaan.id_ruangan,admin_ruangan.ruangan, COUNT(*) as jumlah_permintaan, SUM(CASE WHEN status = "Approved" THEN 1 ELSE 0 END) AS "Approved", SUM(CASE WHEN status = "Pending" THEN 1 ELSE 0 END) AS "Pending", SUM(CASE WHEN status = "Rejected" THEN 1 ELSE 0 END) AS "Rejected" ');
        $this->db->from('permintaan');
        $this->db->join('admin_ruangan','permintaan.id_ruangan = admin_ruangan.id_ruangan');
        $this->db->group_by(array('tanggal', 'permintaan.id_user','admin_ruangan.ruangan'));
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function get_by_date($id_ruangan,$tanggal) {
        $this->db->select('id_permintaan, tanggal,id_ruangan, kd_barang, nama_barang, kategori, satuan, stok, jumlah, status');
        $this->db->from('permintaan');
        $this->db->join('barang','permintaan.id_barang = barang.id_barang');
        $this->db->where('id_ruangan',$id_ruangan);
        $this->db->where('tanggal',$tanggal);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function get_approved_by_date($id_ruangan,$tanggal) {
        $this->db->select('permintaan.id_permintaan,permintaan.tanggal,ruangan, kd_barang, nama_barang, kategori, satuan, jumlah, status');
        $this->db->from('permintaan');
        $this->db->join('barang','permintaan.id_barang = barang.id_barang');
        $this->db->join('admin_ruangan','permintaan.id_ruangan = admin_ruangan.id_ruangan');
        $this->db->where('permintaan.id_ruangan',$id_ruangan);
        $this->db->where('tanggal',$tanggal);
        $this->db->where('status','Approved');
        $query = $this->db->get();
        return $query->result_array();
    }

	public function get_by_date_and_user($tanggal, $id_user) {
        $this->db->select('*');
        $this->db->from('permintaan p');
        $this->db->join('barang b', 'p.id_barang = b.id_barang', 'left');
        $this->db->join('user u', 'p.id_user = u.id_user', 'left');
		$this->db->join('admin_ruangan r', 'p.id_ruangan = r.id_ruangan', 'left');
        $this->db->where('p.tanggal', $tanggal);
        $this->db->where('p.id_user', $id_user);
        $query = $this->db->get();
        return $query->result_array();
    }
	
	public function insert($data) {
		//var_dump($data);
		$this->db->insert('permintaan', $data);

		$error = $this->db->error();
		if (!empty($error['code'])) {
			// Display error message as JSON
			echo json_encode([
				'status' => 'error',
				'message' => $error['message']
			]);
		}
    }

	public function get_user_requests($user_id) {
        $this->db->where('id_user', $user_id);
        return $this->db->get('permintaan')->result();
    }

	public function create_request($data) {
        $this->db->insert('permintaan', $data);
    }

	public function update_status($id_permintaan, $status , $jumlah) {
        $data = array(
            'status' => $status,
			'jumlah' => $jumlah
        );
        $this->db->where('id_permintaan', $id_permintaan);
        $this->db->update('permintaan', $data);
    }

	public function get_jumlah_permintaan($id_permintaan) {
        $this->db->select('jumlah');
        $this->db->where('id_permintaan', $id_permintaan);
        $query = $this->db->get('permintaan');
        if ($query->num_rows() > 0) {
            return $query->row()->jumlah;
        }
        return 0; // Jika tidak ditemukan data atau ada kesalahan lainnya
    }

    public function get_id_barang($id_permintaan) {
        $this->db->select('id_barang');
        $this->db->where('id_permintaan', $id_permintaan);
        $query = $this->db->get('permintaan');
        if ($query->num_rows() > 0) {
            return $query->row()->id_barang;
        }
        return null; // Jika tidak ditemukan data atau ada kesalahan lainnya
    }


	public function get($id = null)

    {
        $this->db->select('*');
        $this->db->where('status', 'Approved');
        $this->db->from('permintaan');
        if($id != null)
        {
            $this->db->where('id_permintaan',$id);
        }
        return $this->db->get();
    }
	
}
