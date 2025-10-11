<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">
                <?php
                if (isset($_GET['id'])) {
                    $itmid = intval($_GET['id']);
                    $sqlitm = "SELECT * FROM myitems WHERE id = '$itmid'";
                    $resitm = mysqli_query($conn, $sqlitm);
                    $rowitm = mysqli_fetch_assoc($resitm);
                }
                ?>
                <div class="card-header">
                    <h2 class="hors-head hazaz"> حركة صنف [ <?= $rowitm['iname'] ?> ]</h2>
                </div>

                <div class="card-body">
                    <!-- ✅ نموذج الفلاتر -->
                    <form method="GET" class="row mb-4">
                        <input type="hidden" name="id" value="<?= $itmid ?>">
                        <div class="col-md-3">
                            <label>من تاريخ:</label>
                            <input type="date" name="from" class="form-control" value="<?= $_GET['from'] ?? '' ?>">
                        </div>
                        <div class="col-md-3">
                            <label>إلى تاريخ:</label>
                            <input type="date" name="to" class="form-control" value="<?= $_GET['to'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <label style="visibility: hidden;">عرض</label>
                            <button type="submit" class="btn btn-primary btn-block">فلتر</button>
                        </div>
                    </form>

                    <!-- ✅ جدول البيانات -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="myTable" data-page-length="50">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>نوع العملية</th>
                                    <th>المخزن</th>
                                    <th>رقم الصنف</th>
                                    <th class="td5">كميه واردة</th>
                                    <th class="td6">كمية منصرفة</th>
                                    <th class="td7">رصيد الصنف بعد</th>
                                    <th>سعر</th>
                                    <th>تكلفة الصنف</th>
                                    <th>الربح</th>
                                    <th>id</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($_GET['id'])) {
                                    $where = "item_id = $itmid AND isdeleted = 0";

                                    if (!empty($_GET['from'])) {
                                        $from = $_GET['from'];
                                        $where .= " AND crtime >= '$from 00:00:00'";
                                    }
                                    if (!empty($_GET['to'])) {
                                        $to = $_GET['to'];
                                        $where .= " AND crtime <= '$to 23:59:59'";
                                    }
                                

                                    $resdet = $conn->query("SELECT * FROM fat_details WHERE $where ORDER BY crtime");
                                    if ($resdet->num_rows > 0) {
                                        $hash = 0;
                                        while ($rowdet = $resdet->fetch_assoc()) {
                                            $hash++;
                                            $itmid = $rowdet['item_id'];
                                            $storeid = $rowdet['det_store'];
                                            $protybe_id = $rowdet['pro_tybe'];
                                            $datetime = $rowdet['crtime'];

                                            // جلب اسم نوع العملية
                                            $protybe = $conn->query("SELECT pname FROM pro_tybes WHERE id = $protybe_id")->fetch_assoc();
                                            // جلب اسم المخزن
                                            $store = $conn->query("SELECT aname FROM acc_head WHERE id = $storeid")->fetch_assoc();
                                            // جلب الباركود
                                            $iname = $conn->query("SELECT barcode FROM myitems WHERE id = $itmid")->fetch_assoc();
                                ?>
                                            <tr>
                                                <td><?= $hash ?></td>
                                                <td><?= $datetime ?></td>
                                                <td>
                                                    <a href="<?php
                                                                $pro_id = $rowdet['pro_id'];
                                                                if ($protybe_id == 3 || $protybe_id == 4) {
                                                                    echo "sales.php?e=$pro_id";
                                                                }
                                                                ?>">
                                                        <?= $protybe['pname'] ?>
                                                    </a>
                                                </td>
                                                <td><?= $store['aname'] ?></td>
                                                <td><?= $iname['barcode'] ?></td>
                                                <td class="td5"><?= $rowdet['qty_in'] ?></td>
                                                <td class="td6"><?= $rowdet['qty_out'] ?></td>
                                                <td class="td7"></td>
                                                <td><?= $rowdet['price'] ?></td>
                                                <td><?= $rowdet['cost_price'] ?></td>
                                                <td><?= $rowdet['profit'] ?></td>
                                                <td></td>
                                            </tr>
                                <?php
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50">
                                    <th colspan="5">إجمالي الوارد:</th>
                                    <th><b id="sum_in"></b></th>
                                    <th>إجمالي المنصرف:</th>
                                    <th><b id="sum_out"></b></th>
                                    <th colspan="2">الرصيد الحالي:</th>
                                    <th class="bg-sky-200"><b id="sum_all"></b></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <!-- يمكن إضافة محتوى إضافي هنا -->
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let cumulativeSum = 0;

        $('#myTable tbody tr').each(function() {
            let qtyIn = parseFloat($(this).find('.td5').text()) || 0;
            let qtyOut = parseFloat($(this).find('.td6').text()) || 0;
            cumulativeSum += qtyIn - qtyOut;
            $(this).find('.td7').text(cumulativeSum.toFixed(2));
        });

        // مجموع الوارد
        let sum5 = 0;
        $('.td5').each(function() {
            sum5 += parseFloat($(this).text()) || 0;
        });
        $('#sum_in').text(sum5.toFixed(2));

        // مجموع المنصرف
        let sum6 = 0;
        $('.td6').each(function() {
            sum6 += parseFloat($(this).text()) || 0;
        });
        $('#sum_out').text(sum6.toFixed(2));

        // الرصيد النهائي
        $('#sum_all').text((sum5 - sum6).toFixed(2));

        // تلوين الخلايا السالبة
        $('.td7').each(function() {
            if (parseFloat($(this).text()) < 0) {
                $(this).addClass('bg-danger text-white');
            }
        });
    });
</script>

<?php include('includes/footer.php') ?>
