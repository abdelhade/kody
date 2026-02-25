<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <div class="row">
                <div class="col"><h3>الاصناف</h3></div>
                <div class="col-md-6"><input type="text" id="search" class="form-control frst" placeholder="بحث..."></div>
                <div class="col">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="add_item.php" id="addNewElement" class="btn btn-primary btn-sm"> f3 جديد</a>
                        <a href="do/recost.php" class="btn btn-secondary btn-sm">اعادة حساب</a>
                        <button id="reset-manual-prices" class="btn btn-warning btn-sm">إعادة تعيين الحماية</button>
                        <button id="reindex" class="btn btn-secondary btn-sm">اعادة الفهرسة</button>
                    </div>
                </div>
                </div> 
                <div class="row"><div id="response-message"></div></div>
            </div>

            <div class="card-body">
         
                <div class="table-responsive">
                    <table data-page-length='100'  id="horsTable" class="table table-striped"> 
                        <thead>
                            <tr>
                                <th>م</th>
                                <th>رقم الصنف</th>
                                <th>الاسم</th>
                                <th>الكميه</th>
                                <th>الوحدة</th>
                                <th>الوصف</th>
                                <th>سعر البيع</th>
                                <th>سعر الشراء</th>
                                <th>سعر التكلفة</th>
                                <th>سعر السوق</th>
                                <th>عمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php

                        $limit = 100;  // عدد العناصر في الصفحة الواحدة
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // الصفحة الحالية
                        $offset = ($page - 1) * $limit;
                        $resitm = $conn->query("SELECT * FROM myitems WHERE isdeleted = 0 LIMIT $limit OFFSET $offset");
                        $x=0;
                        while ($rowitm = $resitm->fetch_assoc()) {
                        $x++;
                        ?>
                        
                            <tr>
                                <td><?= $x ?></td>
                                <td><?= $rowitm['id'] ?></td>
                                <td><b><?= $rowitm['iname'] ?></b></td>
                                <td class="qty" data-row-id="<?= $rowitm['id'] ?>" data-original-qty="<?= $rowitm['itmqty'] ?>">
                                    <a class="btn btn-sm btn-light" id="item_qty_<?= $rowitm['id'] ?>" href="item_summery.php?id=<?= $rowitm['id'] ?>"><?= $rowitm['itmqty'] ?></a>
                                </td>
                                <td class="unit">
                                <select name="" id="item_unit_<?= $rowitm['id'] ?>" class="form-control form-control-sm" data-row-id="<?= $rowitm['id'] ?>">
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
                                <td><?= $rowitm['info'] ?></td>
                                <td><b><?= $rowitm['price1'] ?></b></td>
                                <td><b><?= $rowitm['last_price'] ?></b></td>
                                <td><b><?= $rowitm['cost_price'] ?></b></td>
                                <td><b><?= $rowitm['market_price'] ?></b></td>
                               
                                    <td>
                                        <a class="btn btn-warning btn-sm" href="add_item.php?edit=<?= $rowitm['id'] ?>"><i class="fa fa-pen"></i></a>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteitm<?= $rowitm['id']?>">
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

                </div>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <?php
                        $countitm = $conn->query("SELECT COUNT(*) as total FROM myitems WHERE isdeleted = 0")->fetch_assoc();
                        $total_items = $countitm['total'];
                        $total_pages = ceil($total_items / $limit);
                        
                        // زر السابق
                        if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="myitems.php?page=<?= $page - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">&laquo;</span>
                            </li>
                        <?php endif;
                        
                        // عرض أرقام الصفحات
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        // الصفحة الأولى
                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="myitems.php?page=1">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif;
                        endif;
                        
                        // الصفحات المحيطة
                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="myitems.php?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor;
                        
                        // الصفحة الأخيرة
                        if ($end_page < $total_pages): 
                            if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="myitems.php?page=<?= $total_pages ?>"><?= $total_pages ?></a>
                            </li>
                        <?php endif;
                        
                        // زر التالي
                        if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="myitems.php?page=<?= $page + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">&raquo;</span>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            عرض <?= ($offset + 1) ?> - <?= min($offset + $limit, $total_items) ?> من <?= $total_items ?> صنف
                        </small>
                    </div>
                </nav>
            </div>
        </div>

        </div>

    </section>
</div>


<script>
$(document).ready(function() {
    // إعادة تعيين حماية الأسعار اليدوية
    $('#reset-manual-prices').click(function() {
        if (confirm('هل أنت متأكد من إعادة تعيين حماية الأسعار؟ سيتم إعادة حساب جميع الأسعار عند الضغط على إعادة حساب')) {
            $.ajax({
                url: 'do/reset_manual_prices.php',
                method: 'POST',
                success: function(response) {
                    alert('تم إعادة التعيين بنجاح');
                    location.reload();
                },
                error: function() {
                    alert('حدث خطأ');
                }
            });
        }
    });
    
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
