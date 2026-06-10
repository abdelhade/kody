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

                <div class="card-header d-flex align-items-center gap-3 flex-wrap justify-content-between">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <h3 class="mb-0"><?= $isAll ? 'كل الأصناف' : 'العمليات علي الفاتورة' ?></h3>
                        <button class="btn btn-sm btn-outline-info btn-toggle-panel" type="button" data-target="#filtersPanel">
                            <i class="fas fa-filter"></i> فلاتر
                        </button>
                        <button class="btn btn-sm btn-outline-primary btn-toggle-panel" type="button" data-target="#bulkPricingPanel">
                            <i class="fas fa-tags"></i> تسعير مجمع
                        </button>
                    </div>
                </div>
                
                <div style="display: none;" id="filtersPanel">
                    <div class="card-body border-bottom bg-light">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="small text-muted mb-1">بحث</label>
                                <input type="text" id="inv-search" class="form-control form-control-sm" placeholder="بحث باسم الصنف أو الباركود...">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small text-muted mb-1">المجموعة</label>
                                <select id="inv-group-filter" class="form-control form-control-sm">
                                    <option value="">-- كل المجموعات --</option>
                                    <?php
                                    $resgroup = $conn->query('SELECT * FROM item_group WHERE isdeleted = 0');
                                    while ($rowgroup = $resgroup->fetch_assoc()) {
                                        echo '<option value="' . (int)$rowgroup['id'] . '">' . htmlspecialchars($rowgroup['gname'], ENT_QUOTES) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: none;" id="bulkPricingPanel">
                    <div class="card-body border-bottom bg-light">
                        <div class="row align-items-end">
                            <div class="col-md-2 mb-2">
                                <label class="small text-muted mb-1">السعر المرجعي</label>
                                <select id="bp-base-price" class="form-control form-control-sm">
                                    <option value="last_price">سعر الشراء الأخير</option>
                                    <option value="cost_price">سعر الشراء المتوسط</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="small text-muted mb-1">سعر النتيجة</label>
                                <select id="bp-target-price" class="form-control form-control-sm">
                                    <option value="price1">سعر البيع</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="small text-muted mb-1">طريقة التسعير</label>
                                <select id="bp-method" class="form-control form-control-sm">
                                    <option value="percent">نسبة مئوية (%)</option>
                                    <option value="value">قيمة ثابتة</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="small text-muted mb-1">القيمة المضافة</label>
                                <input type="number" id="bp-amount" class="form-control form-control-sm" step="0.01" value="0">
                            </div>
                            <div class="col-md-4 mb-2">
                                <button type="button" id="btn-bp-apply" class="btn btn-sm btn-primary"><i class="fas fa-calculator"></i> تطبيق</button>
                                <button type="button" id="btn-bp-confirm" class="btn btn-sm btn-success"><i class="fas fa-save"></i> تأكيد وحفظ</button>
                            </div>
                        </div>
                    </div>
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
                <th>سعر الشراء المتوسط</th>
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
            <tr id="item-<?= $iid ?>" class="inv-row" data-search="<?= htmlspecialchars($searchVal, ENT_QUOTES) ?>" data-group="<?= (int)$rowop2['group1'] ?>" data-item-id="<?= $iid ?>">
                <th><?= $x + 1 ?></th>
                <th><input readonly type="text" value="<?= htmlspecialchars($dispCode) ?>" name="code[]" class="form-control form-control-sm border-0 bg-transparent"></th>
                <th><input readonly type="text" value="<?= htmlspecialchars($rowop2['barcode']) ?>" name="barcode[]" class="form-control form-control-sm border-0 bg-transparent"></th>
                <th><input readonly type="text" value="<?= htmlspecialchars($rowop2['iname']) ?>" name="iname[]" class="form-control form-control-sm border-0 bg-transparent"></th>
                <th><input readonly type="text" value="<?= (float)$rowop2['last_price'] ?>" name="last_price[]" class="form-control form-control-sm border-0 bg-transparent base-last-price"></th>
                <th><input readonly type="text" value="<?= (float)$rowop2['cost_price'] ?>" name="cost_price[]" class="form-control form-control-sm border-0 bg-transparent base-cost-price"></th>
                <th><input type="number" step="0.01" value="<?= (float)$rowop2['price1'] ?>" name="price[]" onchange="updatePrice(<?= $iid ?>, this.value)" class="form-control form-control-sm price target-price"></th>
                <th><input type="number" value="<?= $isAll ? 0 : (int)$row['qty_in'] ?>" name="qty[]" class="form-control form-control-sm"></th>
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
    $('.btn-toggle-panel').on('click', function() {
        var target = $(this).data('target');
        $(target).slideToggle();
    });

    $('input[name^="price"]').on('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            var nextRow = $(this).closest('tr').next('tr');
            if (nextRow.length) {
                nextRow.find('input[name^="price"]').focus();
            }
        }
    });

    // Filter Logic
    function applyFilters() {
        var textVal = $('#inv-search').val().toLowerCase();
        var groupVal = $('#inv-group-filter').val();
        
        $('#inv-table tbody tr.inv-row').each(function() {
            var s = $(this).data('search') || '';
            var g = $(this).data('group') || '';
            
            var matchText = s.indexOf(textVal) !== -1;
            var matchGroup = (groupVal === "" || g.toString() === groupVal);
            
            if (matchText && matchGroup) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    $('#inv-search').on('input', applyFilters);
    $('#inv-search, #bp-amount').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if ($(this).attr('id') === 'bp-amount') {
                $('#btn-bp-apply').click();
            }
        }
    });
    $('#inv-group-filter').on('change', applyFilters);

    // Bulk Pricing Logic
    $('#btn-bp-apply').on('click', function() {
        var basePriceField = $('#bp-base-price').val();
        var method = $('#bp-method').val();
        var amount = parseFloat($('#bp-amount').val()) || 0;

        $('#inv-table tbody tr.inv-row:visible').each(function() {
            var baseInput = basePriceField === 'last_price' ? $(this).find('.base-last-price') : $(this).find('.base-cost-price');
            var baseVal = parseFloat(baseInput.val()) || 0;
            var newPrice = baseVal;
            
            if (method === 'percent') {
                newPrice = baseVal + (baseVal * (amount / 100));
            } else {
                newPrice = baseVal + amount;
            }
            
            $(this).find('.target-price').val(newPrice.toFixed(2));
        });
        
        $('#msg').html("تم تطبيق الحسابات على الجدول، اضغط تأكيد للحفظ").show();
        setTimeout(function() { $('#msg').hide(); }, 4000);
    });

    $('#btn-bp-confirm').on('click', function() {
        if (!confirm("سيتم تغيير جميع الأصناف الظاهرة في الفلتر، هل أنت متأكد؟")) return;

        var itemsToUpdate = [];
        $('#inv-table tbody tr.inv-row:visible').each(function() {
            var id = $(this).data('item-id');
            var price = parseFloat($(this).find('.target-price').val()) || 0;
            itemsToUpdate.push({ id: id, price1: price });
        });

        if (itemsToUpdate.length === 0) {
            alert("لا توجد أصناف ظاهرة لتحديثها.");
            return;
        }

        $.ajax({
            url: 'js/ajax/bulk_update_prices.php',
            method: 'POST',
            data: { items: itemsToUpdate },
            success: function(res) {
                console.log(res);
                var data = JSON.parse(res);
                if (data.status === 'success') {
                    $('#msg').html("تم حفظ " + data.updated_count + " صنف بنجاح").show();
                    setTimeout(function() { $('#msg').hide(); }, 3000);
                } else {
                    alert("حدث خطأ: " + data.message);
                }
            },
            error: function(xhr) {
                alert("تعذر الاتصال بالخادم.");
            }
        });
    });
});
</script>