<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class LoginController_api extends RestController
{
    public function __construct()
    {
        parent::__construct();

        // Allow CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Preflight request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            header('Access-Control-Max-Age: 86400'); // Cache preflight response for 24 hours
            exit(0);
        }

        // Load dependencies
        $this->load->model('User_model');
        $this->load->model('Role_model');
        $this->load->model('Ruangan_model');

        $this->load->helper(['form', 'url', 'jwt']);
        $this->load->config('rest');
    }

    /* ==============================
       AUTH LOGIN API
    ============================== */

    // login endpoint (pakai JWT)
    public function login_post()
    {
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            return $this->response([
                'status' => FALSE,
                'message' => validation_errors()
            ], RestController::HTTP_BAD_REQUEST);
        }

        $username = htmlspecialchars($this->post('username', TRUE));
        $password = htmlspecialchars($this->post('password', TRUE));

        $user = $this->cek_login($username, $password);
        // $user = $this->dummy_cek_login($username, $password);

        if ($user) {
            $token = generate_jwt($user);
            return $this->response([
                'status' => TRUE,
                'message' => 'Login successful',
                'token' => $token,
                'data' => $user
            ], RestController::HTTP_OK);
        }

        return $this->response([
            'status' => FALSE,
            'message' => 'Invalid username or password'
        ], RestController::HTTP_UNAUTHORIZED);
    }

    /* ==============================
       PRIVATE LOGIN HELPERS
    ============================== */
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
        

        if (!$user) return false;

        $role = $this->Role_model->get_role_by_id($user['id_user']);


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
            'nama' => $data_user->fullname,
            'username' => $data_user->username,
            'jabatan' => $user['jabatan'],
            'id_user' => $user['id_user'],
            'id_satker' => $user['id_satker'],
            'data_ruangan' => $data_ruangan,
            'role' => $role_map[$role_id]
        ];
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
        } elseif ($username == 'dummy-pengadaan' && $password == 'dummy-pengadaan') {
            $user = $this->db->get_where('user', ['nip_lama' => '340060087'])->row_array();
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

}