<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/ApiController.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use chriskacerguis\RestServer\RestController;

class KartuKendali extends ApiController
{

    public function __construct()
    {
        parent::__construct();

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE,PUT');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            header('Access-Control-Max-Age: 86400'); // Cache preflight response for 24 hours
            exit(0); // Hentikan eksekusi lebih lanjut
        }


        // Load necessary libraries and helpers
        $this->load->helper(['url', 'file']);
        $this->load->model('Permintaan_barang_model');
        $this->load->model('Barang_model');
        $this->load->model('User_model');
        $this->load->model('ruangan_model');
    }

    public function generate_post()
    {
        // Capture POST data (e.g., details for the PDF)
        // $data = $this->input->post();

        $tgl_mulai = $this->post('tgl_mulai');
        $tgl_selesai = $this->post('tgl_selesai');


        $data_keluar = $this->Permintaan_barang_model->karken_data($tgl_mulai, $tgl_selesai, $this->decoded_token['id_satker']);

        // Proses data untuk dikelompokkan
        $groupedData = [];
        foreach ($data_keluar as $item) {
            // Cari apakah sudah ada grup dengan id_barang yang sama
            $foundIndex = null;
            foreach ($groupedData as $index => $group) {
                if ($group["id_barang"] === $item["id_barang"]) {
                    $foundIndex = $index;
                    break;
                }
            }

            if ($foundIndex === null) {
                // Jika belum ada grup, tambahkan grup baru
                $groupedData[] = [
                    "id_barang" => $item["id_barang"],
                    "nama_barang" => $item["nama_barang"],
                    "kd_barang" => $item["kd_barang"],
                    "satuan" => $item["satuan"],
                    "kategori" => $item["kategori"],
                    "rows" => []
                ];
                $foundIndex = count($groupedData) - 1; // Index grup baru
            }

            // Tambahkan data barang ke grup yang ditemukan
            $groupedData[$foundIndex]["rows"][] = [
                "tanggal" => $item["tanggal"],
                "masuk" => $item["jumlah_masuk"],
                "keluar" => $item["jumlah_keluar"],
                "faktur" => $item["faktur"],
                "uraian" => $item["uraian"],
                "current_stok" => $item["current_stok"]
            ];
        }

        // Return the file as a response
        return $this->response([
            'status' => true,
            'message' => 'PDF generated successfully',
            'download_url' => $groupedData
        ], RestController::HTTP_OK);


        $url = [];
        foreach ($groupedData as $pdfData) {
            $pdfData['logo'] = base_url('/img/logo.png');

            $html = $this->load->view('kartu_kendali', $pdfData, true);

            // Aktifkan remote image
            $options = new Options();
            $options->set('isRemoteEnabled', true);

            // Inisialisasi Dompdf dengan opsi
            $dompdf = new Dompdf($options);

            // $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Save the PDF temporarily
            $output = $dompdf->output();
            $name = 'downloads/Kartu_Kendali_' . $pdfData['id_barang'] . '.pdf';
            $filePath = FCPATH . $name;
            file_put_contents($filePath, $output);

            $url[] = base_url($name);
        }



        // Return the file as a response
        return $this->response([
            'status' => true,
            'message' => 'PDF generated successfully',
            'download_url' => $url
        ], RestController::HTTP_OK);
    }


    public function summary_post()
    {
        // Capture POST data (e.g., details for the PDF)
        // $data = $this->input->post();

        $tgl_mulai = $this->post('tgl_mulai');
        $tgl_selesai = $this->post('tgl_selesai');

        $data_pengajuan = $this->Permintaan_barang_model->summary_karken($tgl_mulai, $tgl_selesai, $this->decoded_token['id_satker']);

        if (!$data_pengajuan) {
            return $this->response([
                'status' => false,
                'message' => 'Gagal mendapatkan data'
            ], RestController::HTTP_OK);
        } else {
            // Return the file as a response
            return $this->response([
                'status' => true,
                'message' => 'Berhasil mendapatkan data',
                'data' => $data_pengajuan
            ], RestController::HTTP_OK);
        }
    }
}
