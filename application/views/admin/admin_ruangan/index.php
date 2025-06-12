<div class="card">
    <div class="card-header">

        <!-- Page Heading -->
        <h1 class="h3 mb-3 text-gray-800">Kelola Data Ruangan</h1>

        <div class="card shadow mb-4">
            <div class="card-header py-2">
                <div class="my-2"></div>
                <?php echo $this->session->flashdata('message'); ?>
                <a href="<?= base_url('admin/tambah_admin_ruangan'); ?>" class="btn btn-primary mt-1">Tambah Data</a>
                
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Ruangan</th>
                                <th>Jabatan</th>
								<th>Penanggungjawab</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- /.card-header -->



                            <?php
                            $no = 1;
                            foreach ($ruangan as $cs) :
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $cs['ruangan']; ?></td>
                                    <td><?= $cs['jabatan']; ?></td>
									<td><?= $cs['penanggungjawab']; ?></td>

                                    <td>
                                        <a href="<?= base_url() ?>admin/ubah_ruangan/<?= $cs['id_ruangan']; ?>" class="btn-small text-info"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="<?= base_url() ?>admin/hapus_ruangan/<?= $cs['id_ruangan']; ?>" onclick="return confirm('Yakin Data Akan dihapus..?');" admin="button" class="btn-small text-danger" ><i class="fas fa-trash"></i>Hapus</a>
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
