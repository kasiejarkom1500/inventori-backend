<!-- general form elements -->
<div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Halaman Tambah Data</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form  method="post" action="">
                <div class="card-body">
                <div class="form-group">
                    <label>Tanggal Masuk</label>
                    <input type="date" placeholder="Tanggal Masuk" name="tgl_masuk" value="<?= set_value('tgl_masuk'); ?>" class="form-control <?= form_error('tgl_masuk') ? 'is-invalid' : null ?>" >
                    <small class="form-text text-danger"><?= form_error('tgl_masuk'); ?></small>
                    </div>

					<div class="form-group">
                    <label for="exampleInputEmail1">Data Barang</label>
                    <select class="custom-select" id="id_barang" name="id_barang">
                        <option value="">--Pilih Barang--</option>
                        
                    </select>
                </div>

                    <div class="form-group">
                    <label>Jenis Barang</label>
                    <input type="text" id="kategori"  class="form-control" readonly>
                    
                    </div>

					<div class="form-group">
                    <label>Satuan</label>
                    <input type="text" id="satuan"  class="form-control" readonly>
                    
                    </div>
                    
                    <div class="form-group">
                    <label for="exampleInputEmail1">Jumlah Barang</label>
                    <input type="number" name="jumlah" placeholder="Jumlah Barang" value="<?= set_value('jumlah'); ?>" class="form-control <?= form_error('jumlah') ? 'is-invalid' : null ?>">
                    <small class="form-text text-danger"><?= form_error('jumlah'); ?></small>
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
