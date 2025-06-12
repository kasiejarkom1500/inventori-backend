<div class="card">
	<div class="card-header">

		<!-- Page Heading -->
		<h1 class="h3 mb-3 text-gray-800">Kelola Data Barang Persediaan (521811)</h1>

		<div class="card shadow mb-4">
			<div class="card-header py-2">
				<div class="my-2"></div>
				<?php echo $this->session->flashdata('message'); ?>
				<?php if ($this->session->flashdata('success')) : ?>
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<?php echo $this->session->flashdata('success'); ?>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				<?php endif; ?>
				<?php if ($this->session->flashdata('error')) : ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<?php echo $this->session->flashdata('error'); ?>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				<?php endif; ?>
				<a href="<?= base_url('barang/tambah'); ?>" class="btn btn-primary mt-1">Tambah Data</a>
				<div class="row">
					<div class="col-auto">
						<button class="btn btn-success mt-1" data-toggle="modal" data-target="#importModal">Import Excel</button>
					</div>
					<div class="col-auto">
						<button class="btn btn-warning mt-1" onclick="window.location.href='<?= base_url('barang/download_excel'); ?>'">Download Format Excel</button>
					</div>
					<div class="col-auto">
						<form action="<?= base_url("barang/update_harga_pengajuan"); ?>" class="d-inline" method="post">
							<input name="harga_pengajuan" type="number" class="form-control mt-1" placeholder="Input % harga pengajuan">
							<button class="btn btn-outline-primary" type="submit">Save</button>
						</form>
					</div>
				</div>


			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table id="dataTable" class="table table-striped table-bordered" style="width:100%">
						<thead>
							<tr>
								<th>No</th>
								<th>Kd Barang</th>
								<th>Nama Barang</th>
								<th>Jenis Barang</th>
								<th>Satuan</th>
								<th>Stok</th>
								<th>Harga Satuan</th>
								<th>Stok Dasar</th>
								<th>
									Harga Pengajuan
								</th>
								<th>Aksi</th>
							</tr>
						</thead>

						<tbody>
                            <?php $no = 1 ;?>
                            <?php foreach ($barang as $cs) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $cs['kd_barang']; ?></td>
                                    <td><?= $cs['nama_barang']; ?></td>
                                    <td><?= $cs['kategori']; ?></td>
                                    <td><?= $cs['satuan']; ?></td>
                                    <td><?= $cs['stok']; ?></td>
                                    <td><?= $cs['harga_satuan']; ?></td>
                                    <td><?= $cs['stok_dasar']; ?></td>
                                    <td>
                                        <span id="harga_pengajuan_<?= $cs['id_barang']; ?>"><?= $cs['harga_pengajuan']; ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url() ?>barang/ubah/<?= $cs['id_barang']; ?>" class="btn-small text-info"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="<?= base_url() ?>barang/hapus/<?= $cs['id_barang']; ?>" onclick="return confirm('Yakin Data Akan dihapus..?');" class="btn-small text-danger"><i class="fas fa-trash" title="hapus"></i></a>
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
				<h5 class="modal-title" id="importModalLabel">Import Data Barang</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?php echo form_open_multipart('barang/excel'); ?>
				<div class="form-group">
					<label for="file">Choose Excel File</label>
					<input type="file" name="file" class="form-control" required>
				</div>
				<button type="submit" class="btn btn-primary">Upload</button>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#dataTable').DataTable({});
	});
</script>
