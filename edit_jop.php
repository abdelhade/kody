<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php include('includes/connect.php');


$id = $_GET['id'];
$sqljop = "SELECT * FROM `jops` WHERE `id` = '$id' ";

$rowjop = $conn->query($sqljop)->fetch_assoc();



?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">

            <div class=" card card-warning">
                <div class="card-header">
                    <h3 class="card-title"> <?= $lang_jopedit ?></h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form role="form" action="do/doedit_jop.php?id=<?= $id ?>" method="post">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1"> <?= $lang_namejop ?></label>
                            <input value="<?= $rowjop['name'] ?>" name="name" type="text" class="form-control" placeholder="<?= $lang_plholder_jop ?>">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1"> <?= $lang_publicinfo ?></label>

                            <textarea class="form-control" name="info" id="" cols="20" rows="5">
                            <?= $rowjop['info'] ?>
                            </textarea>
                        </div>

                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning btn-block"><?= $lang_publicconfirm ?></button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Content Header (Page header) -->
    </section>
</div>



<?php include('includes/footer.php') ?>