<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>


  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">

      <?php if($role['show_users'] == 1){ ?>
      
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-10">
                    <h1>ادوار المستخدمين</h1>
                    </div>
                    <div class="col-md-2">
                        <a href="add_role.php" class="btn btn-outline-primary btn-sm">جديد</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table table-responsive">
                    <table id="myTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الوصف</th>
                                <th>عمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sqlshowrole = "SELECT id,rollname,info FROM usr_pwrs";
                            $resshowrole = $conn->query($sqlshowrole);
                            while ($rawrole = $resshowrole->fetch_assoc()) { 
                            ?>
                            <tr>
                                <th><?= $rawrole['id'] ?></th>
                                <th><?= $rawrole['rollname'] ?></th>
                                <th><?= $rawrole['info'] ?></th>
                                <th>
                                    <a href="edit_role.php?id=<?= md5($rawrole['id']) ?>&no=<?= $rawrole['id'] ?>&name=<?= $rawrole['rollname'] ?>" class="btn btn-warning btn-sm">تعديل</a>
                                    <a href="do/dodel_role.php?id=<?= $rawrole['id'] ?>" class="btn btn-danger btn-sm" >حذف</a>
                                </th>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                </div>

            </div>
            <div class="card-footer">

            </div>
        </div>





      <?php }else{echo $userErrorMassage;} ?>
      </div>
    </section>
  </div>
<?php include('includes/footer.php') ?>
