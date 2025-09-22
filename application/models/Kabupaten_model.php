<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Kabupaten_model extends CI_Model
{

	
	public function get_all($kabupaten_id = null)
	{
		$this->db->select('*');
		$this->db->from('master_kabupaten');
		if ($kabupaten_id !== null) {
			$this->db->where('id', $kabupaten_id);
		}
		$query = $this->db->get();
		return $query->result_array();
	}
}