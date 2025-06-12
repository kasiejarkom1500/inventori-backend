<div class="card">
	<div class="card-header">

		<!-- Page Heading -->
		<h1 class="h3 mb-3 text-gray-800">Permintaan Barang</h1>

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
								<th>Pilih status</th>
								<th>Aksi</th>
							</tr>
						</thead>

						<tbody>
							<!-- /.card-header -->



							<?php
							$no = 1;
							foreach ($permintaan as $cs) :
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
										break;
								}
							?>
								<tr>
									<td><?= $no++ ?></td>
									<td><?= $cs['tanggal']; ?></td>
									<td><?= $cs['kd_barang']; ?></td>
									<td><?= $cs['nama_barang']; ?></td>
									<td><?= $cs['kategori']; ?></td>
									<td><?= $cs['satuan']; ?></td>
									<td>
										<input type="text" name="jumlah_<?= $cs['id_permintaan'] ;?>" value="<?= $cs['jumlah']; ?>" class="form-control">
										<input type="text" name="id_permintaan" hidden value="<?= $cs['id_permintaan']; ?>" class="form-control">
									</td>
									<td><?= $cs['ruangan']; ?></td>
									<td><button type="button" class="btn <?= $statusClass ?>"><?= $statusLabel ?></button></td>
									<td>
										<!-- Input radio untuk status -->
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="status_<?= $cs['id_permintaan'] ?>" id="pending_<?= $cs['id_permintaan'] ?>" value="Pending" <?= ($cs['status'] == 'Pending') ? 'checked' : '' ?>>
											<label class="form-check-label" for="pending_<?= $cs['id_permintaan'] ?>">Pending</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="status_<?= $cs['id_permintaan'] ?>" id="approved_<?= $cs['id_permintaan'] ?>" value="Approved" <?= ($cs['status'] == 'Approved') ? 'checked' : '' ?>>
											<label class="form-check-label" for="approved_<?= $cs['id_permintaan'] ?>">Approved</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="status_<?= $cs['id_permintaan'] ?>" id="rejected_<?= $cs['id_permintaan'] ?>" value="Rejected" <?= ($cs['status'] == 'Rejected') ? 'checked' : '' ?>>
											<label class="form-check-label" for="rejected_<?= $cs['id_permintaan'] ?>">Rejected</label>
										</div>
									</td>
									<td>
										<!-- Tombol simpan -->
										<button type="button" class="btn btn-primary btn-save" data-id="<?= $cs['id_permintaan'] ?>">Simpan</button>
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
