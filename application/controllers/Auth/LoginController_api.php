<?php
defined('BASEPATH') or exit('No direct script access allowed');


use chriskacerguis\RestServer\RestController;

class LoginController_api extends RestController
{

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
        $this->load->model('Ruangan_model');


        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('jwt');
        $this->load->config('rest');
    }

    public function login_post()
    {
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
            // $user = $this->cek_login($username, $password);
            $user = $this->dummy_cek_login($username, $password);


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

    public function newlogin_post()
    {
        // Validasi input

        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        

        if ($this->form_validation->run() == FALSE) {
            $this->response([
                'status' => FALSE,
                'message' => validation_errors()
            ], RestController::HTTP_BAD_REQUEST);
        } else {
            $username = htmlspecialchars($this->post('username', TRUE));
            $password = htmlspecialchars($this->post('password', TRUE));

            // $user = $this->cek_login($username, $password);
            $user = $this->dummy_cek_login($username, $password);

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

    /**
     * Attempts to authenticate a user via SSO and returns user data on success.
     *
     * @param string $username
     * @param string $password
     * @return array|false Returns an associative array with keys:
     *   - nama: string
     *   - username: string
     *   - jabatan: string
     *   - id_user: int
     *   - id_satker: int
     *   - data_ruangan: array|null
     *   - role: string
     *   Or false on failure.
     */
    private function cek_login($username, $password)
    {
        // $sso_url = 'https://bpsjambi.id/sso/public/index.php/';
        $sso_url = 'https://bpsjambi.web.bps.go.id/home/public/index.php/';
        $url = $sso_url . 'auth/login';
        $data = [
            'username' => $username,
            'password' => $password
        ];
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
                'timeout' => 10
            ]
        ];
        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $response = json_decode($response);

        // echo ($response->token);
        // die;

        if ($response->status == 200 || isset($response->token)) {

            $url_validation = $sso_url . 'auth/validate-token';
            $data = [
                'token' => $response->token,
                'secret_key' => "rahasianegara!"
            ];
            $data = [
                'token' => $response->token,
                'secret_key' => "rahasianegara!"
            ];
            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                    'timeout' => 10
                ]
            ];
            $context  = stream_context_create($options);
            $response_validation = file_get_contents($url_validation, false, $context);
            if ($response_validation === false) {

                return false;
            }
            $response_validation = json_decode($response_validation);


            $data_user = $response_validation->user ?? null;

            if (!is_object($response_validation) || !isset($response_validation->user)) {
                return false;
            }

            if (is_null($data_user) || empty($data_user)) {
                return false;
            }

            $url_pegawai = $sso_url . 'pegawai/by-niplama';
            $data = [
                'token' => $response->token,
                'secret_key' => "rahasianegara!",
                'nip_lama' => $data_user->nip_lama_user
            ];
            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                    'timeout' => 10
                ]
            ];
            $context  = stream_context_create($options);
            $response_pegawai = file_get_contents($url_pegawai, false, $context);
            $response_pegawai = json_decode($response_pegawai);

            if (!is_object($response_pegawai) || !isset($response_pegawai->data)) {
                return false;
            } else {
                $data_pegawai = $response_pegawai->data;
            }
        }

        
        $user = $this->db->get_where('user', ['nip_lama' => $data_user->nip_lama_user])->row_array();
        

       


        // var_dump($user);
        // die;

        if (!$user) return false;

        $role = $this->Role_model->get_role_by_id($user['id_user']);
        // var_dump($user['id_user']);
        // die;

        if (!$role) return $this->response([
            'status' => 'Gagal',
            'message' => 'Role tidak ditemukan silahkan hubungi admin'
        ], RestController::HTTP_UNAUTHORIZED);

        $data_ruangan = null;
        if ($role->id_role == '4' || $role->id_role == '6' || $role->id_role == '0' || $role->id_role == '7' || $role->id_role == '1') {
            $data_ruangan = [];
            $ruangan = $this->Ruangan_model->get_ruangan($user['id_user']);
            //jika ruangan kosong maka tidak perlu diisi
            if ($ruangan) {
                foreach ($ruangan as $item) {
                    $data_ruangan[] = [
                        'id_ruangan' => $item['id_ruangan'],
                        'ruangan' => $item['ruangan']
                    ];
                }
            }
        }

        $role_map = [
            0 => 'admin',
            1 => 'admin_gudang',
            2 => 'admin_pengadaan',
            3 => 'admin_pembelian',
            4 => 'user',
            5 => 'viewer',
            6 => 'ketua_tim',
            7 => 'ketua_gudang'
        ];

        $role_id = (int)$role->id_role;
        if (!isset($role_map[$role_id])) return false;

        return [
            // 'nama' => $data_user->fullname,
            // 'username' => $data_user->username,
            'jabatan' => $user['jabatan'],
            'id_user' => $user['id_user'],
            'id_satker' => $user['id_satker'],
            'data_ruangan' => $data_ruangan,
            'role' => $role_map[$role_id]
        ];;
    }

    private function dummy_cek_login($username, $password)
    {
        if($username == 'dummy-operator' && $password =='dummy-operator') {
            $user = $this->db->get_where('user', ['nip_lama' => '340060082'])->row_array();
        } elseif ($username == 'dummy-ketuatim' && $password == 'dummy-ketuatim') {
            $user = $this->db->get_where('user', ['nip_lama' => '340060083'])->row_array();
        } elseif ($username =='dummy-admingudang' && $password == 'dummy-admingudang') {
            $user = $this->db->get_where('user', ['nip_lama' => '340060084'])->row_array();
        } elseif ($username == 'dummy-ketuagudang' && $password == 'dummy-ketuagudang') {
            $user = $this->db->get_where('user', ['nip_lama' => '340060085'])->row_array();
        } elseif ($username == 'dummy-admin' && $password == 'dummy-admin') {
            $user = $this->db->get_where('user', ['nip_lama' => '340060086'])->row_array();
        }


       


        // var_dump($user);
        // die;

        if (!$user) return false;

        $role = $this->Role_model->get_role_by_id($user['id_user']);
        // var_dump($user['id_user']);
        // die;

        if (!$role) return $this->response([
            'status' => 'Gagal',
            'message' => 'Role tidak ditemukan silahkan hubungi admin'
        ], RestController::HTTP_UNAUTHORIZED);

        $data_ruangan = null;
        if ($role->id_role == '4' || $role->id_role == '6' || $role->id_role == '0' || $role->id_role == '7' || $role->id_role == '1') {
            $data_ruangan = [];
            $ruangan = $this->Ruangan_model->get_ruangan($user['id_user']);
            //jika ruangan kosong maka tidak perlu diisi
            if ($ruangan) {
                foreach ($ruangan as $item) {
                    $data_ruangan[] = [
                        'id_ruangan' => $item['id_ruangan'],
                        'ruangan' => $item['ruangan']
                    ];
                }
            }
        }

        $role_map = [
            0 => 'admin',
            1 => 'admin_gudang',
            2 => 'admin_pengadaan',
            3 => 'admin_pembelian',
            4 => 'user',
            5 => 'viewer',
            6 => 'ketua_tim',
            7 => 'ketua_gudang'
        ];

        $role_id = (int)$role->id_role;
        if (!isset($role_map[$role_id])) return false;

        return [
            'nama' => $user['nama'],
            'username' => $user['nama'],
            'jabatan' => $user['jabatan'],
            'id_user' => $user['id_user'],
            'id_satker' => $user['id_satker'],
            'data_ruangan' => $data_ruangan,
            'role' => $role_map[$role_id]
        ];;
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
