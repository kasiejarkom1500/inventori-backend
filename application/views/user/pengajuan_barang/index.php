<div class="card">
    <div class="card-header">

        <!-- Page Heading -->
        <h1 class="h3 mb-3 text-gray-800">Kelola Data Pengajuan Barang</h1>

        <div class="card shadow mb-4">
            <div class="card-header py-2">
                <div class="my-2"></div>
                <?php echo $this->session->flashdata('message'); ?>
                <a href="<?= base_url('pengajuan_barang/tambah'); ?>" class="btn btn-primary mt-1">Tambah Data</a>
                
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Jumlah Pengajuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- /.card-header -->



                            <?php
                            $no = 1;
                            foreach ($pengajuan_barang as $cs) :
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $cs['tanggal']; ?></td>
                                    <td><?= $cs['jumlah_pengajuan']; ?></td>

                                    <td>
                                        <a href="<?= base_url() ?>pengajuan_barang/detail/<?= $cs['tanggal']; ?>/<?= $cs['id_user']; ?>" class="btn-small text-info"><i class="fas fa-edit"></i> Detail</a>
                                    </td>
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
