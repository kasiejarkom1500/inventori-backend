<div class="card card-primary">
     <div class="card-header">
         <h6 class="m-0 font-weight-bold text-primary">Edit Data User Pegawai</h6>
     </div>
     <!-- /.card-header -->
     <!-- form start -->
     <form action="" method="post">
         <div class="card-body">
             <input type="hidden" name="id" value="<?= $ubah_user['id_user'] ?>">
             <div class="form-group">
                 <label for="exampleInputEmail1">Nama Pegawai</label>
                 <input type="text" name="nama" value="<?= $ubah_user['nama'] ?>" class="form-control"  required>
             </div>

			 <div class="form-group">
                 <label for="exampleInputEmail1">Jabatan</label>
                 <input type="text" name="jabatan" value="<?= $ubah_user['jabatan'] ?>" class="form-control"  required>
             </div>

			 <div class="form-group">
                 <label for="exampleInputEmail1">Ruangan</label>
                 <input type="text" name="ruangan" value="<?= $ubah_user['ruangan'] ?>" class="form-control"  required>
             </div>
             
             <div class="form-group">
                 <label for="exampleInputEmail1">Username</label>
                 <input type="text" name="username" value="<?= $ubah_user['username'] ?>" class="form-control"  required>
             </div>
             <div class="form-group">
                 <label for="exampleInputEmail1">Password</label>
                 <input type="password" name="password" class="form-control" >
             </div>
             
             
         </div>
         <!-- /.card-body -->
         <div class="card-footer">
             <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i>&nbsp;Update</button>
         </div>
     </form>
 </div>
 <!-- /.card -->
