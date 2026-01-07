<?php
class Sumber_model extends CI_Model
{
	public function get_sumber($kabupaten_id)
	{
		$this->db->select('*');
		$this->db->from('mst_sumber');
		// Jika kabupaten_id tidak null, tambahkan kondisi where
		if ($kabupaten_id !== null) {
			$this->db->where('id_satker', $kabupaten_id);
		}
		return $this->db->get()->result_array();
	}

	public function insert_sumber($data)
	{
		return $this->db->insert('mst_sumber', $data);
	}

	public function delete_sumber($id_sumber, $id_satker)
	{
		$this->db->where('id_sumber', $id_sumber);
		$this->db->where('id_satker', $id_satker);
		return $this->db->delete('mst_sumber');
	}

	public function update_sumber($id_sumber, $id_satker, $data)
	{
		$this->db->where('id_sumber', $id_sumber);
		$this->db->where('id_satker', $id_satker);
		return $this->db->update('mst_sumber', $data);
	}
}
