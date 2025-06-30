<?php
class Barang_masuk_model extends CI_Model
{


    public function tampil_data()
    {
        $this->db->select('*');
        $this->db->from('barang_masuk');
        $this->db->join('barang', 'barang_masuk.id_barang = barang.id_barang');
        $this->db->join('pengajuan', 'barang.id_barang = pengajuan.id_barang');
        $this->db->join('admin_ruangan', 'pengajuan.id_ruangan = admin_ruangan.id_ruangan');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function data_pembelian($kabupaten_id = null)
    {
        $this->db->select('pembelian.*, mst_sumber.nama_sumber');
        $this->db->from('pembelian');
        $this->db->join('mst_sumber', 'pembelian.id_sumber = mst_sumber.id_sumber');

        if ($kabupaten_id !== null) {
            $this->db->where('pembelian.id_satker', $kabupaten_id);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    // Mendapatkan total pemeblian yang dilakukan berdasarkan bulan
    public function total_pembelian_per_bulan()
    {
        $this->db->select('MONTH(tanggal_masuk) as bulan, YEAR(tanggal_masuk) as tahun, COUNT(*) as total_pembelian');
        $this->db->from('pembelian');
        $this->db->group_by(['bulan', 'tahun']);
        $this->db->order_by('tahun', 'ASC');
        $this->db->order_by('bulan', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }


    public function ubah_api($id, $data)
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
        $this->db->where('pembelian.id_pembelian', $id_pembelian);

        $query = $this->db->get();

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

    public function get_stok_masuk_fifo($id_barang)
    {
        return $this->db->order_by('tgl_masuk', 'ASC')
            ->get_where('barang_masuk', ['id_barang' => $id_barang])
            ->result_array();
    }

    public function kurangi_barang_masuk($id_barang_masuk, $jumlah)
    {
        $this->db->set('sisa_stok', 'sisa_stok - ' . (int)$jumlah, false)
            ->where('id_barang_masuk', $id_barang_masuk)
            ->update('barang_masuk');
        // Check if the update was successful
        if ($this->db->affected_rows() > 0) {
            // If successful, return true
            return true;
        } else {
            // If no rows were affected, return false
            return false;
        }
    }

    public function tambah_barang_masuk($id_barang_masuk, $jumlah)
    {
        return $this->db->set('sisa_stok', 'sisa_stok + ' . (int)$jumlah, false)
            ->where('id_barang_masuk', $id_barang_masuk)
            ->update('barang_masuk');
    }

    public function get_barang_details($id_barang)
    {
        $query = $this->db->select('id_barang, kd_barang, nama_barang, kategori, satuan,stok, harga_satuan, id_harga_pengajuan')
            ->from('barang')
            ->where('id_barang', $id_barang)
            ->get();

        return $query->row_array();
    }

    public function search_barang($term)
    {
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
