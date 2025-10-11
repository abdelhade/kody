<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">

      <div class=" card card-primary">
        <div class="card-header">
          <h3 class="card-title"> <?= $lang_addjop ?></h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form role="form" action="do/doadd_jop.php" method="post">
          <div class="card-body">
            <div class="form-group">
              <label for="exampleInputEmail1"> <?= $lang_namejop ?></label>
              <input name="name" type="text" class="form-control" placeholder="<?= $lang_plholder_jop ?>">
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1"> <?= $lang_publicinfo ?></label>

              <textarea class="form-control" name="info" id="" cols="20" rows="5"></textarea>
            </div>

          </div>
          <!-- /.card-body -->

          <div class="card-footer">
            <button type="submit" class="btn btn-primary btn-block"><?= $lang_publicconfirm ?></button>
          </div>
        </form>
      </div>
    </div>
    <!-- Content Header (Page header) -->
  </section>
</div>



<?php include('includes/footer.php') ?>