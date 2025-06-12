<div class="card">
	<div class="card-header">
		<!-- Page Heading -->
		<h1 class="h3 mb-3 text-gray-800">Laporan Stok</h1>
		<form action="<?= base_url('laporan_stok/filter'); ?>" method="POST">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label for="kategori">Jenis Barang</label>
						<select class="custom-select" name="jenis_barang">
							<option value="" selected>--Pilih Jenis Barang--</option>
							<option value="ATK">ATK</option>
							<option value="ARK">ARK</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="tgl_akhir">Ruangan</label>
						<select class="form-control" name="id_ruangan" id="">
							<option value="">---Pilih Ruangan---</option>
							<?php foreach ($ruangan as $item) : ?>
								<option value="<?= $item['id_ruangan']; ?>"><?= $item['ruangan']; ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<label for="tgl_akhir">Nama Barang</label>
						<input type="text" name="nama_barang" class="form-control">
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label>&nbsp;</label>
						<button type="submit" class="btn btn-primary btn-block">Filter</button>
					</div>
				</div>
			</div>
		</form>
		<div class="card shadow mb-4">
			<div class="card-header py-2">
				<div class="my-2"></div>
				<?php echo $this->session->flashdata('message'); ?>
				<?php if (isset($kategori) || isset($ruangan_id) || isset($nama_barang)) : ?>
					<form action="<?= base_url("/laporan_stok/cetak"); ?>" method="post">
						<input type="text" name="kategori" value="<?= $kategori; ?>" hidden>
						<input type="text" name="id_ruangan" value="<?= $ruangan_id; ?>" hidden>
						<input type="text" name="nama_barang" value="<?= $nama_barang; ?>" hidden>

						<button class="btn btn-success" type="submit">CETAK</button>
					</form>
				<?php endif ?>
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
