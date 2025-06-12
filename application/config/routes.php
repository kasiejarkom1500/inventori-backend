<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['change-password'] = 'Auth/ChangePasswordController';
$route['reset_password/(:any)'] = 'Auth/ChangePasswordController/index/$1';

//GET routes

//Routes Barang
$route['api/get_barang_atk'] = 'Barang_api/all_barang';

$route['api/get_barang_ark'] = 'Barang_api/all_barang_ark';

$route['api/get_barang_all'] = 'Barang_api/barang';

$route['api/get_barang_id/(:num)'] = 'Barang_api/barang_id/$1';

$route['api/get_barang_masuk'] = 'Barang_masuk_api/all_barang_masuk';

$route['api/get_transaksi_permintaan'] = 'Permintaan_barang_api/transaksi_permintaan';

$route['api/get_detail_permintaan/(:num)/(:any)'] = 'Permintaan_barang_api/permintaan_detail/$1/$2';

$route['api/cetak_permintaan/(:num)/(:any)'] = 'Permintaan_barang_api/cetak_permintaan/$1/$2';

$route['api/update_status'] = 'Permintaan_barang_api/update_status';

$route['api/tambah_permintaan'] = 'Permintaan_barang_api/simpan';

//Pengajuan
$route['api/get_transaksi_pengajuan'] = 'Pengajuan_barang_api/transaksi_pengajuan';

$route['api/get_detail_pengajuan/(:num)/(:any)'] = 'Pengajuan_barang_api/pengajuan_detail/$1/$2';

$route['api/update_status_pengajuan'] = 'Pengajuan_barang_api/update_status';

$route['api/tambah_pengajuan'] = 'Pengajuan_barang_api/simpan';

$route['api/filter_pengajuan'] = 'Laporan_pengajuan_api/filter_pengajuan';

$route['api/cetak_kak'] = 'Laporan_pengajuan_api/cetak_kak';


//POST
$route['api/insert_barang'] = 'Barang_api/insert_barang';

//Delete
$route['api/hapus_barang/(:num)'] = 'Barang_api/hapus_barang/$1';

//Update
//Delete
$route['api/ubah_barang/(:num)'] = 'Barang_api/ubah/$1';

//Import_Excel_Barang
$route['api/import_excel'] = 'Barang_api/import_excel';

//Update Harga Pengajuan
$route['api/update_harga_pengajuan'] = 'Barang_api/update_harga_pengajuan';


//POST routes
$route['api/login'] = 'Auth/LoginController_api/newlogin';

//Kartu Kendali
$route['api/generate'] = 'Kartukendali/generate';

//Input Pembelian
$route['api/tambah_pembelian'] = 'Barang_masuk_api/simpan';
$route['api/get_pembelian'] = 'Barang_masuk_api/all_pembelian';

$route['api/detail_pembelian/(:num)'] = 'Barang_masuk_api/detail_pembelian/$1';

$route['api/get_sumber'] = 'Sumber/sumber_all';

$route['api/summary_karken'] = 'Kartukendali/summary';


//User
$route['api/get_users'] = 'User_api/all_user';

//Dashboard
$route['api/total_barang/(:any)'] = 'Dashboard_api/total_barang/$1';
$route['api/total_stok/(:any)'] = 'Dashboard_api/total_stok/$1';
$route['api/dashboard_permintaan/(:any)'] = 'Dashboard_api/jumlah_permintaan/$1';
//routes pemakaian bulanan
$route['api/total_pemakaian_bulanan/(:any)/(:any)'] = 'Dashboard_api/total_pemakaian_bulanan/$1/$2';

