<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Kabupaten_model extends CI_Model
{

	
	public function get_all($kabupaten_id = null)
	{
		$this->db->select('*');
		$this->db->from('master_kabupaten');
		$query = $this->db->get();
		return $query->result_array();
	}
}