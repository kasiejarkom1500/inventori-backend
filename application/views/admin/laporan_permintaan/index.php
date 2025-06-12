<div class="card">
	<div class="card-header">
		<!-- Page Heading -->
		<h1 class="h3 mb-3 text-gray-800">Laporan Permintaan Barang (Barang Keluar)</h1>
		<form action="<?= base_url('laporan_permintaan/filter'); ?>" method="POST">
			<div class="row">
				<div class="col-md-5">
					<div class="form-group">
						<label for="tgl_mulai">Tanggal Mulai</label>
						<input type="date" name="tgl_mulai" id="tgl_mulai" class="form-control" required value="<?= $tgl_akhir ;?>">
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<label for="tgl_akhir">Tanggal Akhir</label>
						<input type="date" name="tgl_akhir" id="tgl_akhir" class="form-control" required value="<?= $tgl_akhir ;?>">
					</div>
				</div>
				<!-- <div class="col-md-5">
					<div class="form-group">
						<label for="tgl_akhir">Ruangan</label>
						<select class="form-control" name="id_ruangan" id="">
							<?php foreach ($ruangan as $item) : ?>
								<option value="<?= $item['id_ruangan']; ?>"><?= $item['ruangan']; ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div> -->
				<div class="col-md-2">
					<div class="form-group">
						<label>&nbsp;</label>
						<button type="submit" class="btn btn-primary btn-block mt-2">Filter</button>
					</div>
				</div>
			</div>
		</form>
		<div class="card shadow mb-4">
			<div class="card-header py-2">
				<div class="my-2"></div>
				<?php echo $this->session->flashdata('message'); ?>
				<a target="_blank" href="<?= base_url('/laporan_permintaan/cetak?tgl_mulai=' . $tgl_mulai . '&tgl_akhir=' . $tgl_akhir ); ?>" class="btn btn-success mt-1">Cetak Laporan</a>
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
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php $no = 1; ?>
							<?php foreach ($permintaan as $cs) : ?>
								<tr>
									<td><?= $no++ ?></td>
									<td><?= $cs['tanggal']; ?></td>
									<td><?= $cs['kd_barang']; ?></td>
									<td><?= $cs['nama_barang']; ?></td>
									<td><?= $cs['kategori']; ?></td>
									<td><?= $cs['satuan']; ?></td>
									<td><?= $cs['jumlah']; ?></td>
									<td><?= $cs['ruangan']; ?></td>
									<td><?= $cs['status']; ?></td>
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
