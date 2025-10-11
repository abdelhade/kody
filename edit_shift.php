<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php include('includes/connect.php');

$id = $_GET['id'];
$sqlshift = "SELECT * FROM shifts WHERE id = '$id'";
$resshift = $conn->query($sqlshift);
$rowshift = $resshift->fetch_assoc();

?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">

            <div class=" card card-warning">
                <div class="card-header">
                    <h3 class="card-title"> <?= $lang_infoshift ?></h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form role="form" action="do/doedit_shift.php?id=<?= $id ?>" method="POST">
                    <div class="card-body">

                        <div class="row">
                            <div class="col">

                                <div class="form-group">
                                    <label for="name"> <?= $lang_addhicont_name ?></label>
                                    <input name="name" value="<?= $rowshift['name'] ?>" id="name" type="text" class="form-control" placeholder="اسم الورديه">
                                </div>

                            </div>
                            <div class="col">
                                <div class="form-group mt-4">
                                    <label for=""><?= $lang_workday ?></label>
                                </div>
                                <?php
                                $workingdayssql = "SELECT workingdays FROM shifts WHERE id = '$id'";
                                $wdresult = $conn->query($workingdayssql);
                                $workingdaysrow = $wdresult->fetch_row();
                                $workingdays = explode(',', $workingdaysrow[0]);


                                $wdarray = [];

                                ?>
                                <div class="form-check form-switch">
                                    <input name="sat" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" <?php if (in_array(6, $workingdays)) echo "checked" ?>>
                                    <label class="form-check-label" for="flexSwitchCheckDefault"><?= $lang_addsh_sat ?></label>
                                </div>
                                <div class="form-check form-switch">
                                    <input name="sun" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" <?php if (in_array(7, $workingdays)) echo "checked" ?>>
                                    <label class="form-check-label" for="flexSwitchCheckChecked"><?= $lang_addsh_sun ?></label>
                                </div>
                                <div class="form-check form-switch">
                                    <input name="mon" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDisabled" <?php if (in_array(1, $workingdays)) echo "checked" ?>>
                                    <label class="form-check-label" for="flexSwitchCheckDisabled"><?= $lang_addsh_mon ?></label>
                                </div>
                                <div class="form-check form-switch">
                                    <input name="tus" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckCheckedDisabled" <?php if (in_array(2, $workingdays)) echo "checked" ?>>
                                    <label class="form-check-label" for="flexSwitchCheckCheckedDisabled"><?= $lang_addsh_tue ?></label>
                                </div>
                                <div class="form-check form-switch">
                                    <input name="wed" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckCheckedDisabled" <?php if (in_array(3, $workingdays)) echo "checked" ?>>
                                    <label name="" class="form-check-label" for="flexSwitchCheckCheckedDisabled"><?= $lang_addsh_wed ?></label>
                                </div>
                                <div class="form-check form-switch">
                                    <input name="thur" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckCheckedDisabled" <?php if (in_array(4, $workingdays)) echo "checked" ?>>
                                    <label class="form-check-label" for="flexSwitchCheckCheckedDisabled"><?= $lang_addsh_thu ?></label>
                                </div>

                                <div class="form-check form-switch">
                                    <input name="fri" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckCheckedDisabled" <?php if (in_array(5, $workingdays)) echo "checked" ?>>
                                    <label class="form-check-label" for="flexSwitchCheckCheckedDisabled"><?= $lang_addsh_fri ?></label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /.card-body -->

                    <label for=""><?= $lang_end_dismissal_date ?></label>
                    <label for=""><?= $lang_end_dismissal_date ?></label>

                    <div class=" card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"> <?= $lang_Attendance_rules ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for=""><?= $lang_addsh_start ?></label>
                                        <input value="<?= $rowshift['shiftstart'] ?>" name="shiftstart" type="time" id="" class="form-control">
                                    </div>

                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for=""><?= $lang_addsh_end ?></label>
                                        <input value="<?= $rowshift['shiftend'] ?>" name="shiftend" type="time" id="" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for=""><?= $lang_addsh_stardatt ?></label>
                                        <input value="<?= $rowshift['instart'] ?>" name="instart" type="time" id="" class="form-control">
                                    </div>

                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for=""><?= $lang_addsh_endatt ?></label>
                                        <input value="<?= $rowshift['inend'] ?>" name="inend" type="time" id="" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for=""><?= $lang_addsh_startout ?> </label>
                                        <input value="<?= $rowshift['outstart'] ?>" name="outstart" type="time" id="" class="form-control">
                                    </div>

                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for=""><?= $lang_addsh_endout ?></label>
                                        <input value="<?= $rowshift['outend'] ?>" name="outend" type="time" id="" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for=""><?= $lang_addsh_delaylimits ?></label>
                                        <input value="<?= $rowshift['latelimit'] ?>" name="latelimit" type="number" id="" class="form-control">
                                    </div>

                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for=""><?= $lang_addsh_earlylimits ?></label>
                                        <input value="<?= $rowshift['earlylimit'] ?>" name="earlylimit" type="number" id="" class="form-control">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class=" card-footer">
                            <button type="submit" class="btn btn-warning btn-block"><?= $lang_addhicont_confirm ?></button>
                        </div>
                </form>
            </div>
        </div>
        <!-- Content Header (Page header) -->
    </section>
</div>



<?php include('includes/footer.php') ?>