<div class="card">
	<div class="card-header">

		<!-- Page Heading -->
		<h1 class="h3 mb-3 text-gray-800">Pengajuan Barang</h1>

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
								<th>Tanggal Pengajuan</th>
								<th>Kd Barang</th>
								<th>Nama Barang</th>
								<th>Jenis Barang</th>
								<th>Satuan</th>
								<th>Jumlah Pengajuan</th>
								<th>Total Harga</th>
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
							foreach ($pengajuan as $cs) :
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
										<input type="text" class="form-control" name="id_barang" value="<?= $cs['id_barang']; ?>" data-id="<?= $cs['id_pengajuan']; ?>" data-field="id_barang"hidden>

										<input type="text" class="form-control" name="jumlah" value="<?= $cs['jumlah']; ?>" data-id="<?= $cs['id_pengajuan']; ?>" data-field="jumlah">
									</td>
									<td><?= $cs['total_harga']; ?></td>
									<td><?= $cs['ruangan']; ?></td>
									<td>
										<button type="button" class="btn btn-<?= $statusClass ?>"><?= $statusLabel ?></button>
									</td>
									<td>
										<!-- Radio buttons for status -->
										<div class="form-check form-check-inline">
											<input class="form-check-input status-radio" type="radio" name="status_<?= $cs['id_pengajuan'] ?>" value="Pending" <?= ($cs['status'] == 'Pending') ? 'checked' : '' ?> data-id="<?= $cs['id_pengajuan']; ?>">
											<label class="form-check-label">Pending</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input status-radio" type="radio" name="status_<?= $cs['id_pengajuan'] ?>" value="Approved" <?= ($cs['status'] == 'Approved') ? 'checked' : '' ?> data-id="<?= $cs['id_pengajuan']; ?>">
											<label class="form-check-label">Approved</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input status-radio" type="radio" name="status_<?= $cs['id_pengajuan'] ?>" value="Rejected" <?= ($cs['status'] == 'Rejected') ? 'checked' : '' ?> data-id="<?= $cs['id_pengajuan']; ?>">
											<label class="form-check-label">Rejected</label>
										</div>
									</td>
									<td>
										<!-- Button to trigger AJAX -->
										<button <?php if ($statusLabel == 'Approved') : ?>
											<?= "disabled" ;?>
										<?php endif ?> type="button" class="btn btn-primary save-button" data-id="<?= $cs['id_pengajuan']; ?>">Simpan</button>
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

<script>
	$(document).ready(function() {
		// Function to handle the save button click
		$('.save-button').click(function() {
			var idPengajuan = $(this).data('id');
			var jumlah = $('input[name="jumlah"][data-id="' + idPengajuan + '"]').val();
			var id_barang = $('input[name="id_barang"][data-id="' + idPengajuan + '"]').val();

			var status = $('input[name="status_' + idPengajuan + '"]:checked').val();

			$.ajax({
				url: '<?= base_url('/pengajuan_barang/data_pengajuan_update'); ?>',
				type: 'POST',
				data: {
					id_pengajuan: idPengajuan,
					jumlah: jumlah,
					status: status,
					id_barang: id_barang
				},
				success: function(response) {
					console.log(response)
					alert('Data berhasil disimpan!');
					location.reload();
				},
				error: function() {
					alert('Terjadi kesalahan saat menyimpan data.');
				}
			});
		});
	});
</script>
