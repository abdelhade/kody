<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                <div class="row">
                    <div class="col">
                <h2 class="hadi-fade-in2 bg-zinc-200">ضبط الارصدة الافتتاحية للمحازن</h2>
                    </div>
                    <div class="col text-left" >
                    <ul style="float: left;">
                    <li>
                                <button class="btn bg-red-400 ">تصفير الرصيد الافتتاحي</button>
                                <button class="btn bg-yellow-400">تعديل بضاعه اول المدة</button>
                                <button class="btn bg-green-400">حفظ</button>
                        </li>
                    </ul>    
                    </div>
                </div>    
            </div>






            <div class="card-body">
                <div class="table table-responsive table-stripped" id="horsTable">
                    <table class="table" id="myTable" data-page-length="50">
                        <thead>
                            <tr class="bg-gray-300">
                                <th>#</th>
                                <th>كود الصنف</th>
                                <th>اسم الصنف</th>
                                <th>الوحده</th>
                                <th>رصيد اول المدة الجديد</th>
                                <th>رصيد اول المدة الحالي</th>
                                <th>سعر اول المدة الجديد</th>
                                <th>سعر اول المدة الحالي</th>
                                <th>التسوية</th>   
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM myitems where isdeleted = 0 order by iname";
                            $result = $conn->query($sql);
                            $count = 0;
                            while ($row = $result->fetch_assoc()) {
                                $itmid = $row['id'];
                                $unit_result = $conn->query("SELECT uname FROM myunits WHERE id = (SELECT unit_id FROM item_units WHERE item_id = $itmid)")->fetch_assoc();
                                $unit_name = ($unit_result && isset($unit_result['uname'])) ? $unit_result['uname'] : '';
                               $count++;
                               ?>
                            <tr>
                                <th><?= $count?></th>
                                <th><?= $row['code'] ?></th>
                                <th><?= $row['iname']?></th>
                                <th><?= $unit_name?></th>
                                <th>رصيد اول المدة الجديد</th>
                                <th>رصيد اول المدة الحالي</th>
                                <th>سعر اول المدة الجديد</th>
                                <th>سعر اول المدة الحالي</th>
                                <th>التسوية</th>   
                            </tr>
                            <?php   }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
            
        </div>
    </section>
</div>

<?php include('includes/footer.php') ?>

