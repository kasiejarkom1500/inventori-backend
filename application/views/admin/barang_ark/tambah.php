<!-- general form elements -->
<div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Halaman Tambah Data</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form  method="post" action="<?= base_url("/barang/insert_barang") ;?>">
                <div class="card-body">

				<div class="form-group">
                    <label>Kd Barang</label>
                    <input type="text" placeholder="Kd Barang" name="kd_barang" value="<?= set_value('kd_barang'); ?>" class="form-control <?= form_error('kd_barang') ? 'is-invalid' : null ?>" >
                    <small class="form-text text-danger"><?= form_error('kd_barang'); ?></small>
                    </div>

                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" placeholder="Nama Barang" name="nama_barang" value="<?= set_value('nama_barang'); ?>" class="form-control <?= form_error('nama_barang') ? 'is-invalid' : null ?>" >
                    <small class="form-text text-danger"><?= form_error('nama_barang'); ?></small>
                    </div>

                <div class="form-group">
                    <label for="exampleInputEmail1">Jenis Barang</label>
                    <select class="custom-select" name="kategori">
                        <option selected>--Pilih Jenis Barang--</option>
                        <option value="ATK">ATK</option>
                        <option value="ARK">ARK</option>
                    </select>
                </div>
                    
				<div class="form-group">
                    <label for="exampleInputEmail1">Satuan</label>
                    <select class="custom-select" name="satuan">
                        <option selected>--Pilih Satuan--</option>
                        <option value="Lusin">Lusin</option>
                        <option value="Unit">Unit</option>
						<option value="Liter">Liter</option>
                        <option value="Pcs">Pcs</option>
						<option value="Buah">Buah</option>
                        <option value="Kotak">Kotak</option>
						<option value="Set">Set</option>
                        <option value="Box">Box</option>
						<option value="Bks">Bks</option>
                        <option value="Pack">Pack</option>
						<option value="Rim">Rim</option>
                        <option value="Qyt">Qyt</option>
						<option value="Botol">Botol</option>
                        <option value="Blok">Blok</option>
						<option value="Buku">Buku</option>
                        <option value="Lembar">Lembar</option>
						<option value="Exemplar">Exemplar</option>
                        <option value="Gulung">Gulung</option>
                    </select>
                </div>

				<div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" placeholder="Stok" value="<?= set_value('stok'); ?>" class="form-control <?= form_error('stok') ? 'is-invalid' : null ?>" >
                    <small class="form-text text-danger"><?= form_error('stok'); ?></small>
                </div>

				<div class="form-group">
                    <label>Harga Satuan</label>
                    <input type="number" id="harga_satuan" name="harga_satuan" placeholder="Harga Satuan" value="<?= set_value('harga_satuan'); ?>" class="form-control <?= form_error('harga_satuan') ? 'is-invalid' : null ?>" oninput="calculateHargaPengajuan()">                    
                    <small class="form-text text-danger"><?= form_error('harga_satuan'); ?></small>
                </div>
                
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
                </div>
                
        </form>
        </div>
        
            <!-- /.card -->
