<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RegisterController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//Load Dependencies
		$this->load->model('User_model');
	}

	// List all your items
	public function index()
	{
		$data['title'] = 'Register';

		
		$this->form_validation->set_rules('nama','Nama','trim|required|alpha_numeric_spaces');
		$this->form_validation->set_rules('email','email','trim|required');
		$this->form_validation->set_rules('username','Username','trim|required|alpha_numeric_spaces|callback_username_check');
		$this->form_validation->set_rules('password','Password','trim|required|alpha_numeric_spaces|min_length[6]|max_length[15]');
		

		if ($this->form_validation->run() == FALSE) :
			$this->load->view('register');
		else :
			$params = [
				
				'nama'			=> htmlspecialchars($this->input->post('nama',TRUE)),
				'email'			=> htmlspecialchars($this->input->post('email',TRUE)),
				'username'		=> htmlspecialchars($this->input->post('username',TRUE)),
				'password'		=> password_hash(htmlspecialchars($this->input->post('password',TRUE)), PASSWORD_DEFAULT),
				
			];

			$resp = $this->User_model->create($params);

			if ($resp) :
				$this->session->set_flashdata('msg','<div class="alert alert-primary" role="alert">
					Register berhasil, Silahkan Login!
					</div>');

				redirect('Auth/LoginController');
			else :
				$this->session->set_flashdata('msg','<div class="alert alert-danger" role="alert">
					Register gagal!
					</div>');

				redirect('Auth/RegisterController');
			endif;

		endif;
	}

	public function username_check($str = NULL)
	{
		if (!empty($str)) :
			$user = $this->db->get_where('user',['username' => $str])->row_array();

			$admin = $this->db->get_where('admin',['username' => $str])->row_array();

			if ($user == TRUE OR $admin == TRUE) :

				$this->form_validation->set_message('username_check', 'Username ini sudah terdaftar ada.');

				return FALSE;
			else :
				return TRUE;
			endif;
		else :
			$this->form_validation->set_message('username_check', 'Inputan Kosong');

			return FALSE;
		endif;
	}
}

/* End of file RegisterController.php */
/* Location: ./application/controllers/Auth/RegisterController.php */
