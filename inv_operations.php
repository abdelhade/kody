<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<style>
    #msg{
        display: none;
        position: absolute;
        bottom: 20px;
        left: 20px;
        width: 350px;
        height: 70px;
        padding: 20px;
        font-size: 25px;
        background: #00000080;
        color: white;
        text-align: center;
        line-height: 30px;
        z-index: 1000;
        
    }
</style>
<div id="msg" class="btn btn-xl btn-light border"></div>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="card">

            <?php
                $q = isset($_GET['q']) ? $_GET['q'] : '';
                $isAll = ($q === 'all');

                if (!$isAll) {
                    // الوضع الأصلي: فاتورة محددة بـ hash
                    if (!(isset($_GET['q']) && isset($_GET['h']))) {
                        echo $userErrorMassage;
                    } else {
                        $id   = $_GET['q'];
                        $hash = md5($id);
                        $h    = $_GET['h'];
                        if ($hash != $h) {
                            echo $userErrorMassage;
                        } else {
                            $resop = $conn->query("SELECT * FROM fat_details WHERE fatid = " . (int)$id . " AND isdeleted = 0");
                        }
                    }
                }

                if ($isAll || isset($resop)):
                    // بناء مصفوفة الصفوف
                    $rows = [];
                    if ($isAll) {
                        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                        $limit = 100;
                        $offset = ($page - 1) * $limit;
                        
                        $totalRes = $conn->query("SELECT count(*) as cnt FROM myitems WHERE isdeleted = 0");
                        $totalRow = $totalRes->fetch_assoc();
                        $totalPages = ceil($totalRow['cnt'] / $limit);

                        $resAll = $conn->query("SELECT * FROM myitems WHERE isdeleted = 0 ORDER BY id DESC LIMIT $limit OFFSET $offset");
                        while ($r = $resAll->fetch_assoc()) {
                            $rows[] = ['item' => $r, 'qty_in' => 1];
                        }
                    } else {
                        while ($rowop = $resop->fetch_assoc()) {
                            $itm  = (int)$rowop['item_id'];
                            $res2 = $conn->query("SELECT * FROM myitems WHERE id = $itm");
                            $r2   = $res2->fetch_assoc();
                            if ($r2) $rows[] = ['item' => $r2, 'qty_in' => $rowop['qty_in']];
                        }
                    }
            ?>

                <div class="card-header d-flex align-items-center gap-3 flex-wrap">
                    <h3 class="mb-0"><?= $isAll ? 'كل الأصناف' : 'العمليات علي الفاتورة' ?></h3>
                    <?php if ($isAll): ?>
                    <input type="text" id="inv-search" class="form-control form-control-sm" style="max-width:250px;" placeholder="بحث باسم الصنف أو الباركود...">
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <form action="print/br2538.php" method="post" target="_blank">
    <div class="mb-2"><button type="submit" class="btn btn-success btn-sm">طباعة الباركود</button></div>
    <table class="font-thin table table-hover table-bordered table-sm" id="inv-table">
        <thead>
            <tr>
                <th>#</th>
                <th>كود الصنف</th>
                <th>barcode</th>
                <th>اسم الصنف</th>
                <th>سعر الشراء الاخير</th>
                <th>سعر البيع <span class="text-slate-500 font-thin text-sm">(قابل للتغيير)</span></th>
                <th>العدد المطلوب طباعته</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $x => $row):
                $rowop2 = $row['item'];
                $iid    = (int)$rowop2['id'];
                $rowunt = $conn->query("SELECT unit_barcode FROM item_units WHERE item_id = $iid LIMIT 1")->fetch_assoc();
                $dispCode = !empty($rowunt['unit_barcode']) ? $rowunt['unit_barcode'] : $rowop2['barcode'];
                $searchVal = strtolower($rowop2['iname'] . ' ' . $rowop2['barcode'] . ' ' . $dispCode);
            ?>
            <tr id="item-<?= $iid ?>" data-search="<?= htmlspecialchars($searchVal, ENT_QUOTES) ?>">
                <th><?= $x + 1 ?></th>
                <th><input readonly type="text" value="<?= htmlspecialchars($dispCode) ?>" name="code[]"></th>
                <th><input readonly type="text" value="<?= htmlspecialchars($rowop2['barcode']) ?>" name="barcode[]"></th>
                <th><input readonly type="text" value="<?= htmlspecialchars($rowop2['iname']) ?>" name="iname[]"></th>
                <th><input readonly type="text" value="<?= $rowop2['last_price'] ?>" name="last_price[]"></th>
                <th><input type="number" step="0.01" value="<?= $rowop2['price1'] ?>" name="price[]" onchange="updatePrice(<?= $iid ?>, this.value)" class="price"></th>
                <th><input type="number" value="<?= $isAll ? 0 : (int)$row['qty_in'] ?>" name="qty[]"></th>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>

<?php if ($isAll && isset($totalPages) && $totalPages > 1): ?>
    <nav aria-label="Page navigation" class="mt-3">
      <ul class="pagination justify-content-center flex-wrap">
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
          <a class="page-link" href="?q=all&page=<?= $page - 1 ?>">السابق</a>
        </li>
        
        <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
            <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
              <a class="page-link" href="?q=all&page=<?= $p ?>"><?= $p ?></a>
            </li>
        <?php endfor; ?>
        
        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
          <a class="page-link" href="?q=all&page=<?= $page + 1 ?>">التالي</a>
        </li>
      </ul>
    </nav>
<?php endif; ?>

                    </div>
                </div>

                <?php endif; ?>



            
        </div>
    </section>
</div>
<?php include('includes/footer.php') ?>


<script>
    function updatePrice(itemId, newPrice) {
    $.ajax({
        url: 'js/ajax/update_price.php',
        method: 'POST',
        data: { id: itemId, price: newPrice },
        success: function(response) {
            console.log('Price updated successfully');
            $('#msg').html("تم تغيير السعر بنجاح").show();
            setTimeout(function() {
            $('#msg').hide();}, 3000);
        },
        error: function(xhr, status, error) {
            console.error('Error updating price:', error);
        }
    });
}
$(document).ready(function() {
    $('input[name^="price"]').on('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            var nextRow = $(this).closest('tr').next('tr');
            if (nextRow.length) {
                nextRow.find('input[name^="price"]').focus();
            }
        }
    });

    // filter للـ q=all
    $('#inv-search').on('input', function() {
        var val = $(this).val().toLowerCase();
        $('#inv-table tbody tr').each(function() {
            var s = $(this).data('search') || '';
            $(this).toggle(s.indexOf(val) !== -1);
        });
    });
});
</script>