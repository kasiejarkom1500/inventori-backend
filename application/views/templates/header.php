<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<title>Admin Inventory </title>

	<!-- Custom fonts for this template-->
	<link href="<?= base_url(); ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

	<!-- Custom styles for this template-->
	<link href="<?= base_url(); ?>assets/css/sb-admin-2.min.css" rel="stylesheet">
	<link href="<?= base_url(); ?>assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/select2/css/select2.min.css">
	<link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<style>
		.btn-warning {
			background-color: yellow;
			color: black;
		}

		.btn-success {
			background-color: green;
			color: white;
		}

		.btn-danger {
			background-color: red;
			color: white;
		}

		.btn-secondary {
			background-color: grey;
			color: white;
		}

		/* Style for small buttons inside table cells */
		.btn-small {
			padding: 5px 10px;
			font-size: 12px;
		}
	</style>

</head>

<body id="page-top">
	<!-- Page Wrapper -->
	<div id="wrapper">
		<!-- Sidebar -->
		<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
			<!-- Sidebar - Brand -->
			<a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
				<div class="sidebar-brand-icon rotate-n-15"></div>
				<div class="sidebar-brand-text mx-3">Admin Inventory</div>
			</a>

			<!-- Divider -->
			<hr class="sidebar-divider my-0">

			<!-- Nav Item - Dashboard -->
			<li class="nav-item active">
				<a class="nav-link" href="<?= base_url('home'); ?>">
					<i class="fas fa-fw fa-tachometer-alt"></i>
					<span>Dashboard</span></a>
			</li>

			<!-- Nav Item - Data Master -->
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDataMaster" aria-expanded="true" aria-controls="collapseDataMaster">
					<i class="fas fa-solid fa-user-tie"></i>
					<span>Data Master</span>
				</a>
				<div id="collapseDataMaster" class="collapse" aria-labelledby="headingDataMaster" data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">
						<h6 class="collapse-header">Data Master</h6>
						<a class="collapse-item" href="<?= base_url('admin'); ?>">Data Admin</a>
						<a class="collapse-item" href="<?= base_url('admin/data_user'); ?>">Data Pegawai</a>
						<a class="collapse-item" href="<?= base_url('admin/admin_pengajuan'); ?>">Data Admin Pengajuan</a>
						<a class="collapse-item" href="<?= base_url('barang'); ?>">Data Barang ATK</a>
						<a class="collapse-item" href="<?= base_url('barang_ark'); ?>">Data Barang ARK</a>
						<a class="collapse-item" href="<?= base_url('admin/admin_ruangan'); ?>">Data Ruangan</a>
					</div>
				</div>
			</li>

			<!-- Nav Item - Menu Transaksi -->
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMenuTransaksi" aria-expanded="true" aria-controls="collapseMenuTransaksi">
					<i class="fas fa-solid fa-user-tie"></i>
					<span>Menu Transaksi</span>
				</a>
				<div id="collapseMenuTransaksi" class="collapse" aria-labelledby="headingMenuTransaksi" data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">
						<h6 class="collapse-header">Menu Transaksi</h6>
						<a class="collapse-item" href="<?= base_url('barang_masuk'); ?>">Barang Masuk</a>
						<a class="collapse-item" href="<?= base_url('barang_keluar'); ?>">Barang Keluar</a>
						<a class="collapse-item" href="<?= base_url('permintaan_barang/data_permintaan'); ?>">Permintaan Barang</a>
					</div>
				</div>
			</li>

			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMenuLaporan" aria-expanded="true" aria-controls="collapseMenuLaporan">
					<i class="fas fa-solid fa-user-tie"></i>
					<span>Menu Laporan</span>
				</a>
				<div id="collapseMenuLaporan" class="collapse" aria-labelledby="headingMenuLaporan" data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">
						<h6 class="collapse-header">Menu Laporan</h6>
						<a class="collapse-item" href="<?= base_url('laporan_pengajuan/lap_pengajuan'); ?>">Laporan Pengajuan (In)</a>
						<a class="collapse-item" href="<?= base_url('laporan_permintaan'); ?>">Laporan Permintaan (Out)</a>
						<a class="collapse-item" href="<?= base_url('laporan_stok'); ?>">Laporan Stok</a>
					</div>
				</div>
			</li>

			<!-- Divider -->
			<hr class="sidebar-divider d-none d-md-block">

			<!-- Nav Item - Logout -->
			<li class="nav-item">
				<a class="nav-link" href="<?= base_url('Auth/LogoutController'); ?>">
					<i class="fas fa-fw fa-table"></i>
					<span>Logout</span></a>
			</li>

			<!-- Sidebar Toggler (Sidebar) -->
			<div class="text-center d-none d-md-inline">
				<button class="rounded-circle border-0" id="sidebarToggle"></button>
			</div>
		</ul>
		<!-- End of Sidebar -->

		<!-- Content Wrapper -->
		<div id="content-wrapper" class="d-flex flex-column">
			<!-- Main Content -->
			<div id="content">
				<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
					<!-- Topbar Navbar -->
					<ul class="navbar-nav ml-auto">
						<!-- Nav Item - Alerts -->
						<li class="nav-item dropdown no-arrow mx-1">
							<a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-user"></i>

							</a>
							<!-- Dropdown - Alerts -->
							<div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
								<h6 class="dropdown-header">Profile</h6>
								<a class="dropdown-item d-flex align-items-center" href="#">
									<div class="mr-3">
										<div class="icon-circle bg-primary">
											<i class="fas fa-file-alt text-white"></i>
										</div>

									</div>
									<div>
										<div class="small text-gray-500"></div>
										<?= $this->fungsi->user_login()->nama ?>
									</div>
								</a>
								<a class="dropdown-item d-flex align-items-center" href="#">
									<div class="mr-3">
										<div class="icon-circle bg-success">
											<i class="fa fa-envelope text-white"></i>
										</div>
									</div>
									<div>
										<div class="small text-gray-500"></div>
										<?= $this->fungsi->user_login()->email ?>
									</div>
								</a>
							</div>
						</li>
					</ul>
				</nav>
				<!-- End of Topbar -->
				<div class="container-fluid">
					<script>
						document.addEventListener("DOMContentLoaded", function() {
							// Menangkap elemen badge-counter
							var badgeCounter = document.querySelector('.badge-counter');

							// Menambahkan event listener untuk menghilangkan notifikasi ketika notifikasi di-klik
							badgeCounter.addEventListener('click', function() {
								// Mengubah teks badge menjadi kosong
								badgeCounter.innerHTML = '';
								// Atau Anda bisa menghapus seluruh elemen span badge-counter jika ingin menghilangkannya sepenuhnya
								// badgeCounter.parentNode.removeChild(badgeCounter);
							});
						});
					</script>
