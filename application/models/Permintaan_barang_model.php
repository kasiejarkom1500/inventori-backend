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

    public function simpan_current_stok($id_permintaan, $jumlah)
    {
        $this->db->set('current_stok', $jumlah, FALSE);
        $this->db->where('id_permintaan', $id_permintaan);
        $this->db->update('permintaan');
    }



    public function karken_data($tgl_mulai, $tgl_selesai, $kabupaten_id = null)
    {


        $transaksi = "
        SELECT * FROM (
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
                masuk.tgl_masuk BETWEEN ? AND ? AND barang.kabupaten_id = ?
            GROUP BY 
                masuk.id_barang, masuk.tgl_masuk, beli.no_kuitansi, barang.nama_barang, barang.kd_barang, barang.satuan, current_stok, sumber.nama_sumber, barang.kategori
                
            UNION
            
            SELECT 
                keluar.id_barang,
                barang.nama_barang,
                barang.kd_barang,
                barang.satuan,
                keluar.tanggal AS tanggal,
                concat(REPLACE(keluar.tanggal, '-', ''),'-',keluar.id_ruangan) AS faktur,
                0 AS jumlah_masuk,
                SUM(keluar.jumlah_approved) AS jumlah_keluar,
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
                keluar.tanggal BETWEEN ? AND ? AND barang.kabupaten_id = ? AND status = 'Approved' AND keluar.jumlah_approved > 0
            GROUP BY 
                keluar.id_barang, keluar.tanggal, faktur, barang.nama_barang, barang.kd_barang, barang.satuan, current_stok, ruangan.ruangan, barang.kategori
        ) AS union_result
        ORDER BY tanggal ASC";
        $query = $this->db->query($transaksi, array($tgl_mulai, $tgl_selesai, $kabupaten_id, $tgl_mulai, $tgl_selesai, $kabupaten_id));

        return $query->result_array();
    }

    public function summary_karken($tgl_mulai, $tgl_selesai, $kabupaten_id)
    {

        //  var_dump($this->db->escape($tgl_mulai));
        //  die;

        $sql =
            "
            SELECT combined_data.id_barang, nama_barang, 
                SUM(total_masuk) AS total_masuk, 
                SUM(total_keluar) AS total_keluar, 
                barang.stok, barang.kategori
            FROM (
                SELECT id_barang, jumlah AS total_masuk, 0 AS total_keluar 
                FROM barang_masuk
                WHERE tgl_masuk BETWEEN " . $this->db->escape($tgl_mulai) . " AND " . $this->db->escape($tgl_selesai) . "
                
                UNION ALL
                
                SELECT id_barang, 0 AS total_masuk, jumlah_approved AS total_keluar 
                FROM permintaan 
                WHERE tanggal BETWEEN " . $this->db->escape($tgl_mulai) . " AND " . $this->db->escape($tgl_selesai) . "AND status = 'Approved'
            ) AS combined_data
            JOIN barang ON barang.id_barang = combined_data.id_barang
            WHERE barang.kabupaten_id = " . $kabupaten_id . "
            GROUP BY combined_data.id_barang, nama_barang, barang.stok, barang.kategori
            ORDER BY combined_data.id_barang ASC";

        $query = $this->db->query($sql);

        // $check = $this->db->last_query();
        // $error = $this->db->error();
        // echo $check;
        // die;



        // // $error = $this->db->error();
        // var_dump($query->result_array());
        // die;

        return $query->result_array();
    }

    public function get_grouped_by_date($kabupaten_id = null)
    {

        $this->db->select('tanggal, permintaan.id_user,permintaan.id_ruangan,admin_ruangan.ruangan, COUNT(*) as jumlah_permintaan, SUM(CASE WHEN status = "Approved" THEN 1 ELSE 0 END) AS "Approved", SUM(CASE WHEN status = "Pending" THEN 1 ELSE 0 END) AS "Pending", SUM(CASE WHEN status = "Rejected" THEN 1 ELSE 0 END) AS "Rejected" ');
        $this->db->from('permintaan');
        $this->db->join('admin_ruangan', 'permintaan.id_ruangan = admin_ruangan.id_ruangan');
        if (!$kabupaten_id == null) {
            $this->db->where('admin_ruangan.id_satker', $kabupaten_id);
        }
        $this->db->group_by(array('tanggal', 'permintaan.id_user', 'permintaan.id_ruangan', 'admin_ruangan.ruangan'));
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_grouped_filter_by_date($bulanTahun, $kabupaten_id = null)
    { // Parameter $bulanTahun diharapkan dalam format YYYY-MM
        $this->db->select('permintaan.tanggal, permintaan.id_user, permintaan.id_ruangan, admin_ruangan.ruangan, COUNT(*) as jumlah_jenis_barang, SUM(CASE WHEN status = "Approved" THEN 1 ELSE 0 END) AS "Approved", SUM(CASE WHEN status = "Pending" THEN 1 ELSE 0 END) AS "Pending", SUM(CASE WHEN status = "Rejected" THEN 1 ELSE 0 END) AS "Rejected" ');
        $this->db->from('permintaan');
        $this->db->join('admin_ruangan', 'permintaan.id_ruangan = admin_ruangan.id_ruangan');
        $this->db->where("DATE_FORMAT(permintaan.tanggal, '%Y-%m') =", $bulanTahun);
        if (!$kabupaten_id == null) {
            $this->db->where('admin_ruangan.id_satker', $kabupaten_id);
        }
        $this->db->group_by(array('permintaan.tanggal', 'permintaan.id_user', 'permintaan.id_ruangan', 'admin_ruangan.ruangan'));
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_by_date($id_ruangan, $tanggal)
    {
        $this->db->select('id_permintaan, tanggal,id_ruangan, kd_barang, nama_barang, kategori, satuan, stok, jumlah, status,is_buat_pengajuan,jumlah_approved');
        $this->db->from('permintaan');
        $this->db->join('barang', 'permintaan.id_barang = barang.id_barang');
        $this->db->where('id_ruangan', $id_ruangan);
        $this->db->where('tanggal', $tanggal);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_approved_by_date($id_ruangan, $tanggal)
    {
        $this->db->select('permintaan.id_permintaan,permintaan.tanggal,ruangan, kd_barang, nama_barang, kategori, satuan, jumlah, status');
        $this->db->from('permintaan');
        $this->db->join('barang', 'permintaan.id_barang = barang.id_barang');
        $this->db->join('admin_ruangan', 'permintaan.id_ruangan = admin_ruangan.id_ruangan');
        $this->db->where('permintaan.id_ruangan', $id_ruangan);
        $this->db->where('tanggal', $tanggal);
        $this->db->where('status', 'Approved');
        $query = $this->db->get();
        return $query->result_array();
    }

    //Mendapatkan pemakaian barang selama 1 tahun terakhir group by ruangan
    public function get_pemakaian_tahunan_ruangan($tahun)
    {
        $this->db->select('admin_ruangan.ruangan, SUM(permintaan.jumlah) as total_pemakaian');
        $this->db->from('permintaan');
        $this->db->join('admin_ruangan', 'permintaan.id_ruangan = admin_ruangan.id_ruangan');
        $this->db->where('YEAR(permintaan.tanggal)', $tahun);
        $this->db->where('permintaan.status', 'Approved');
        $this->db->group_by('admin_ruangan.ruangan');
        $query = $this->db->get();
        return $query->result_array();
    }

    // Mendapatkan pemakaian barang berdasarkan tahun dan kategori group by bulan dan tahun status nya Approved 
    public function get_pemakaian_bulanan($tahun, $kategori)
    {
        $this->db->select('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun, SUM(jumlah) as total_pemakaian');
        $this->db->from('permintaan');
        $this->db->join('barang', 'permintaan.id_barang = barang.id_barang');
        $this->db->where('YEAR(tanggal)', $tahun);
        $this->db->where('barang.kategori', $kategori);
        $this->db->where('status', 'Approved');
        $this->db->group_by(array('bulan', 'tahun'));
        $query = $this->db->get();
        return $query->result_array();
    }

    //Mendapatakan nama barang dan jumlahnya di permintaan group by id_barang dan nama barang sort by jumlah descending
    public function get_barang_terbanyak($tahun)
    {
        $this->db->select('barang.kd_subkategori, sub_kategori.sub_kategori,barang.id_barang,  barang.nama_barang, SUM(permintaan.jumlah) as total_jumlah');
        $this->db->from('permintaan');
        $this->db->join('barang', 'permintaan.id_barang = barang.id_barang');
        $this->db->join('sub_kategori', 'sub_kategori.kd_subkategori = barang.kd_subkategori');
        $this->db->where('permintaan.status', 'Approved');
        $this->db->where('YEAR(tanggal)', $tahun);
        $this->db->group_by('barang.kd_subkategori, sub_kategori.sub_kategori,barang.id_barang,  barang.nama_barang');
        $this->db->order_by('total_jumlah', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_by_date_and_user($tanggal, $id_user)
    {
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

    public function insert($data)
    {
        //var_dump($data);
        $this->db->insert('permintaan', $data);
        if ($this->db->affected_rows() == 0) {
            return false; // Jika tidak ada baris yang terpengaruh, berarti insert gagal
        }
        // Jika insert berhasil, kita bisa mengembalikan ID dari permintaan yang baru saja dimasukkan
        return $this->db->insert_id(); // Mengembalikan ID dari permintaan yang baru saja dimasukkan
    }

    public function get_user_requests($user_id)
    {
        $this->db->where('id_user', $user_id);
        return $this->db->get('permintaan')->result();
    }

    public function create_request($data)
    {
        $this->db->insert('permintaan', $data);
    }

    public function update_status($id_permintaan, $data)
    {
        // $data = array(
        //     'status' => $status,
        //     'jumlah_approved' => $jumlah,
        //     'is_buat_pengajuan' => $is_buat_pengajuan
        // );
        $this->db->where('id_permintaan', $id_permintaan);
        $this->db->update('permintaan', $data);
    }

    public function get_permintaan($id_permintaan)
    {
        $this->db->select('*');
        $this->db->where('id_permintaan', $id_permintaan);
        $query = $this->db->get('permintaan');
        return $query->row_array();
    }

    public function get_id_barang($id_permintaan)
    {
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
        if ($id != null) {
            $this->db->where('id_permintaan', $id);
        }
        return $this->db->get();
    }

    public function catat_keluar_fifo($data)
    {
        return $this->db->insert('barang_keluar_detail', $data);

        // $check = $this->db->last_query();
        // $error = $this->db->error();
        // echo $check;
        // die;



        // // $error = $this->db->error();
        // var_dump($query->result_array());
        // die;
    }

    public function get_log_keluar_by_permintaan($id_permintaan)
    {
        $this->db->select('*');
        $this->db->from('barang_keluar_detail');
        $this->db->where('id_permintaan', $id_permintaan);
        $this->db->where('deleted_at IS NULL'); // Pastikan hanya mengambil data yang belum dihapus
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return []; // Mengembalikan array kosong jika tidak ada data
        }
    }

    //function untuk mengupdate kolom barang_keluar_detail deleted_at dengan current timestamp
    public function soft_delete_keluar($id_bk_detail)
    {
        $this->db->set('deleted_at', 'NOW()', FALSE);
        $this->db->where('id_bk_detail', $id_bk_detail);
        return $this->db->update('barang_keluar_detail');
    }

    public function hapus_permintaan($id_permintaan)
    {
        $this->db->where('id_permintaan', $id_permintaan);
        return $this->db->delete('permintaan');
    }
}
