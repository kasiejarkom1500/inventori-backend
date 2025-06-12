<div class="card">
    <div class="card-header">

        <!-- Page Heading -->
        <h1 class="h3 mb-3 text-gray-800">Kelola Data User Pegawai</h1>

        <div class="card shadow mb-4">
            <div class="card-header py-2">
                <div class="my-2"></div>
                <?php echo $this->session->flashdata('message'); ?>
				<?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $this->session->flashdata('success'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $this->session->flashdata('error'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                <a href="<?= base_url('admin/tambah_user'); ?>" class="btn btn-primary mt-1">Tambah Data</a>
                <button class="btn btn-success mt-1" data-toggle="modal" data-target="#importModal">Import Excel</button>
				<button class="btn btn-warning mt-1" onclick="window.location.href='<?= base_url('admin/download_excel'); ?>'">Download Format Excel</button>

            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
								<th>Ruangan</th>
                                <th>Username</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- /.card-header -->



                            <?php
                            $no = 1;
                            foreach ($data_user as $cs) :
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $cs['nama']; ?></td>
                                    <td><?= $cs['jabatan']; ?></td>
									<td><?= $cs['ruangan']; ?></td>
                                    <td><?= $cs['username']; ?></td>

                                    <td>
                                        <a href="<?= base_url() ?>admin/ubah_user/<?= $cs['id_user']; ?>" class="btn-small text-info"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="<?= base_url() ?>admin/hapus_user/<?= $cs['id_user']; ?>" onclick="return confirm('Yakin Data Akan dihapus..?');" admin="button" class="btn-small text-danger" ><i class="fas fa-trash"></i>Hapus</a>
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data Pegawai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart('admin/excel'); ?>
                    <div class="form-group">
                        <label for="file">Choose Excel File</label>
                        <input type="file" name="file"  class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
