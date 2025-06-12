<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    public $db;
	public $Pengajuan_barang_model;

    public function __construct(){
        parent::__construct();
        is_logged_in();
		$this->load->model('Pengajuan_barang_model');
    }


        public function index() {
			
			$data['monthly_totals'] = $this->Pengajuan_barang_model->get_total_per_month();
			$this->load->view('templates/header', $data);
			$this->load->view('home/index', $data);
			$this->load->view('templates/footer', $data);
			
        }

		public function home_user() {
			$data['pengajuan_pertanggal'] = $this->get_pengajuan_per_hari_2_minggu_terakhir();
			$data['pengajuan_perbulan'] = $this->get_pengajuan_per_bulan_tahun_ini();
			
            
            $this->load->view('templates/header1', $data);
            $this->load->view('user/home/index', $data);
            $this->load->view('templates/footer', $data);
        }

		public function home_admin_pengajuan()
	{


		$data['pengajuan_pertanggal'] = $this->get_pengajuan_per_hari_2_minggu_terakhir();
		$data['pengajuan_perbulan'] = $this->get_pengajuan_per_bulan_tahun_ini();

		$this->load->view('templates/header2');
		$this->load->view('admin_pengajuan/home/index', $data);
		$this->load->view('templates/footer');
	}

	public function get_pengajuan_per_hari_2_minggu_terakhir()
	{
		$date_two_weeks_ago = date('Y-m-d', strtotime('-2 weeks'));

		// Ambil data pengajuan selama 2 minggu terakhir
		$this->db->select('DATE(tanggal) as tanggal, COUNT(id_pengajuan) as total_pengajuan');
		$this->db->from('pengajuan');
		$this->db->where('tanggal >=', $date_two_weeks_ago);
		$this->db->group_by('DATE(tanggal)');
		$this->db->order_by('DATE(tanggal)', 'ASC');
		$query = $this->db->get();

		$result = $query->result_array();

		// Format data untuk Chart.js
		$data = [];
		$start_date = new DateTime($date_two_weeks_ago);
		$end_date = new DateTime();

		while ($start_date <= $end_date) {
			$formatted_date = $start_date->format('Y-m-d');
			$data[$formatted_date] = 0; // Initialize with 0
			$start_date->modify('+1 day');
		}

		foreach ($result as $row) {
			$data[$row['tanggal']] = (int) $row['total_pengajuan'];
		}
		return $data;
	}
	public function get_pengajuan_per_bulan_tahun_ini()
	{
		$current_year = date('Y');

		// Ambil data pengajuan per bulan selama tahun ini
		$this->db->select('DATE_FORMAT(tanggal, "%Y-%m") as bulan, COUNT(id_pengajuan) as total_pengajuan');
		$this->db->from('pengajuan');
		$this->db->where('YEAR(tanggal)', $current_year);
		$this->db->group_by('bulan');
		$this->db->order_by('bulan', 'ASC');
		$query = $this->db->get();

		$result = $query->result_array();

		// Format data untuk Chart.js
		$data = [];

		// Pemetaan nama bulan dalam bahasa Indonesia
		$bulan_map = [
			"01" => "Januari",
			"02" => "Februari",
			"03" => "Maret",
			"04" => "April",
			"05" => "Mei",
			"06" => "Juni",
			"07" => "Juli",
			"08" => "Agustus",
			"09" => "September",
			"10" => "Oktober",
			"11" => "November",
			"12" => "Desember"
		];

		// Inisialisasi array dengan bulan dari Januari hingga Desember
		foreach ($bulan_map as $num => $nama) {
			$data[$nama] = 0;
		}

		foreach ($result as $row) {
			// Ekstrak bulan dari format 'YYYY-MM'
			$bulan_num = substr($row['bulan'], 5, 2);
			$nama_bulan = $bulan_map[$bulan_num];
			$data[$nama_bulan] = (int) $row['total_pengajuan'];
		}
		return $data;
	}
}
