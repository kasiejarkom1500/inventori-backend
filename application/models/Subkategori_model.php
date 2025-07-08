<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Barang_model extends CI_Model
{

	
	public function get_all($kabupaten_id = null)
	{
		$this->db->select('*');
		$this->db->from('subkategori');
		$query = $this->db->get();
		return $query->result_array();
	}
}