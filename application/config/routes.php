<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| URI ROUTING
|--------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method.
|
| example.com/class/method/id/
|
| Reserved routes:
|   $route['default_controller'] = 'welcome';
|   $route['404_override'] = 'errors/page_missing';
|   $route['translate_uri_dashes'] = FALSE;
|
| For complete details, see:
| https://codeigniter.com/userguide3/general/routing.html
|--------------------------------------------------------------------------
*/

$route['default_controller']    = 'welcome';
$route['404_override']          = '';
$route['translate_uri_dashes']  = FALSE;

/* =======================
   AUTH
======================= */
$route['api/login']              = 'Auth/LoginController_api/login';

/* =======================
   BARANG
======================= */
$route['api/get_barang_atk']         = 'Barang_api/all_barang';
$route['api/get_barang_ark']         = 'Barang_api/all_barang_ark';
$route['api/get_barang_all']         = 'Barang_api/barang';
$route['api/get_barang_id/(:num)']   = 'Barang_api/barang_id/$1';

$route['api/insert_barang']          = 'Barang_api/insert_barang';
$route['api/hapus_barang/(:num)']    = 'Barang_api/hapus_barang/$1';
$route['api/ubah_barang/(:num)']     = 'Barang_api/ubah/$1';

$route['api/import_excel']           = 'Barang_api/import_excel';
$route['api/update_harga_pengajuan'] = 'Barang_api/update_harga_pengajuan';
$route['api/get_subkategori']        = 'Barang_api/subkategori';
$route['api/get_baso']               = 'Barang_api/barang_BASO';

/* =======================
   PERMINTAAN BARANG
======================= */
$route['api/get_transaksi_permintaan']                     = 'Permintaan_barang_api/transaksi_permintaan';
$route['api/get_detail_permintaan/(:num)/(:any)/(:num)']   = 'Permintaan_barang_api/permintaan_detail/$1/$2/$3';
$route['api/cetak_permintaan/(:num)/(:any)']               = 'Permintaan_barang_api/cetak_permintaan/$1/$2';
$route['api/update_status']                                = 'Permintaan_barang_api/update_status_admin';
$route['api/tambah_permintaan']                            = 'Permintaan_barang_api/simpan';
$route['api/approve_transaksi/(:num)/(:num)']              = 'Permintaan_barang_api/approve_transaksi/$1/$2'; //stok berkurang
$route['api/approve_gudang/(:num)/(:num)']                 = 'Permintaan_barang_api/approve_gudang/$1/$2';

/* =======================
   PENGAJUAN BARANG
======================= */
$route['api/get_transaksi_pengajuan']             = 'Pengajuan_barang_api/transaksi_pengajuan';
$route['api/get_detail_pengajuan/(:num)/(:any)']  = 'Pengajuan_barang_api/pengajuan_detail/$1/$2';
$route['api/update_status_pengajuan']             = 'Pengajuan_barang_api/update_status';
$route['api/tambah_pengajuan']                    = 'Pengajuan_barang_api/simpan';

$route['api/filter_pengajuan'] = 'Laporan_pengajuan_api/filter_pengajuan';
$route['api/cetak_kak']        = 'Laporan_pengajuan_api/cetak_kak';

/* =======================
   PEMBELIAN / BARANG MASUK
======================= */
$route['api/get_barang_masuk']         = 'Barang_masuk_api/all_barang_masuk';
$route['api/tambah_pembelian']         = 'Barang_masuk_api/simpan'; //stok bertambah
$route['api/get_pembelian']            = 'Barang_masuk_api/all_pembelian';
$route['api/detail_pembelian/(:num)']  = 'Barang_masuk_api/detail_pembelian/$1';
$route['api/get_sumber']               = 'Sumber/sumber_all';

/* =======================
   KARTU KENDALI
======================= */
$route['api/generate']       = 'Kartukendali/generate';
$route['api/summary_karken'] = 'Kartukendali/summary';

/* =======================
   USER & RUANGAN
======================= */
$route['api/get_users']              = 'User_api/all_user';
$route['api/add_user']               = 'User_api/add_user';
$route['api/update_user']            = 'User_api/update_user';
$route['api/hapus_user/(:num)']      = 'User_api/hapus_user/$1';
$route['api/assign_ruangan']         = 'User_api/assign_ruangan';
$route['api/get_roles']              = 'User_api/get_roles';
$route['api/assign_role']            = 'User_api/assign_role';
$route['api/list_ruangan/(:any)']    = 'Ruangan/ruangan_all/$1';

/* =======================
   DASHBOARD
======================= */
$route['api/total_barang/(:any)']                  = 'Dashboard_api/total_barang/$1';
$route['api/total_stok/(:any)']                    = 'Dashboard_api/total_stok/$1';
$route['api/dashboard_permintaan/(:any)']          = 'Dashboard_api/jumlah_permintaan/$1';
$route['api/total_pemakaian_bulanan/(:any)/(:any)']= 'Dashboard_api/total_pemakaian_bulanan/$1/$2';
$route['api/pemakaian_ruangan/(:any)']             = 'Dashboard_api/total_pemakaian_bulanan_ruangan/$1';
$route['api/barang_terbanyak/(:any)']              = 'Dashboard_api/barang_terbanyak/$1';
$route['api/barang_stok_minimal']                 = 'Dashboard_api/barang_stok_minimal';
$route['api/pembelian_bulan/(:any)']              = 'Dashboard_api/pembelian_bulan/$1';
$route['api/pembelian_detail/(:any)']             = 'Dashboard_api/pembelian_detail/$1';

/* =======================
   POK / PROGRAM / KEGIATAN
======================= */
$route['api/program']          = 'Pok_api/program';
$route['api/kegiatan/(:any)']  = 'Pok_api/kegiatan/$1';
$route['api/kro/(:any)']       = 'Pok_api/kro/$1';
$route['api/ro/(:any)']        = 'Pok_api/ro/$1';
$route['api/komponen/(:any)']  = 'Pok_api/komponen/$1';
$route['api/gabungan_pok']     = 'Pok_api/gabungan_pok';

/* =======================
   LAINNYA
======================= */
$route['api/get_kabupaten'] = 'Kabupaten/kabupaten_all';
