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
}
