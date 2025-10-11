<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">

        <div class="card ">
            <div class="card-header sticky-header">
                <div class="row">
                <div class="col"><h2>الاصناف</h2></div>
                <div class="col-md-6"><input type="text" id="search" class="form-control frst focus:bg-orange-300" placeholder="Search"></div>
                <div class="col">
                    <ul>
                        <li><a href="add_item.php" id="addNewElement" class="btn btn-flat btn-sm bg-sky-300 p-2 m-1 float-right"> f3 جديد</a></li>
                        <li><a href="do/recost.php" id="" class="btn btn-flat btn-sm bg-sky-300 p-2 m-1 float-right">اعادة حساب متوسط التكلفة</a></li>
                        <li><button id="reindex" class="btn btn-flat btn-sm bg-sky-300 p-2 m-1 float-right">اعادة الفهرسة</button></li>
                    </ul>
                 
                </div>
                </div> 
                <div class="row"><div id="response-message"></div></div>
            </div>

            <div class="card-body" style="height: 550px;overflow:scroll">
         
                <div class="table-responsive" style="text-align:center" >
                    <table data-page-length='100'  id="horsTable" class="table table-responsive table-stripped table-hoverable table-bordered" > 
                        <thead class="">
                            <tr>
                                <th>م</th>
                                <th>رقم الصنف</th>
                                <th>الاسم</th>
                                <th>الكميه</th>
                                <th>الوحدة</th>
                                <th>الوصف</th>
                                <th>سعر البيع</th>
                                <th>سعر الشراء الأخير</th>
                                <th>سعر التكلفة</th>
                                <th>سعر السوق</th>
                                <th>عمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php

                        $limit = 1000;  // عدد العناصر في الصفحة الواحدة
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // الصفحة الحالية
                        $offset = ($page - 1) * $limit;
                        $resitm = $conn->query("SELECT * FROM myitems WHERE isdeleted = 0 LIMIT $limit OFFSET $offset");
                        $x=0;
                        while ($rowitm = $resitm->fetch_assoc()) {
                        $x++;
                        ?>
                        
                            <tr class="">
                                <td><b><?= $x ?></b></td>
                                <td><b><?= $rowitm['id'] ?></b></td>
                                <td><b><?= $rowitm['iname'] ?></b></td>
                                <td class="qty" data-row-id="<?= $rowitm['id'] ?>" data-original-qty="<?= $rowitm['itmqty'] ?>">
                                <b>
                                    <a class="btn btn-block btn-light border" id="item_qty_<?= $rowitm['id'] ?>" href="item_summery.php?id=<?= $rowitm['id'] ?>"><?= $rowitm['itmqty'] ?></a>
                                </b>
                                </td>
                                <td class="unit">
                                <select name="" id="item_unit_<?= $rowitm['id'] ?>" class="form-control" data-row-id="<?= $rowitm['id'] ?>">
                                    <?php
                                    $itemid = $rowitm['id'];
                                    $resunt = $conn->query("SELECT * from item_units where item_id = $itemid");
                                    while ($rowunt = $resunt->fetch_assoc()) {
                                    ?>
                                    <option value="<?= $rowunt['u_val']?>">
                                        <?php
                                        $unit_id = $rowunt['unit_id'];
                                        $rowuname = $conn->query("SELECT uname from myunits where id = $unit_id")->fetch_assoc();
                                        echo $rowuname['uname'];
                                        ?>
                                        [<?= $rowunt['u_val'] ?>]
                                    </option>
                                    <?php } ?>
                                </select>
                                </td>
                                <td><b><?= $rowitm['info'] ?></b></td>
                                <td><b><?= $rowitm['price1'] ?></b></td>
                                <td><b><?= $rowitm['last_price'] ?></b></td>
                                <td><b><?= $rowitm['cost_price'] ?></b></td>
                                <td><b><?= $rowitm['market_price'] ?></b></td>
                               
                                    <td>
                                    <a class="btn btn-flat btn-sm btn-warning" href="add_item.php?edit=<?= $rowitm['id'] ?>"><i class="fa fa-pen"></i></a>
                                
                                    <button type="button" class="btn btn-flat btn-sm btn-danger" data-toggle="modal" data-target="#deleteitm<?= $rowitm['id']?>">
                                <i class="fa fa-trash"></i>
                                </button>

                                
                                  <div class="modal fade" id="deleteitm<?= $rowitm['id']?>">
                                    <div class="modal-dialog">
                                    <div class="modal-content bg-danger">
                                        <div class="modal-header">
                                        <h4 class="modal-title">تحذير</h4>
                                        <a href="#">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </a>
                                        </div>

                                        <div class="modal-body">

                                            <p> هل تريد بالتأكيد الحذف <?= $rowitm['iname']?> </p>
                                               
                                            <form action="do/dodel_item.php?id=<?= $rowitm['id'] ?>" method="post">
                                            <input type="password" class="form-control" name="password" id="password">
                                          
                                            </div>
                                            <div class="modal-footer justify-content-between">
                                           <button type="submit" class="btn btn-flat btn-sm btn-outline-light btn-block" id="sub">حذف</button>
                                            </form>  
                                            
                                            
                                        </div>

                                    </div>
                                    <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>

                            
                                </td>
                            </tr>

                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="pagination">
                        <?php
                        $countitm = $conn->query("SELECT count(iname) from myitems where isdeleted = 0 ")->fetch_assoc();
                        $count = $countitm['count(iname)'] / $limit;
                        for ($i=1; $i < $count+1 ; $i++) {  ?>
                            <ul>
                                <li><a href="myitems.php?page=<?= $i?>"><?= $i?></a></li>
                            </ul>
                       <?php } ?>
                        
                    </div>

                </div>
            </div>
        </div>

        </div>



    </section>
</div>




<script>
$(document).ready(function() {
    // استمع لتغيرات في جميع قوائم الوحدة
    $('.unit select').change(function() {
        // الحصول على معرف الصف من السمة data-row-id
        var rowId = $(this).data('row-id');
        
        // الحصول على قيمة الوحدة المحددة
        var selectedUnitValue = $(this).val();
        
        // الحصول على عنصر الكمية للصف المحدد
        var qtyElement = $('#item_qty_' + rowId);
        
        // الحصول على الكمية الأصلية من السمة data-original-qty
        var originalQty = parseFloat($('.qty[data-row-id="' + rowId + '"]').data('original-qty'));
        
        // التحقق من أن قيمة الوحدة المحددة ليست صفر لتجنب القسمة على صفر
        if (selectedUnitValue != 0) {
            // حساب الكمية الجديدة
            var newQty = originalQty / selectedUnitValue;
            
            // تحديث الكمية المعروضة على الصفحة
            qtyElement.text(newQty.toFixed(2));
        }
    });
});
</script>
<script>
    $(document).ready(function() {
    $('#reindex').click(function() {
        $.ajax({
            url: 'js/ajax/reindex.php',
            type: 'POST', // or 'GET' depending on your PHP handling
            dataType: 'json', // change to 'text' if not returning JSON
            success: function(response) {
                // Handle success
                $('#response-message').html('Reindexing successful: ' + response.message);
            },
            error: function(xhr, status, error) {
                // Handle error
                $('#response-message').html('An error occurred: ' + error);
            }
        });
    });
});

</script>

<?php include('includes/footer.php') ?>
