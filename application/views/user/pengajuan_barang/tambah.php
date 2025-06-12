<!-- general form elements -->
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Halaman Pengajuan</h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form id="requestForm">
        <div class="card-body">
            <div class="mb-3 row">
            </div>
            <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Nama</label>
                <div class="col-sm-10">
                    <input type="text" value="<?php echo $user->nama; ?>" class="form-control" readonly>
                    <input type="hidden" id="id_user" value="<?php echo $user->id_user; ?>" name="id_user">
                </div>
            </div>

            <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Jabatan</label>
                <div class="col-sm-10">
                    <input type="text" value="<?php echo $user->jabatan; ?>" class="form-control" readonly>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Pilih Ruangan</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="id_ruangan" name="id_ruangan">
                        <option value="">--Pilih Ruangan--</option>
                        <?php foreach ($ruangan as $item) : ?>
                            <option value="<?= $item['id_ruangan']; ?>">
                                <?= $item['ruangan']; ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Tanggal</label>
                <div class="col-sm-10">
                    <input type="text" name="tanggal" value="<?php echo $tanggal ?>" class="form-control" readonly>
                </div>
            </div>
		

            <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Pilih Barang</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="id_barang" name="id_barang">
                        <option value="">--Pilih Barang--</option>
                        <?php foreach ($barang as $item) : ?>
						
                            <option value="<?= $item->id_barang; ?>" data-kategori="<?= $item->kategori; ?>" data-satuan="<?= $item->satuan; ?>" data-stok="<?= $item->stok; ?>" data-harga="<?= $item->harga_pengajuan_harga; ?>">
                                <?= $item->nama_barang; ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
			
            <!-- <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Pilih Ruangan</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="id_ruangan" name="id_ruangan">
                        <option value="">--Pilih Ruangan--</option>
                        <?php foreach ($ruangan as $item) : ?>
                            <option value="<?= $item['id_ruangan']; ?>">
                                <?= $item['ruangan']; ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div> -->

            <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Jenis Barang</label>
                <div class="col-sm-10">
                    <input type="text" id="kategori" class="form-control">
                </div>
            </div>

            <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Satuan</label>
                <div class="col-sm-10">
                    <input type="text" id="satuan" class="form-control" readonly>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Stok</label>
                <div class="col-sm-10">
                    <input type="text" id="stok" class="form-control" readonly>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Harga</label>
                <div class="col-sm-10">
                    <input type="text" id="harga" class="form-control" readonly>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Jumlah Pengajuan</label>
                <div class="col-sm-10">
                    <input type="number" id="jumlah" class="form-control" name="jumlah" required>
                </div>
            </div>

            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" id="addToTable1" name="tambah" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </form>
</div>

<div class="card card-secondary mt-3">
    <div class="card-header">
        <h3 class="card-title">Data Pengajuan</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="dataTable1">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr id="noData">
                    <td colspan="7">No data available in table</td>
                </tr>
            </tbody>
        </table>
        <button type="button" id="submitAll1" class="btn btn-success mt-3">Pengajuan</button>
    </div>
</div>
<!-- /.card -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

