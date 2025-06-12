<div class="card">
	<div class="card-header">
		<!-- Page Heading -->
		<h1 class="h3 mb-3 text-gray-800">Detail Pengajuan</h1>
		<div class="card shadow mb-4">
			<div class="card-header py-2">
				<div class="my-2"></div> <?php echo $this->session->flashdata('message'); ?>
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
										break;
								}
							?>
								<tr>
									<td><?= $no++ ?></td>
									<td><?= !empty($cs['tanggal']) ? $cs['tanggal'] : 'Data Dihapus'; ?></td>
									<td><?= !empty($cs['kd_barang']) ? $cs['kd_barang'] : 'Data Dihapus'; ?></td>
									<td><?= !empty($cs['nama_barang']) ? $cs['nama_barang'] : 'Data Dihapus'; ?></td>
									<td><?= !empty($cs['kategori']) ? $cs['kategori'] : 'Data Dihapus'; ?></td>
									<td><?= !empty($cs['satuan']) ? $cs['satuan'] : 'Data Dihapus'; ?></td>
									<td><?= !empty($cs['jumlah']) ? $cs['jumlah'] : 'Data Dihapus'; ?></td>
									<td><?= !empty($cs['total_harga']) ? $cs['total_harga'] : 'Data Dihapus'; ?></td>
									<td><?= !empty($cs['ruangan']) ? $cs['ruangan'] : 'Data Dihapus'; ?></td>
									<td>
										<?php if (!empty($cs['status'])) : ?>
											<button type="button" class="btn <?= $statusClass ?>"><?= $statusLabel ?></button>
										<?php else : ?>
											<span>Data Dihapus</span>
										<?php endif; ?>
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
