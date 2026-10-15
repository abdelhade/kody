<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>
<?php include('includes/navbar.php'); ?>
<?php include('includes/connect.php'); ?>


<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">



<div class="col">

  <div class="card card-primary col-lg-6">
    <div class="card-header">
      <h3 class="card-title"> <?= $lang_add_new_user ?></h3>
    </div>

    <form role="form" action="do/doadd_user.php" method="post" autocomplete="off" enctype="multipart/form-data">
      <div class="card-body">
        <div class="form-group">
          <label for="exampleInputEmail1"><?= $lang_username ?></label>
          <input required name="uname" type="text" class="form-control" id="exampleInputEmail1" placeholder="<?= $lang_pbholder_uname ?>">
        </div>

        <div class="form-group">
          <label for="exampleInputEmail1"><?= $lang_password ?></label>
          <input name="password" type="password" class="form-control" id="exampleInputEmail1" placeholder="<?= $lang_pbholder_password ?>">
        </div>

        <div class="form-group">
          <label for="">دور المستخدم</label>
          <select name="userrole" class="form-control">
         <?php
         require_once __DIR__ . '/classes/Roles.php';
         foreach (Roles::options($conn) as $opt):
             if (Roles::assignError($conn, $opt['id']) !== null) {
                 continue;
             }
         ?>
         <option value="<?= $opt['id'] ?>"><?= htmlspecialchars($opt['name'], ENT_QUOTES, 'UTF-8') ?></option>
         <?php endforeach; ?>
        </select>

        </div>
        
        <div class="form-group">
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="is_waiter" name="is_waiter" value="1">
            <label class="custom-control-label" for="is_waiter">
              <i class="fas fa-user-tie"></i> هذا المستخدم ويتر
            </label>
          </div>
          <small class="form-text text-muted">
            الويترز يمكنهم تسجيل الدخول بالباركود في صفحة POS الخاصة بهم
          </small>
        </div>
        <h5 class="mt-3">الافتراضيات</h5>
        <?php UserDefaults::renderUserFields($conn); ?>
          <br>
          <label for="img" class="btn btn-outline-secondary btn-lg"><?= $lang_image_upload ?></label>
          <input hidden type="file" name="img" id="img">
        </div>


        <div class="card-footer">
          <button type="submit" class="btn btn-primary"><?= $lang_publicconfirm ?></button>
        </div>
    </form>
  </div>
</div>
</div>
  </section>
</div>
  <!-- /.card -->
  <?php include('includes/footer.php'); ?>