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
                    <label>Nama Admin Pengajuan</label>
                    <input type="text" placeholder="Nama Admin Pengajuan" name="nama" value="<?= set_value('nama'); ?>" class="form-control <?= form_error('nama') ? 'is-invalid' : null ?>" >
                    <small class="form-text text-danger"><?= form_error('nama'); ?></small>
                    </div>

                    <div class="form-group">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" placeholder="Jabatan" value="<?= set_value('jabatan'); ?>" class="form-control <?= form_error('jabatan') ? 'is-invalid' : null ?>" >
                    <small class="form-text text-danger"><?= form_error('jabatan'); ?></small>
                    </div>

					<div class="form-group">
                    <label>Ruangan</label>
                    <input type="text" name="ruangan" placeholder="ruangan" value="<?= set_value('ruangan'); ?>" class="form-control <?= form_error('ruangan') ? 'is-invalid' : null ?>" >
                    <small class="form-text text-danger"><?= form_error('ruangan'); ?></small>
                    </div>
                    
                    <div class="form-group">
                    <label for="exampleInputEmail1">Username</label>
                    <input type="text" name="username" placeholder="Username" value="<?= set_value('username'); ?>" class="form-control <?= form_error('username') ? 'is-invalid' : null ?>">
                    <small class="form-text text-danger"><?= form_error('username'); ?></small>
                    </div>
                    <div class="form-group">
                    <label for="exampleInputEmail1">Password</label>
                    <input type="password" name="password" placeholder="Password" value="<?= set_value('password'); ?>" class="form-control <?= form_error('password') ? 'is-invalid' : null ?>">
                    <small class="form-text text-danger"><?= form_error('password'); ?></small>
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
