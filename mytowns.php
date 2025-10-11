<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">


        <div class="card">
            <div class="card-header">
                <h2>المدن</h2>
            </div>
            <div class="card-body">
                
            <div class="col-md-4 border rounded m-0 p-0">
                    <form action="do/doadd_town.php" method="post">                                      
                    <input type="text" name="name" class="form-control frst focus:bg-orange-200" placeholder="ادخل مجموعة جديدة">
                    <button tybe="submit" class="btn btn-info btn-block">حفظ</button>
                </form>
                </div>


                <div class="table" id="">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم المجموعة</th>
                                <th>عمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $x=0;
                            $resgrb =$conn->query("SELECT * FROM towns where isdeleted = 0");
                            while ($rowtwn = $resgrb->fetch_assoc()) {
                            $x++;
                            ?>
                            <form action="do/doedit_town.php?id=<?= $rowtwn['id']?>" method="post">
                            <tr>
                                <th><?= $x ?></th>
                                <th><input type="text" value="<?= $rowtwn['name']?>" name="name"></th>
                                <th>
                                    <button type="submit" class="btn btn-sm btn-warning">
                                    <i class="fa fa-pen"></i></button>
                                <a href="do/dodel_town.php?id=<?= $rowtwn['id'] ?>" class="btn bg-red-600 text-red-100"><i class="fa fa-trash"></i></a>
                                </th>
                            </tr>
                            </form>
                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>





        </div>
    </section>
</div>
<?php include('includes/footer.php') ?>
