<?php
defined('BASEPATH') OR exit('No direct script access allowed');


use chriskacerguis\RestServer\RestController;

class LoginController_api extends RestController {

    public function __construct()
    {
        parent::__construct();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE,PUT');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            header('Access-Control-Max-Age: 86400'); // Cache preflight response for 24 hours
            exit(0); // Hentikan eksekusi lebih lanjut
        }

        
        // Load Dependencies
        $this->load->model('User_model');
        $this->load->model('Role_model');
    
        
        $this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('jwt');
        $this->load->config('rest');
    }
    
    public function login_post() {
        // Validasi input
        $this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_numeric_spaces');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|alpha_numeric_spaces');

        if ($this->form_validation->run() == FALSE) {
            // Kirim response jika validasi gagal
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
            ], RestController::HTTP_BAD_REQUEST);
        } else {
            // Ambil input username dan password
            $username = htmlspecialchars($this->post('username', TRUE));
            $password = htmlspecialchars($this->post('password', TRUE));

            // Periksa login menggunakan model
            $user = $this->cek_login($username, $password);
            
            
            if ($user) {
                // Login berhasil, kirim data user
                $this->response([
                    'status' => TRUE,
                    'message' => 'Login successful',
                    'data' => $user
                ], RestController::HTTP_OK);
            } else {
                // Login gagal
                $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid username or password'
                ], RestController::HTTP_UNAUTHORIZED);
            }
        }
    }
    
    public function newlogin_post() {
        // Validasi input
       
        $this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_numeric_spaces');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|alpha_numeric_spaces');
    
        if ($this->form_validation->run() == FALSE) {
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
            ], RestController::HTTP_BAD_REQUEST);
        } else {
            $username = htmlspecialchars($this->post('username', TRUE));
            $password = htmlspecialchars($this->post('password', TRUE));
    
            $user = $this->cek_login($username, $password);
    
            if ($user) {
                $token = generate_jwt($user);
    
                $this->response([
                    'status' => TRUE,
                    'message' => 'Login successful',
                    'token' => $token,
                    'data' => $user // detail user bisa dikirim juga
                ], RestController::HTTP_OK);
            } else {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid username or password'
                ], RestController::HTTP_UNAUTHORIZED);
            }
        }

    }

    // private function cek_login($username, $password)
    // {
    //     $user = $this->db->get_where('user', ['username' => $username])->row_array();
    //     $admin = $this->db->get_where('admin', ['username' => $username])->row_array();
    //     $admin_pengajuan = $this->db->get_where('admin_pengajuan', ['username' => $username])->row_array();
        
    //     $user = $this->db->get_where('user', ['username' => $username])->row_array();
    //     $role = $this->Role_model->get_role_by_id($user['id_user']);
        
    
    //     if ($role->id_role == '4') {
    //         if (password_verify($password, $user['password'])) {
                
    //             $data_ruangan = [];
                
    //             $ruangan = $this->db->get_where('admin_ruangan', ['id_user' => $user['id_user']])->result_array();
                
    //             $role = $this->Role_model->get_role_by_id($user['id_user']);
                
    //             // var_dump($role);
    //             // die();
                
    //             foreach ($ruangan as $item) {
    //                 $data = [
    //                     'id_ruangan' => $item['id_ruangan'],
    //                     'ruangan' => $item['ruangan']
    //                 ];
                    
    //                 array_push($data_ruangan,$data);
    //             }
                
    //             $session = [
    //                 'nama' => $user['nama'],
    //                 'username' => $user['username'],
    //                 'jabatan' => $user['jabatan'],
    //                 'id_user' => $user['id_user'],
    //                 'id_satker' =>  $user['id_satker'],
    //                 'data_ruangan' => $data_ruangan,
    //                 'role' => 'user' // Menambahkan role user
    //             ];
    //             $this->session->set_userdata($session);
    //             $this->session->set_flashdata('msg', '<div class="alert alert-primary" role="alert">Login berhasil!</div>');
    //             return $session;
    //         }
    //     } elseif ($role->id_role == '0') {
    //         if (password_verify($password, $user['password'])) {
    //             $session = [
    //                 'nama' => $user['nama'],
    //                 'username' => $user['username'],
    //                 'data_ruangan' => [],
    //                 'id_satker' =>  $user['id_satker'],
    //                 'role' => 'admin' // Menambahkan role admin
    //             ];
    //             $this->session->set_userdata($session);
    //             $this->session->set_flashdata('msg', '<div class="alert alert-primary" role="alert">Login berhasil!</div>');
    //             return $session;
    //         }
    //     } elseif ($role->id_role == '1') {
    //         if (password_verify($password, $user['password'])) {
    //             $session = [
    //                 'nama' => $user['nama'],
    //                 'username' => $user['username'],
    //                 'jabatan' => $user['jabatan'],
    //                 'id_satker' =>  $user['id_satker'],
    //                 'data_ruangan' => [],
    //                 'role' => 'admin_pengajuan' // Menambahkan role admin_pengajuan
    //             ];
    //             $this->session->set_userdata($session);
    //             $this->session->set_flashdata('msg', '<div class="alert alert-primary" role="alert">Login berhasil!</div>');
    //             return $session;
    //         }
    //     } elseif ($role->id_role == '3') {
    //         if (password_verify($password, $user['password'])) {
    //             $session = [
    //                 'nama' => $user['nama'],
    //                 'username' => $user['username'],
    //                 'jabatan' => $user['jabatan'],
    //                 'id_satker' =>  $user['id_satker'],
    //                 'data_ruangan' => [],
    //                 'role' => 'admin_pembelian' // Menambahkan role admin_pengajuan
    //             ];
    //             $this->session->set_userdata($session);
    //             $this->session->set_flashdata('msg', '<div class="alert alert-primary" role="alert">Login berhasil!</div>');
    //             return $session;
    //         }
    //     }
    //         else {
    //             $this->session->set_flashdata('msg', '<div class="alert alert-danger" role="alert">Username atau Password salah!</div>');
    //     }
    // }
    
    private function cek_login($username, $password)
    {
        $user = $this->db->get_where('user', ['username' => $username])->row_array();
        if (!$user) return false;
    
        $role = $this->Role_model->get_role_by_id($user['id_user']);
        if (!$role) return false;
    
        if (!password_verify($password, $user['password'])) return false;
    
        $data_ruangan = [];
        if ($role->id_role == '4') {
            // Ambil data ruangan hanya untuk role user
            $ruangan = $this->db->get_where('admin_ruangan', ['id_user' => $user['id_user']])->result_array();
            foreach ($ruangan as $item) {
                $data_ruangan[] = [
                    'id_ruangan' => $item['id_ruangan'],
                    'ruangan' => $item['ruangan']
                ];
            }
        }
    
        $role_map = [
            '0' => 'admin',
            '1' => 'admin_pengajuan',
            '3' => 'admin_pembelian',
            '4' => 'user'
        ];
    
        // Role tidak dikenali
        if (!isset($role_map[$role->id_role])) return false;
    
        // Data yang akan dikirim via JWT
        return [
            'nama' => $user['nama'],
            'username' => $user['username'],
            'jabatan' => $user['jabatan'],
            'id_user' => $user['id_user'],
            'id_satker' => $user['id_satker'],
            'data_ruangan' => $data_ruangan,
            'role' => $role_map[$role->id_role]
        ];
    }


    public function forgotPassword()
    {
        $username = htmlspecialchars($this->input->post('username', TRUE));
        $admin = $this->db->get_where('admin', ['username' => $username])->row_array();

        if ($admin) {
            $token = bin2hex(random_bytes(32));
            $this->db->set('reset_token', $token);
            $this->db->set('token_created_at', date('Y-m-d H:i:s'));
            $this->db->where('username', $username);
            $this->db->update('admin');

            $reset_link = base_url('Auth/LoginController/resetPassword/' . $token);
            $this->session->set_flashdata('msg', '<div class="alert alert-success" role="alert">Please click <a href="' . $reset_link . '">this link</a> to reset your password.</div>');
        } else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger" role="alert">Username not found.</div>');
        }

        return redirect('Auth/LoginController');
    }

    public function resetPassword($token = NULL)
    {
        if (!$token) {
            show_404();
        }

        $admin = $this->db->get_where('admin', ['reset_token' => $token])->row_array();

        if (!$admin) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger" role="alert">Invalid password reset token.</div>');
            return redirect('Auth/LoginController');
        }

        // Redirect to ChangePasswordController with token
        redirect('Auth/ChangePasswordController/index/' . $token);
    }


    public function update_password()
{
    $username = $this->input->post('username');
    $password = $this->input->post('password');
    $password_confirm = $this->input->post('password_confirm');

    $this->form_validation->set_rules('username', 'Username', 'required');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
    $this->form_validation->set_rules('password_confirm', 'Konfirmasi Password', 'required|matches[password]');

    if ($this->form_validation->run() == FALSE) {
        $this->load->view('reset_password_form', ['username' => $username]);
    } else {
        // Cari username di tabel user
        $query_user = $this->db->get_where('user', ['username' => $username]);
        if ($query_user && $query_user->num_rows() > 0) {
            $user = $query_user->row_array();
        } else {
            $user = null;
        }

        // Jika tidak ditemukan di tabel user, cari di tabel admin
        if (!$user) {
            $query_admin = $this->db->get_where('admin', ['username' => $username]);
            if ($query_admin && $query_admin->num_rows() > 0) {
                $admin = $query_admin->row_array();
            } else {
                $admin = null;
            }
        } else {
            $admin = null;
        }

        if (!$user && !$admin) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger" role="alert">Username tidak ditemukan.</div>');
            return redirect('Auth/LoginController');
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($user) {
            $this->db->set('password', $hashed_password);
            $this->db->where('username', $user['username']);
            $this->db->update('user');
        } else {
            $this->db->set('password', $hashed_password);
            $this->db->where('username', $admin['username']);
            $this->db->update('admin');
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success" role="alert">Password berhasil direset. Silakan login dengan password baru Anda.</div>');
        return redirect('Auth/LoginController');
    }
}
}
?>
