<div class="card card-primary">
     <div class="card-header">
         <h6 class="m-0 font-weight-bold text-primary">Edit Data Ruangan</h6>
     </div>
     <!-- /.card-header -->
     <!-- form start -->
     <form action="" method="post">
         <div class="card-body">
             <input type="hidden" name="id" value="<?= $ubah_ruangan['id_ruangan'] ?>">
             <div class="form-group">
                 <label for="exampleInputEmail1">Ruangan</label>
                 <input type="text" name="ruangan" value="<?= $ubah_ruangan['ruangan'] ?>" class="form-control"  required>
             </div>

			 <div class="form-group">
                 <label for="exampleInputEmail1">Jabatan</label>
                 <input type="text" name="jabatan" value="<?= $ubah_ruangan['jabatan'] ?>" class="form-control"  required>
             </div>

			 <div class="form-group">
                 <label for="exampleInputEmail1">Penanggungjawab</label>
                 <input type="text" name="penanggungjawab" value="<?= $ubah_ruangan['penanggungjawab'] ?>" class="form-control"  required>
             </div>
             
            </div>
         <!-- /.card-body -->
         <div class="card-footer">
             <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i>&nbsp;Update</button>
         </div>
     </form>
 </div>
 <!-- /.card -->
