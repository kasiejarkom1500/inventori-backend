<?php
class User_model extends CI_Model
{

	private $table = 'user';
   

	public function create($data)
	{
		return $this->db->insert($this->table, $data);;
	}

	public function tampil_data()
    {
        return $this->db->get('user')->result_array();
    }


	

    
    public function get_user_by_username($username) {
        // Get user's data based on username
        $this->db->select('id_user');
        $this->db->from('user');
        $this->db->where('username', $username);
        $query = $this->db->get();
        return $query->row();
    }

	public function get_user_by_username_all($username) {
        // Get user's data based on username
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('username', $username);
        $query = $this->db->get();
        return $query->row();
    }

    
   

	public function simpan()
    {
        $data = [

            "nama" => $this->input->post('nama'),
            "jabatan" => $this->input->post('jabatan'),
			"ruangan" => $this->input->post('ruangan'),
            "username" => $this->input->post('username'),
            "password" => password_hash(htmlspecialchars($this->input->post('password',TRUE)), PASSWORD_DEFAULT),
            
            
        ];
        $this->session->set_flashData('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Data Berhasil Ditambahkan</strong> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>');
        $this->db->insert('user', $data);
    }

	public function get_id($id)
    {
        return $this->db->get_where('user', ['id_user' => $id])->row_array();
        
    }

	public function ubah()
    {
        $pass = $this->input->post('password');

        $data = [
            "nama" => $this->input->post('nama'),
            "jabatan" => $this->input->post('jabatan'),
			"ruangan" => $this->input->post('ruangan'),
            "username" => $this->input->post('username'),
            
        ];

        if ($pass != null) { //jika input password tidak kosong maka yang disimpan password baru
            $data = [
                "password" => password_hash(htmlspecialchars($this->input->post('password',TRUE)), PASSWORD_DEFAULT),
                "nama" => $this->input->post('nama'),
                "jabatan" => $this->input->post('jabatan'),
			    "ruangan" => $this->input->post('ruangan'),
                "username" => $this->input->post('username'),
                
            ];
        }
        $this->session->set_flashData('message', '<div class="alert alert-primary alert-dismissible fade show" role="alert">
            <strong>Data Berhasil Diupdate</strong> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>');
        $this->db->where('id_user', $this->input->post('id'));
        $this->db->update('user', $data);
    }

	public function hapus($id)
    {
        $this->db->where('id_user', $id);
        $this->db->delete('user');
        $this->session->set_flashData('message', '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Data Berhasil Dihapus</strong> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>');
    }

	public function get_user($username) {
        $this->db->select('*');
		$this->db->from('user');
		$this->db->where('user.username', $username);
		$query = $this->db->get();
		return $query->row();
    }
    
	public function get($id = null)
    
    {
        $this->db->select('*');
        $this->db->from('user');
        if ($id != null) {
            $this->db->where('id_user', $id);
        }
        return $this->db->get();
    }

}
