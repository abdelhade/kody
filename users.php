<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>


  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">

      <?php if(isset($role) && is_array($role) && $role['show_users'] == 1){ ?>
         <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="row">
            <div class="col-md-4"><h1><?=$lang_users?></h1></div>
            <div class="col-md-4">
            <a href="add_user.php" class="btn btn-large btn-primary float-left"><?=$lang_add_new?></a>
            </div>
             <div class="col-md-4"> <a href="myroles.php" class="btn btn-large btn-light float-right">ادوار المستخدمين</a>
            </div>
            </div>

          </div>

            <div class="card-body">
            <div class="table table-responsive">  
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>م</th>
                  <th><?=$lang_username?></th>
                  <th>النوع</th>
                  <th><?=$lang_userimage?></th>
                  <th><?=$lang_useroperations?></th>
                </tr>
                </thead>
                <tbody>
                <?php 
                $sql = "SELECT * FROM `users` order by id desc";
                $res = $conn->query($sql);

                $x=0;

                while ($row = $res->fetch_assoc()) {
                    $x++;
                ?>
                <tr>
                  <th><?php echo $x ?></th>
                  <th><?= $row['uname'] ?></th>
                  <th><?= $row['usertype'] ?></th>
                  <th><img class="cover" src="uploads/<?= $row['img'] ?>" alt=""></th>
                  <th><a class="btn btn-warning" href="edit_user.php?id=<?= $row['id']?>"><?=$lang_edit?></a><a class="btn btn-danger" href="do/do_deluser.php?id=<?= $row['id'] ?>">حذف</a></th>
                </tr>
                <?php } ?>            
                </tbody>
                <tfoot>
                  <tr>
                  <th>م</th>
                  <th><?=$lang_username?></th>
                  <th><?=$lang_usergender?></th>
                  <th><?=$lang_useroperations?></th>
                </tr>
            </tfoot>
              </table>
              </div>
            </div>

          </div>
            </div>
          </div>
      <?php }else{echo $userErrorMassage;} ?>
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>


<?php include('includes/footer.php') ?>
