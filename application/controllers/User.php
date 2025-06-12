<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public $db;
	public $session;
	public $User_model;
	public $PengajuanModel;


    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        
        $this->load->model('User_model');
    }
    
    public function index()
	{
		is_logged_in();
        $data['title'] = 'Profile';

        $user = $this->db->get_where('user',['username' => $this->session->userdata('username')])->row_array();
		$admin = $this->db->get_where('admin',['username' => $this->session->userdata('username')])->row_array();

		if ($user == TRUE) :
			$data['user'] = $user;
		elseif ($admin == TRUE) :
			$data['user'] = $admin;
		endif;

        $this->load->view('templates/header1', $data);
        $this->load->view('user/home/index', $data);
        $this->load->view('templates/footer');
	}

    public function simpan()
{
    // Panggil model
    $this->load->model('User_model');

    // Panggil fungsi simpan dari model
    $this->User_model->simpan();

    // Redirect atau tampilkan pesan sukses
    redirect('admin/data_user'); 
}


    
}
