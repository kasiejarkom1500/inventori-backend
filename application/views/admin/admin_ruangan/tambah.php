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
                    <label>Ruangan</label>
                    <input type="text" placeholder="Ruangan" name="ruangan" value="<?= set_value('ruangan'); ?>" class="form-control <?= form_error('ruangan') ? 'is-invalid' : null ?>" >
                    <small class="form-text text-danger"><?= form_error('ruangan'); ?></small>
                    </div>

                    <div class="form-group">
                    <label>Penanggungjawab</label>
                    <input type="text" name="penanggungjawab" placeholder="Penanggungjawab" value="<?= set_value('Penanggungjawab'); ?>" class="form-control <?= form_error('Penanggungjawab') ? 'is-invalid' : null ?>" >
                    <small class="form-text text-danger"><?= form_error('ruangan'); ?></small>
                    </div>
                    
                    <div class="form-group">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" placeholder="Jabatan" value="<?= set_value('jabatan'); ?>" class="form-control <?= form_error('jabatan') ? 'is-invalid' : null ?>" >
                    <small class="form-text text-danger"><?= form_error('jabatan'); ?></small>
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
