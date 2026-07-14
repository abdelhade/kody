<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php if ($_POST) {
    # code...
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">


    <div class="card">
    

        <div class="card-header">
            <form action="" method="post" id="myForm">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>اختر حساب</label>
                        <select class="select2 frst form-control" name="acc_id" id="acc" required>
                            <option value="0">اختر حساب</option>
                            <?php
                                $resacc = $conn->query("SELECT * FROM `acc_head` WHERE is_basic = 0 ORDER BY aname");
                            while ($rowacc = $resacc->fetch_assoc()) { ?>
                            <option value="<?= $rowacc['id'] ?>" <?= (isset($_POST['acc_id']) && $_POST['acc_id'] == $rowacc['id']) ? 'selected' : '' ?>>
                                {<?= $rowacc['code'] ?>}-<?= $rowacc['aname'] ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label>من تاريخ</label>
                        <input type="date" class="form-control" value="<?= isset($_POST['startdate']) ? $_POST['startdate'] : date('Y-m-01') ?>" name="startdate" required>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label>إلى تاريخ</label>
                        <input type="date" class="form-control" value="<?= isset($_POST['enddate']) ? $_POST['enddate'] : date('Y-m-d') ?>" name="enddate" required>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <label>رصيد الحساب</label>
                        <input type="text" class="form-control" readonly value="<?php 
                        if(isset($_POST['acc_id'])){
                            $ac_id = $_POST['acc_id'];
                            if ($ac_id != null && $ac_id != 0) { 
                                $rowaccbalance = $conn->query("SELECT balance from acc_head where id = $ac_id")->fetch_assoc();
                                echo ($rowaccbalance && isset($rowaccbalance['balance'])) ? number_format($rowaccbalance['balance'], 2) : '0.00';
                            } else {
                                echo '0.00';
                            }
                        } else {
                            echo '0.00';
                        }
                        ?>">
                    </div>
                </div>
                
                <div class="col-md-1">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">عرض</button>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="button" class="btn btn-outline-secondary btn-sm btn-block mb-1" id="printBtn">
                            <i class="fa fa-print"></i> طباعة
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm btn-block" id="exportExcel">
                            <i class="fa fa-table"></i> Excel
                        </button>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <div class="card">
        <div class="card-body">

            <div class="table table-responsive" id="horsTable">
                <b><?= $rowstg['company_name']?> / <?= $rowstg['company_tel']?> </b>
                <p><?= $rowstg['company_add']?></p>
                
            <center>
            <h3 class='hazaz'>كشف حساب <?php if(isset($_POST['acc_id'])){
                $ac_id = $_POST['acc_id'];
                if ($ac_id != null) { 
                $rowaccname = $conn->query("SELECT aname from acc_head where id = $ac_id")->fetch_assoc();
                if ($rowaccname != null) {
                echo $rowaccname['aname'];}
            };} ?></h3>   
            </center>
                <table class="table table-bordered" id="summaryTable" style="text-align:center">
                    <thead>
                        <tr>
                            <th>م</th>
                            <th>التاريخ</th>
                            <th>اسم العملية</th>
                            <th>مدين</th>
                            <th>دائن</th>
                            <th>رصيد متحرك</th>
                            <th>الحساب المقابل</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        
            <?php if($_POST){
                $acc = $_POST['acc_id'];
                $startdate = isset($_POST['startdate']) && !empty($_POST['startdate']) ? $_POST['startdate'] : '1970-01-01';
                $enddate = isset($_POST['enddate']) && !empty($_POST['enddate']) ? $_POST['enddate'] : date('Y-m-d');
                
                // حساب الرصيد الافتتاحي (الرصيد السابق قبل تاريخ البداية)
                $sql_open = "SELECT SUM(je.debit) - SUM(je.credit) as opening_balance 
                             FROM journal_entries je
                             JOIN journal_heads jh ON je.journal_id = jh.id
                             WHERE je.isdeleted = 0 AND jh.isdeleted = 0
                             AND je.account_id = $acc
                             AND jh.jdate < '$startdate'";
                $res_open = $conn->query($sql_open);
                $row_open = $res_open->fetch_assoc();
                $opening_balance = $row_open['opening_balance'] ? floatval($row_open['opening_balance']) : 0;
                
                $sqlacc = "SELECT 
                            je.id as je_id,
                            jh.jdate as pro_date,
                            je.debit,
                            je.credit,
                            jh.details as info,
                            oh.pro_tybe,
                            oh.id as edit_id,
                            pt.pname as pro_type_name,
                            (SELECT a.aname FROM journal_entries je2 
                             JOIN acc_head a ON je2.account_id = a.id 
                             WHERE je2.journal_id = je.journal_id AND je2.id != je.id AND je2.isdeleted = 0 LIMIT 1) as opposite_acc_name
                        FROM journal_entries je
                        JOIN journal_heads jh ON je.journal_id = jh.id
                        LEFT JOIN ot_head oh ON oh.id = COALESCE(NULLIF(jh.op_id, 0), NULLIF(je.op_id, 0), NULLIF(jh.op2, 0), NULLIF(je.op2, 0))
                        LEFT JOIN pro_tybes pt ON oh.pro_tybe = pt.id
                        WHERE je.isdeleted = 0 AND jh.isdeleted = 0
                        AND je.account_id = $acc
                        AND jh.jdate BETWEEN '$startdate' AND '$enddate'
                        ORDER BY jh.jdate ASC, jh.id ASC, je.id ASC";
                
                $resacc = $conn->query($sqlacc);
                
                $x = 0;
                $running_balance = $opening_balance;
                
                // عرض صف الرصيد الافتتاحي إذا كان موجوداً
                if ($opening_balance != 0) {
                    $op_debit = $opening_balance > 0 ? $opening_balance : 0;
                    $op_credit = $opening_balance < 0 ? abs($opening_balance) : 0;
                    echo "<tr class='bg-light'>
                            <td>-</td>
                            <td>$startdate</td>
                            <td><b>رصيد افتتاحي (ما قبل الفترة)</b></td>
                            <td class='td4'>" . ($op_debit > 0 ? number_format($op_debit, 2) : '0.00') . "</td>
                            <td class='td5'>" . ($op_credit > 0 ? number_format($op_credit, 2) : '0.00') . "</td>
                            <td class='td6'>" . number_format($running_balance, 2) . "</td>
                            <td>-</td>
                            <td>-</td>
                          </tr>";
                }
                
                if ($resacc && $resacc->num_rows > 0) {
                    while ($rowacc = $resacc->fetch_assoc()) {
                        $x++;
                        
                        $debit = floatval($rowacc['debit']);
                        $credit = floatval($rowacc['credit']);
                        
                        // حساب الرصيد المتحرك
                        $running_balance += ($debit - $credit);
                        
                        // تحديد الحساب المقابل
                        $opposite_acc = $rowacc['opposite_acc_name'];
                        
                        // تحديد رابط التعديل
                        $edit_link = "#";
                        $edit_id = $rowacc['edit_id'];
                        if ($edit_id) {
                            switch ($rowacc['pro_tybe']) {
                                case 1:
                                    $edit_link = "add_voucher.php?t=recive&edit=" . $edit_id;
                                    break;
                                case 2:
                                    $edit_link = "add_voucher.php?t=payment&edit=" . $edit_id;
                                    break;
                                case 3:
                                case 4:
                                case 10:
                                case 11:
                                case 12:
                                case 13:
                                case 14:
                                    $edit_link = "sales.php?edit_id=" . $edit_id;
                                    break;
                                case 9:
                                    $edit_link = "pos_barcode.php?edit=" . $edit_id;
                                    break;
                            }
                        }
                ?>
                        <tr>
                            <td><?= $x ?></td>
                            <td><?= $rowacc['pro_date'] ?></td>
                            <td>
                                <?php if($edit_link !== "#"): ?>
                                    <a href="<?= $edit_link ?>" title="تعديل">
                                        <?= htmlspecialchars($rowacc['pro_type_name'] ?? 'قيد محاسبي') ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($rowacc['pro_type_name'] ?? 'قيد محاسبي') ?>
                                <?php endif; ?>
                            </td>
                            <td class="td4"><?= $debit > 0 ? number_format($debit, 2) : '0.00' ?></td>
                            <td class="td5"><?= $credit > 0 ? number_format($credit, 2) : '0.00' ?></td>
                            <td class="td6"><?= number_format($running_balance, 2) ?></td>
                            <td><?= htmlspecialchars($opposite_acc ?? '') ?></td>
                            <td><?= htmlspecialchars($rowacc['info'] ?? '') ?></td>
                        </tr>
                <?php 
                    }
                } else {
                    if ($opening_balance == 0) {
                        echo "<tr><td colspan='8' style='text-align:center'>لا توجد حركات في هذه الفترة</td></tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='8' style='text-align:center'><b>ابدأ اختيار الحساب و حدد التاريخ</b></td></tr>";
            } 
            ?>
                        
                    </tbody>
                    <tfoot>
                        <tr class="bg-sky-100" style="font-size:20px">
                            <th></th>
                            <th></th>
                            <th>اجمالي مدين</th>
                            <th class="sumth4"></th>
                            <th>اجمالي دائن</th>
                            <th class="sumth5"></th>
                            <th>صافي الحركة</th>
                            <th class="net"></th>
                        </tr>
                    </tfoot>

                </table>
            </div>
            </div>
        


            <div class="card-footer"></div>    
            </div>
            </div>
              </section>
                  </div>


<script>
  $(document).ready(function() {
    $('#acc').select2();
    
    // تفعيل DataTables مع pagination
    $('#summaryTable').DataTable({
        "pageLength": 50,
        "language": {
            "url": "/focus/plugins/datatables-bs4/i18n/Arabic.json"
        },
        "order": [], // عدم الترتيب الافتراضي
        "columnDefs": [
            { "orderable": false, "targets": [0, 7] } // تعطيل الترتيب للعمود الأول والأخير
        ]
    });
});
</script>
<script>
    document.getElementById("myForm").addEventListener("submit", function(event) {
        var selectedValue = document.getElementById("acc").value;
        if (selectedValue === "0") {
            alert("من فضلك اختار حساب");
            event.preventDefault(); // Prevent form submission
        }
    });
</script>

<script>
$(document).ready(function() {
    // حساب الرصيد المتحرك
    var cumulativeSum = 0;
    $('#summaryTable tbody tr').each(function() {
        var td4Text = $(this).find('.td4').text().replace(/,/g, '');
        var td5Text = $(this).find('.td5').text().replace(/,/g, '');
        var td4Value = parseFloat(td4Text);
        var td5Value = parseFloat(td5Text);
        
        if (!isNaN(td4Value) && !isNaN(td5Value)) {
            cumulativeSum += td4Value - td5Value;
            $(this).find('.td6').text(cumulativeSum.toFixed(2));
        }
    });

    // حساب الإجماليات
    var sum4 = 0;
    $(".td4").each(function() { 
        var val = $(this).text().replace(/,/g, '');
        sum4 += parseFloat(val) || 0; 
    });
    $(".sumth4").text(sum4.toFixed(2));

    var sum5 = 0;
    $(".td5").each(function() { 
        var val = $(this).text().replace(/,/g, '');
        sum5 += parseFloat(val) || 0; 
    });
    $(".sumth5").text(sum5.toFixed(2));

    $(".net").text((sum4 - sum5).toFixed(2));
});
</script>
<script>


document.getElementById("printBtn").addEventListener("click", function() {
    var printContents = document.getElementById("horsTable").outerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
});
document.getElementById("exportExcel").addEventListener("click", function() {
    // Get table data
    var table = document.getElementById("horsTable");
    var rows = table.querySelectorAll("tr");
    var data = [];

    rows.forEach(function(row) {
        var rowData = [];
        row.querySelectorAll("td").forEach(function(cell) {
            rowData.push(cell.innerText);
        });
        data.push(rowData);
    });

    // Create Excel file
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.aoa_to_sheet(data);
    XLSX.utils.book_append_sheet(wb, ws, "horsTable");

    // Save Excel file
    var wbout = XLSX.write(wb, {bookType: "xlsx", type: "array"});
    var blob = new Blob([wbout], {type: "application/octet-stream"});
    var fileName = "horsTable.xlsx";

    // Trigger file download
    if (typeof window.navigator.msSaveBlob !== "undefined") {
        // For IE
        window.navigator.msSaveBlob(blob, fileName);
    } else {
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.style.display = "none";
        a.href = url;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
    }
});


</script>
                      <?php include('includes/footer.php') ?>





