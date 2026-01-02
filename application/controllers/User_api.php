<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/ApiController.php';

use chriskacerguis\RestServer\RestController;

class User_api extends ApiController
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

        $this->load->model('User_model');
        $this->load->model('ruangan_model');
        $this->load->model('Role_model');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->config('rest');
    }

    public function all_user_get()
    {

        $users_data = $this->User_model->tampil_data();

       

        foreach ($users_data as $key => $user) {
            $ruangan = $this->ruangan_model->get_ruangan($user['id_user']);
            $users_data[$key]['ruangan'] = $ruangan;
            $role = $this->Role_model->get_multi_role_by_id($user['id_user']);
            $users_data[$key]['role'] = $role;
        }

        // var_dump($users_data);
        // die;

        if ($users_data) {
            $this->response([
                'status' => true,
                'data' => $users_data
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'data' => []
            ], RESTController::HTTP_OK);
        }
    }

    public function add_user_post()
    {
        $data = [
            "id_satker" => $this->post('id_satker'),
            "nip_lama" => $this->post('nip_lama'),
            "nama" => $this->post('nama'),
            "jabatan" => $this->post('jabatan'),
        ];
        $result = $this->User_model->tambah_user($data);
        if ($result) {
            $this->response([
                'status' => 'Berhasil',
                'message' => 'Data Berhasil Ditambahkan'
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => 'Gagal',
                'message' => 'Data Gagal Ditambahkan'
            ], RESTController::HTTP_OK);
        }
    }

    public function update_user_post()
    {
        $id = $this->post('id');
        $data = [
            "id_satker" => $this->post('id_satker'),
            "nip_lama" => $this->post('nip_lama'),
            "nama" => $this->post('nama'),
            "jabatan" => $this->post('jabatan'),
        ];
        $result = $this->User_model->update_user($data, 'user', $id);
        if ($result) {
            $this->response([
                'status' => 'Berhasil',
                'message' => 'Data Berhasil Diupdate'
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => 'Gagal',
                'message' => 'Data Gagal Diupdate'
            ], RESTController::HTTP_OK);
        }
    }

    public function hapus_user_delete($id)
    {
        $result = $this->User_model->hapus_user($id);
        if ($result) {
            $this->response([
                'status' => 'Berhasil',
                'message' => 'Data Berhasil Dihapus'
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => 'Gagal',
                'message' => 'Data Gagal Dihapus'
            ], RESTController::HTTP_OK);
        }
    }

    public function assign_ruangan_post()
    {
        $id_user = $this->post('id_user');
        $id_ruangan = $this->post('id_ruangan'); // bisa array

        if (!is_array($id_ruangan)) {
            $id_ruangan = [$id_ruangan]; // biar tetap bisa handle satuan
        }

        $success = true;
        foreach ($id_ruangan as $ruangan) {
            $data = [
                "id_user" => $id_user,
                "id_ruangan" => $ruangan,
            ];
            $result = $this->User_model->assign_ruangan($data);
            // $this->db->error();
            // var_dump($id_user);
            // die;

            if (!$result) {
                $success = false;
            }
        }

        if ($success) {
            $this->response([
                'status' => 'Berhasil',
                'message' => 'Data Berhasil Ditambahkan'
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status' => 'Gagal',
                'message' => 'Beberapa data gagal ditambahkan'
            ], RESTController::HTTP_OK);
        }
    }

    public function get_roles_get()
    {
        $roles = $this->db->get('mst_role')->result_array();

        if ($roles) {
            return $this->response([
                'status' => true,
                'data' => $roles
            ], RestController::HTTP_OK);
        }

        return $this->response([
            'status' => false,
            'message' => 'Data tidak ditemukan',
            'data' => []
        ], RestController::HTTP_OK);
    }

    public function assign_role_post()
    {
        $id_user = $this->post('id_user');
        $id_role = $this->post('id_role'); // bisa array

        if (empty($id_user) || empty($id_role)) {
            return $this->response([
                'status' => 'Gagal',
                'message' => 'id_user dan id_role wajib diisi'
            ], RestController::HTTP_OK);
        }

        if (!is_array($id_role)) {
            $id_role = [$id_role];
        }

        // Normalisasi dan hilangkan duplikasi
        $id_role = array_values(array_unique(array_map('intval', $id_role)));

        $this->db->trans_begin();

        // Hapus role yang tidak lagi dipilih
        $this->db->where('id_user', $id_user);
        if (!empty($id_role)) {
            $this->db->where_not_in('id_role', $id_role);
        }
        $this->db->delete('user_role');

        // Ambil role yang sudah ada untuk hindari duplikasi insert
        $existing = $this->db->select('id_role')
            ->from('user_role')
            ->where('id_user', $id_user)
            ->get()
            ->result_array();
        $existingRoles = array_map(fn($r) => (int)$r['id_role'], $existing);

        // Tambah role baru yang belum ada
        foreach ($id_role as $role) {
            if (in_array($role, $existingRoles, true)) {
                continue;
            }

            $insert = $this->Role_model->create([
                'id_user' => $id_user,
                'id_role' => $role
            ]);

            if (!$insert) {
                $this->db->trans_rollback();
                return $this->response([
                    'status' => 'Gagal',
                    'message' => 'Gagal menambah role.'
                ], RestController::HTTP_OK);
            }
        }

        $this->db->trans_commit();

        return $this->response([
            'status' => 'Berhasil',
            'message' => 'Role berhasil diperbarui'
        ], RestController::HTTP_OK);
    }
}
