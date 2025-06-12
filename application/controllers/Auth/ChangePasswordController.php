<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChangePasswordController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//Load Dependencies
		is_logged_in();
        
	}

	// List all your items
	public function index()
	{
		$data['title'] = 'Change Password';

		$this->form_validation->set_rules('current_password','Password Sekarang','trim|required');
		$this->form_validation->set_rules('new_password','Password Baru','trim|required|min_length[6]|max_length[15]|matches[confirm_password]');
		$this->form_validation->set_rules('confirm_password','Konfirmasi Password Baru','trim|required|min_length[6]|max_length[15]|matches[new_password]');
		$this->form_validation->set_rules('confirmation_password','Konfirmasi','required');

		if ($this->form_validation->run() == FALSE) {
			$this->load->view('change_password', $data);
        } else {
            $current_password = htmlspecialchars($this->input->post('current_password', TRUE));
            $new_password = htmlspecialchars($this->input->post('new_password', TRUE));

            $this->change_password($current_password, $new_password);
        }
    }

	public function change_password($current_password, $new_password)
    {
        $user = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row_array();
        $admin = $this->db->get_where('admin', ['username' => $this->session->userdata('username')])->row_array();

        if ($user) {
            if (password_verify($current_password, $user['password'])) {
                $params = ['password' => password_hash($new_password, PASSWORD_DEFAULT)];
                $this->db->update('user', $params, ['username' => $user['username']]);
                $this->session->set_flashdata('msg', '<div class="alert alert-success" role="alert">Password successfully changed!</div>');
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger" role="alert">Current password is incorrect.</div>');
            }
        } elseif ($admin) {
            if (password_verify($current_password, $admin['password'])) {
                $params = ['password' => password_hash($new_password, PASSWORD_DEFAULT)];
                $this->db->update('admin', $params, ['username' => $admin['username']]);
                $this->session->set_flashdata('msg', '<div class="alert alert-success" role="alert">Password successfully changed!</div>');
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger" role="alert">Current password is incorrect.</div>');
            }
        } else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger" role="alert">User not found.</div>');
        }

        redirect('Auth/ChangePasswordController');
    }
}

/* End of file ChangePasswordController.php */
/* Location: ./application/controllers/Auth/ChangePasswordController.php */
