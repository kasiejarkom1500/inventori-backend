<?php

use function PHPUnit\Framework\isEmpty;

class Pok_model extends CI_Model
{
	

	public function get_program()
    {
	$this->db->select('*');
    $this->db->from('mst_program');

    $query = $this->db->get();
    return $query->result_array();
        
    }

    public function get_kegiatan($kd_program) 
    {
        $this->db->select('*');
        $this->db->from('mst_kegiatan');
        $this->db->where('kd_program', $kd_program);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_kro($kd_kegiatan) 
    {
        $this->db->select('*');
        $this->db->from('mst_kro');
        $this->db->where('kd_kegiatan', $kd_kegiatan);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_ro($kd_kro) 
    {
        $this->db->select('*');
        $this->db->from('mst_ro');
        $this->db->where('kd_kro', $kd_kro);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_komponen($kd_ro) 
    {
        $this->db->select('*');
        $this->db->from('mst_komponen');
        $this->db->where('kd_ro', $kd_ro);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_all_komponen() 
    {
        $this->db->select('*');
        $this->db->from('mst_komponen');


        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_gabungan_pok($kd_program = null, $kd_kegiatan = null, $kd_kro = null, $kd_ro = null, $kd_komponen = null)
    {

        $this->db->select('*');
        $this->db->from('mst_pok');
        //cek jika string kosong atau null
        if (!empty($kd_program)) {
            $this->db->where('kd_program', $kd_program);
        }
        if (!empty($kd_kegiatan)) {
            $this->db->where('kd_kegiatan', $kd_kegiatan);
        }
        if (!empty($kd_kro)) {
            $this->db->where('kd_kro', $kd_kro);
        }
        if (!empty($kd_ro)) {
            $this->db->where('kd_ro', $kd_ro);
        }
        if (!empty($kd_komponen)) {
            $this->db->where('kd_komponen', $kd_komponen);
        }

        $query = $this->db->get();
        return $query->result_array();

    }

	
	
}
