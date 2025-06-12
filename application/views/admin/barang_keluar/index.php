<div class="card">
    <div class="card-header">

        <!-- Page Heading -->
        <h1 class="h3 mb-3 text-gray-800">Kelola Data Barang Keluar</h1>

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
								<th>Tanggal Keluar</th>
								<th>Kd Barang</th>
                                <th>Nama Barang</th>
                                <th>Jenis Barang</th>
								<th>Satuan</th>
								<th>Ruangan</th>
								<th>Jumlah Barang</th>
								
                            </tr>
                        </thead>

                        <tbody>
                            <!-- /.card-header -->



                            <?php
                            $no = 1;
                            foreach ($data_barang_keluar as $cs) :
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
									<td><?= $cs['tanggal']; ?></td>

									<td><?= $cs['kd_barang']; ?></td>
                                    <td><?= $cs['nama_barang']; ?></td>
                                    <td><?= $cs['kategori']; ?></td>
                                    <td><?= $cs['satuan']; ?></td>
                                    <td><?= $cs['ruangan']; ?></td>
									<td><?= $cs['jumlah']; ?></td>
                                    
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
