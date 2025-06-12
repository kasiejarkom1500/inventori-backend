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
                    <label>Nama Admin</label>
                    <input type="text" placeholder="Nama Admin" name="nama" value="<?= set_value('nama'); ?>" class="form-control <?= form_error('nama') ? 'is-invalid' : null ?>" >
                    <small class="form-text text-danger"><?= form_error('nama'); ?></small>
                    </div>

                    <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Email" value="<?= set_value('email'); ?>" class="form-control <?= form_error('email') ? 'is-invalid' : null ?>" >
                    <small class="form-text text-danger"><?= form_error('email'); ?></small>
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
