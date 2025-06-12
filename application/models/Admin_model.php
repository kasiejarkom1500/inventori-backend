<?php
class Admin_model extends CI_Model
{

	public function tampil_data()
	{
		return $this->db->get('admin')->result_array();
	}

	public function insert_pegawai($data)
	{
		$this->db->insert('user', $data);
	}

	public function get_all_ruangan()
	{
		return $this->db->get('admin_ruangan')->result();
	}

	public function insert_ruangan($data)
	{
		$this->db->insert('admin_ruangan', $data);
	}

	public function tampil_data1()
	{
		return $this->db->get('admin_pengajuan')->result_array();
	}

	public function tampil_data2()
	{
		return $this->db->get('admin_ruangan')->result_array();
	}

	public function simpan()
	{
		$data = [

			"nama" => $this->input->post('nama'),
			"email" => $this->input->post('email'),
			"username" => $this->input->post('username'),
			"password" => password_hash(htmlspecialchars($this->input->post('password', TRUE)), PASSWORD_DEFAULT),


		];
		$this->session->set_flashData('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Data Berhasil Ditambahkan</strong> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>');
		$this->db->insert('admin', $data);
	}

	public function simpan_pengajuan()
	{
		$data = [

			"nama" => $this->input->post('nama'),
			"jabatan" => $this->input->post('jabatan'),
			"ruangan" => $this->input->post('ruangan'),
			"username" => $this->input->post('username'),
			"password" => password_hash(htmlspecialchars($this->input->post('password', TRUE)), PASSWORD_DEFAULT),


		];
		$this->session->set_flashData('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Data Berhasil Ditambahkan</strong> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>');
		$this->db->insert('admin_pengajuan', $data);
	}

	public function simpan_ruangan()
	{
		$data = [

			"ruangan" => $this->input->post('ruangan'),
			"penanggungjawab" => $this->input->post('penanggungjawab'),
			"jabatan" => $this->input->post('jabatan'),
		];
		$this->session->set_flashData('message', '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Data Berhasil Ditambahkan</strong> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>');
		$this->db->insert('admin_ruangan', $data);
	}

	public function hapus($id)
	{
		$this->db->where('id_admin', $id);
		$this->db->delete('admin');
		$this->session->set_flashData('message', '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Data Berhasil Dihapus</strong> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>');
	}

	public function hapus1($id)
	{
		$this->db->where('id_pengajuan', $id);
		$this->db->delete('admin_pengajuan');
		$this->session->set_flashData('message', '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Data Berhasil Dihapus</strong> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>');
	}

	public function hapus2($id)
	{
		$this->db->where('id_ruangan', $id);
		$this->db->delete('admin_ruangan');
		$this->session->set_flashData('message', '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Data Berhasil Dihapus</strong> 
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>');
	}


	public function get_id($id)
	{
		return $this->db->get_where('admin', ['id_admin' => $id])->row_array();
	}

	public function get_id1($id)
	{
		return $this->db->get_where('admin_pengajuan', ['id_pengajuan' => $id])->row_array();
	}

	public function get_id2($id)
	{
		return $this->db->get_where('admin_ruangan', ['id_ruangan' => $id])->row_array();
	}


	public function usernamecek($username, $id)
	{
		$this->db->where('username =', $username);
		$this->db->where('id_admin !=', $id);
		$cek = $this->db->get('admin')->num_rows();
		return $cek;
	}


	public function ubah()
	{
		$pass = $this->input->post('password');

		$data = [
			"nama" => $this->input->post('nama'),
			"email" => $this->input->post('email'),
			"username" => $this->input->post('username'),

		];

		if ($pass != null) { //jika input password tidak kosong maka yang disimpan password baru
			$data = [
				"password" => password_hash(htmlspecialchars($this->input->post('password', TRUE)), PASSWORD_DEFAULT),
				"nama" => $this->input->post('nama'),
				"email" => $this->input->post('email'),
				"username" => $this->input->post('username'),

			];
		}
		$this->session->set_flashData('message', '<div class="alert alert-primary alert-dismissible fade show" role="alert">
            <strong>Data Berhasil Diupdate</strong> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>');
		$this->db->where('id_admin', $this->input->post('id'));
		$this->db->update('admin', $data);
	}

	public function ubah_pengajuan()
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
				"password" => password_hash(htmlspecialchars($this->input->post('password', TRUE)), PASSWORD_DEFAULT),
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
		$this->db->where('id_pengajuan', $this->input->post('id'));
		$this->db->update('admin_pengajuan', $data);
	}

	public function ubah_ruangan()
	{
		$data = [
			"ruangan" => $this->input->post('ruangan'),
			"penanggungjawab" => $this->input->post('penanggungjawab'),
			"jabatan" => $this->input->post('jabatan'),
		];

		$this->session->set_flashData('message', '<div class="alert alert-primary alert-dismissible fade show" role="alert">
            <strong>Data Berhasil Diupdate</strong> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>');
		$this->db->where('id_ruangan', $this->input->post('id'));
		$this->db->update('admin_ruangan', $data);
	}

	public function login($post)
	{
		$this->db->select('*');
		$this->db->from('admin');
		$this->db->where('username', $post['user_name']);
		$this->db->where('password', sha1($post['pass']));
		return $this->db->get();
	}

	public function get($id = null)
	//membuat 1 fungsi untuk menampilkan semua data
	//dan menampilkan data per id/satu data
	{
		$this->db->select('*');
		$this->db->from('admin');
		if ($id != null) {
			$this->db->where('id_admin', $id);
		}
		return $this->db->get();
	}

	public function getByUsername($username)
	{
		$this->db->select('*');
		$this->db->from('admin');
		$this->db->where('username', $username);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			return $query->row();  // Return a single row
		} else {
			return null;  // Or handle the case where no record is found
		}
	}

	public function getByUsernameAdmin2($username)
	{
		$this->db->select('*');
		$this->db->from('admin_pengajuan');
		$this->db->where('username', $username);
		$query = $this->db->get();

		if ($query->num_rows() == 1) {
			return $query->row();  // Return a single row
		} else {
			return null;  // Or handle the case where no record is found
		}
	}

	public function get1($id = null)
	//membuat 1 fungsi untuk menampilkan semua data
	//dan menampilkan data per id/satu data
	{
		$this->db->select('*');
		$this->db->from('admin_pengajuan');
		if ($id != null) {
			$this->db->where('id_pengajuan', $id);
		}
		return $this->db->get();
	}

	public function get2($id = null)
	//membuat 1 fungsi untuk menampilkan semua data
	//dan menampilkan data per id/satu data
	{
		$this->db->select('*');
		$this->db->from('admin_ruangan');
		if ($id != null) {
			$this->db->where('id_ruangan', $id);
		}
		return $this->db->get();
	}
}
