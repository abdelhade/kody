<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/connect.php'; ?>
<?php

$id = $_GET['id'];
$sql = "select * from users where id = $id";
$res = $conn->query($sql);
$row = $res->fetch_assoc();

?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">تعديل المستخدم</h3>
                    </div>
                    <form role="form" enctype="multipart/form-data" action="do/doedit_user.php?id=<?= $row['id'] ?>"
                        method="post" autocomplete="off">
                        <div class="card-body">
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1"> <?= $lang_username ?></label>
                                <input value="<?= $row['uname'] ?>" name="uname" type="text" class="form-control"
                                    id="exampleInputEmail1" placeholder="اكتب اسم المستخدم">
                            </div>


                            <div class="form-group col-md-3">
                                <label for="">دور المستخدم</label>
                                <select name="userrole" class="form-control">
                                    <?php
                        $sqlrol = "SELECT id,rollname fROM usr_pwrs order by id";
                        $resrol = $conn->query($sqlrol);
                        while ($rowrol =$resrol->fetch_assoc()) { ?>
                                    <option value="<?= $rowrol['id'] ?>" <?php if ($rowrol['id'] == $row['userrole']) {
                                        echo ' selected ';
                                    } ?>><?= $rowrol['rollname'] ?>
                                    </option>
                                    <?php } ?>
                                </select>

                            </div>

                            <?php if(isset($role) && $role['edit_user_passwords'] == 1): ?>
                            <div class="form-group col-md-3">
                                <label for="password">كلمة المرور الجديدة</label>
                                <input name="password" type="password" class="form-control" id="password"
                                    placeholder="اترك فارغاً إذا كنت لا تريد تغيير كلمة المرور">
                                <small class="form-text text-muted">اترك هذا الحقل فارغاً إذا كنت لا تريد تغيير كلمة
                                    المرور</small>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="confirm_password">تأكيد كلمة المرور</label>
                                <input name="confirm_password" type="password" class="form-control"
                                    id="confirm_password" placeholder="تأكيد كلمة المرور الجديدة">
                            </div>
                            <?php endif; ?>

                            <div class="form-group col-md-3">
                                <br>
                                <label for="img" class="btn btn-outline-secondary btn-lg">
                                    <?= $lang_image_upload ?></label>
                                <input type="file" name="img" id="img">
                            </div>

                            <div class="card-footer col-md-3">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const passwordField = document.getElementById('password');
                            const confirmPasswordField = document.getElementById('confirm_password');
                            const form = document.querySelector('form');

                            function validatePasswords() {
                                if (passwordField.value !== '' || confirmPasswordField.value !== '') {
                                    if (passwordField.value !== confirmPasswordField.value) {
                                        confirmPasswordField.setCustomValidity('كلمات المرور غير متطابقة');
                                        return false;
                                    } else {
                                        confirmPasswordField.setCustomValidity('');
                                        return true;
                                    }
                                }
                                return true;
                            }

                            passwordField.addEventListener('input', validatePasswords);
                            confirmPasswordField.addEventListener('input', validatePasswords);

                            form.addEventListener('submit', function(e) {
                                if (!validatePasswords()) {
                                    e.preventDefault();
                                    alert('يرجى التأكد من تطابق كلمات المرور');
                                }
                            });
                        });
                    </script>
                </div>

            </div>




        </div>
    </section>
</div>


<?php include 'includes/footer.php'; ?>
