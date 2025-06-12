<?php
//untuk menampilkan data user berdasarkan id session
class Fungsi
{
    protected $ci;

    function __construct()
    {
        $this->ci = &get_instance();
    }

	public function count_barang() 
    {
        $this->ci->load->model('Barang_model');
        return $this->ci->Barang_model->get()->num_rows();
    }

	public function count_permintaan() 
    {
        $this->ci->load->model('Permintaan_barang_model');
        return $this->ci->Permintaan_barang_model->get()->num_rows();
    }


	public function count_pengajuan() 
    {
        $this->ci->load->model('Pengajuan_barang_model');
        return $this->ci->Pengajuan_barang_model->get()->num_rows();
    }

	public function count_harga() 
    {
        $this->ci->load->model('Pengajuan_barang_model');
        return $this->ci->Pengajuan_barang_model->get_approved_total();
    }
    

	function user_login()
    {
		$user_data = $this->ci->session->userdata;

		$this->ci->load->model('Admin_model');
		$admin = $this->ci->Admin_model->getByUsername($user_data['username']);
	

		return $admin;
    }

	function user_login1()
    {

		$this->ci->load->model('User_model');
        $user_data = $this->ci->session->userdata;
		$admin = $this->ci->User_model->get_user_by_username_all($user_data['username']);

        return $admin;
    }
    
	function user_login2()
    {
		$user_data = $this->ci->session->userdata;

		$this->ci->load->model('Admin_model');
		$admin = $this->ci->Admin_model->getByUsernameAdmin2($user_data['username']);
        return $admin;
    }


	public function dashboardTotalHargaPengajuanBulanIni() {
        $this->ci->load->database();
        $current_month = date('m');
        $current_year = date('Y');

        $this->ci->db->select_sum('harga_pengajuan');
        $this->ci->db->from('barang');
        $this->ci->db->where('MONTH(tanggal)', $current_month);
        $this->ci->db->where('YEAR(tanggal)', $current_year);
        $query = $this->ci->db->get();

        if ($query === false) {
            // Log the error or handle it as needed
            log_message('error', 'Database query failed: ' . $this->ci->db->last_query());
            return 0;
        }

        if ($query->num_rows() > 0) {
            return $query->row()->harga_pengajuan;
        } else {
            return 0;
        }
    }

    public function dashboardTotalPengajuanBulanIni() {
        $this->ci->load->database();
        $current_month = date('m');
        $current_year = date('Y');

        $this->ci->db->from('barang');
        $this->ci->db->where('MONTH(tanggal)', $current_month);
        $this->ci->db->where('YEAR(tanggal)', $current_year);
        return $this->ci->db->get();
    }

    public function dashboardTotalPermintaanBulanIni() {
        $this->ci->load->database();
        $current_month = date('m');
        $current_year = date('Y');

        $this->ci->db->select_sum('jumlah');
        $this->ci->db->from('permintaan');
        $this->ci->db->where('MONTH(tanggal)', $current_month);
        $this->ci->db->where('YEAR(tanggal)', $current_year);
        $query = $this->ci->db->get();

        if ($query === false) {
            // Log the error or handle it as needed
            log_message('error', 'Database query failed: ' . $this->ci->db->last_query());
            return 0;
        }

        if ($query->num_rows() > 0) {
            return $query->row()->jumlah;
        } else {
            return 0;
        }
    }
    
   

    
}
