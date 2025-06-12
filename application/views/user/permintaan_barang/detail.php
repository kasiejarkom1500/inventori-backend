<div class="card">
    <div class="card-header">

        <!-- Page Heading -->
        <h1 class="h3 mb-3 text-gray-800">Detail Permintaan</h1>

        <div class="card shadow mb-4">
            <div class="card-header py-2">
                <div class="my-2"></div>
                <?php echo $this->session->flashdata('message'); ?>
                
                
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
							<th>No</th>
								<th>Tanggal Permintaan</th>
								<th>Kd Barang</th>
                                <th>Nama Barang</th>
                                <th>Jenis Barang</th>
								<th>Satuan</th>
								<th>Jumlah Permintaan</th>
                                <th>Ruangan</th>
								<th>status</th>
                                
                            </tr>
                        </thead>

                        <tbody>
                            <!-- /.card-header -->



                            <?php
                            $no = 1;
                            foreach ($detail as $cs) :
								$statusClass = '';
                                $statusLabel = '';
                                switch ($cs['status']) {
                                    case 'Pending':
                                        $statusClass = 'btn-warning';
                                        $statusLabel = 'Pending';
                                        break;
                                    case 'Approved':
                                        $statusClass = 'btn-success';
                                        $statusLabel = 'Approved';
                                        break;
                                    case 'Rejected':
                                        $statusClass = 'btn-danger';
                                        $statusLabel = 'Rejected';
                                        break;
                                    default:
                                        $statusClass = 'btn-secondary';
                                        $statusLabel = 'Unknown';
                                        break;}
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $cs['tanggal']; ?></td>
                                    <td><?= $cs['kd_barang']; ?></td>
									<td><?= $cs['nama_barang']; ?></td>
                                    <td><?= $cs['kategori']; ?></td>
									<td><?= $cs['satuan']; ?></td>
                                    <td><?= $cs['jumlah']; ?></td>
                                    <td><?= $cs['ruangan']; ?></td>
									<td><button type="button" class="btn <?= $statusClass ?>"><?= $statusLabel ?></button></td>
                                    
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    


    
</div>
</div>
