<div class="card card-primary">
     <div class="card-header">
         <h6 class="m-0 font-weight-bold text-primary">Edit Data Admin</h6>
     </div>
     <!-- /.card-header -->
     <!-- form start -->
     <form action="" method="post">
         <div class="card-body">
             <input type="hidden" name="id" value="<?= $ubah_admin['id_admin'] ?>">
             <div class="form-group">
                 <label for="exampleInputEmail1">Nama Admin</label>
                 <input type="text" name="nama" value="<?= $ubah_admin['nama'] ?>" class="form-control"  required>
             </div>

             <div class="form-group">
                 <label for="exampleInputEmail1">Email</label>
                 <input type="email" name="email" value="<?= $ubah_admin['email'] ?>" class="form-control"  required>
             </div>
             
             <div class="form-group">
                 <label for="exampleInputEmail1">Username</label>
                 <input type="text" name="username" value="<?= $ubah_admin['username'] ?>" class="form-control"  required>
             </div>
             <div class="form-group">
                 <label for="exampleInputEmail1">Password</label>
                 <input type="password" name="password" class="form-control" >
             </div>
             
             
         </div>
         <!-- /.card-body -->
         <div class="card-footer">
             <button type="submit" name="tambah" class="btn btn-primary"><i class="fa fa-check"></i>&nbsp;Update</button>
         </div>
     </form>
 </div>
 <!-- /.card -->
