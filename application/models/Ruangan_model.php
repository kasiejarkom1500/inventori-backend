<?php
class Ruangan_model extends CI_Model
{
	public function get_ruangan($id_user)
	{
		$this->db->select('*');
		$this->db->from('ruangan_user');
		$this->db->join('admin_ruangan', 'admin_ruangan.id_ruangan = ruangan_user.id_ruangan');
		$this->db->where('ruangan_user.id_user', $id_user);
		$query = $this->db->get();
		// var_dump($this->db->error()['message']);
		// die;

		return $query->result_array();
	}

	public function get_all($kabupaten_id = null)
	{
		$this->db->select('*');
		$this->db->from('admin_ruangan');
		if ($kabupaten_id !== null) {
			$this->db->where('id_satker', $kabupaten_id);
		}
		$query = $this->db->get();
		return $query->result_array();
	}
}
