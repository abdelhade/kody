<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                
                <div class="card-head">
                <h3>ادخال الوحدات</h3>    
            </div>
            <div class="card-body">
            <form action="do/doadd_unit.php" method="post" id="myForm">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="">الوحدة</label>
                            <input type="text" class="form-control" name="uname" pattern=".{3,}" title="يجب ان يكون الاسم 3 حروف علي الاقل" required>
                            <br>

                            <button type="submit">حفظ f12</button>
                            </div>
                    </div>
                    </form>
                    <div class="table table-responsive" id="horsTable">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم الوحدة</th>
                                    <th>تعديل</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $x=0;
                                $resunits = $conn->query("SELECT * FROM myunits order by id asc");
                                while ($rowunits = $resunits->fetch_assoc()) {
                                $x++;
                                ?>
                                <tr>
                                    <form action="do/doedit_unit.php?id=<?= $rowunits['id'] ?>" method="post">
                                    <th><?= $rowunits['id'] ?></th>
                                    <th><input type="text" name="uname" id="" class="form-control" value="<?= $rowunits['uname'] ?>"></th>
                                    <th><button type="submit" class="btn btn-warning">تعديل</button></th>
                                    
                                    </form>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
            </div>
            </div>
        </div>
    </section>
</div>

<?php include('includes/footer.php') ?>
