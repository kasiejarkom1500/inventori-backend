<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin Pengajuan Inventory</title>

    
    <!-- Custom fonts for this template-->
    <link href="<?= base_url(); ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url(); ?>assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= base_url(); ?>assets/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="<?= base_url(); ?>assets/vendor/js/sweetalert2.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url(); ?>assets/vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">    
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    
     

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
		.nav-item .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
        }

        .nav-item .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('.nav-item .nav-link').on('click', function() {
                $('.nav-item .nav-link').removeClass('active');
                $(this).addClass('active');
            });
        });
    </script>
</head>


<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <div class="sidebar-brand-icon rotate-n-15">
          
            </div>
                <div class="sidebar-brand-text mx-3">Inventory</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <li class="nav-item active">
                <a class="nav-link" href="<?= base_url('home/home_admin_pengajuan'); ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <li class="nav-item">
				<a class="nav-link collapsed" href="<?= base_url('AdminP_Data_Barang'); ?>" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
					<i class="fas fa-solid fa-hospital-user"></i>
					<span>Data Barang</span>
               </a>
            </li>

            

            <li class="nav-item">
                <a class="nav-link collapsed" href="<?= base_url('pengajuan_barang/data_pengajuan'); ?>" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-solid fa-hospital-user"></i>
                    <span>Data Pengajuan Barang</span>
                </a>
            </li>

			<li class="nav-item">
                <a class="nav-link collapsed" href="<?= base_url('Laporan_pengajuan'); ?>" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-solid fa-hospital-user"></i>
                    <span>Laporan Pengajuan</span>
                </a>
            </li>

            
           
<!-- Nav Item - Dashboard -->


     
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('Auth/LogoutController'); ?>">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Logout</span></a>
            </li>


            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message -->


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
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user"></i>
                               
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">Profile</h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
										
                                    </div>
									<div>
                                        <div class="small text-gray-500"></div>
                                        <?= $this->fungsi->user_login2()->nama ?>
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
                                        <?= $this->fungsi->user_login2()->jabatan ?>
                                    </div>
                                </a>
                                
                                
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->
                <div class="container-fluid">
            





                    <!-- /.container-fluid -->
