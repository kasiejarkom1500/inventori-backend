<?php
class Sumber_model extends CI_Model
{
	public function get_sumber()
	{
		return $this->db->get('mst_sumber')->result_array();
	}
}
