<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Data Barang</h6>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
				<thead>
					<tr>
						<th>No</th>
						<th>Kd Barang</th>
						<th>Nama Barang</th>
						<th>Jenis Barang</th>
						<th>Satuan</th>
						<th>Stok</th>
					</tr>
				</thead>

				<tbody>
					<?php
					$no = 1;
					foreach ($data_barang as $cs) :
					?>
						<tr>
							<td><?= $no++ ?></td>
							<td><?= $cs['kd_barang']; ?></td>
							<td><?= $cs['nama_barang']; ?></td>
							<td><?= $cs['kategori']; ?></td>
							<td><?= $cs['satuan']; ?></td>
							<td><?= $cs['stok']; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>