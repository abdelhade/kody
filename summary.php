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
                <table class="table table-bordered"  style="text-align:center" data-page-length='50'>
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
                $x=0;
                $sqlacc = "SELECT * FROM ot_head where acc1 = $acc or acc2 = $acc AND isdeleted = 0 order by pro_date ";
                $resacc= $conn->query($sqlacc);
                if (isset($resacc)) {
                    $x=0;
                while ($rowacc = $resacc->fetch_assoc()) {
                $x++;
                ?>
                        <tr>
                            <td><?= $x ?></td>
                            <td><?= $rowacc['pro_date']?></td>
                     
                            <td><?php $pro_tybe =  $rowacc['pro_tybe'];$row_tybe = $conn->query("SELECT pname FROM pro_tybes where id = $pro_tybe")->fetch_assoc();echo ($row_tybe && isset($row_tybe['pname'])) ? $row_tybe['pname'] : '';?></td>

                     
                            <td class="td4">
                            <?php if($rowacc['acc1'] == $acc){
                            $op_id = $rowacc['id'];
                            $rowdb = $conn->query("SELECT debit from journal_entries where (op_id = '$op_id' OR op2 = '$op_id') AND debit > 0 ")->fetch_assoc();echo ($rowdb && isset($rowdb['debit'])) ? $rowdb['debit'] : 0;}else{echo 0;}?>
                            </td>
                     
                            <td class="td5">
                            <?php if($rowacc['acc2'] == $acc){
                            $op_id = $rowacc['id'];
        
                            $rowcr = $conn->query("SELECT credit from journal_entries where (op_id = '$op_id' OR op2 = '$op_id') AND credit > 0 ")->fetch_assoc();echo ($rowcr && isset($rowcr['credit'])) ? $rowcr['credit'] : 0;}else{echo 0;}?>
                            
                            </td>
                     
                            <td class="td6"></td>

                     
                            <td><?php if($rowacc['acc2'] == $acc){
                            $acc_name = $rowacc['acc1'];
                            }elseif($rowacc['acc1'] == $acc){
                            $acc_name = $rowacc['acc2'];}
                            $rowaccname = $conn->query("SELECT * FROM acc_head where id = $acc_name")->fetch_assoc();
                            echo ($rowaccname && isset($rowaccname['aname'])) ? $rowaccname['aname'] : '';
                            ?></td>
                            
                            <td><?= $rowacc['info']?></td>
                            
                        </tr>
                    
                        <?php };?>
                        <tfoot>
                       
                        <?php }; 
                    
                    }else{echo "<b style='text-align:center'>ابدأ اختيار الحساب و حدد التاريخ</b>";} ?>
                        
                    </tbody>
                    <tr class="bg-sky-100" style="font-size:20px">
                            <th></th>
                            <th></th>
                            <th>اجمالي مدين</th>
                            <th class="sumth4"></th>
                            <th >اجمالي دائن</th>
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
    $('#acc').select2();});
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
    var cumulativeSum = 0;
    $('#horsTable tr').each(function() {
        var td4Value = parseFloat($(this).find('.td4').text());
        var td5Value = parseFloat($(this).find('.td5').text());
        if (!isNaN(td4Value) && !isNaN(td5Value)) { // Check if values are valid numbers
            cumulativeSum += td4Value - td5Value;
            $(this).find('.td6').text(cumulativeSum);
        }
    });
});


var sum4 = 0;
$(".td4").each(function() { sum4 += parseFloat($(this).text()) || 0; });
$(".sumth4").text(sum4);

var sum5 = 0;
$(".td5").each(function() { sum5 += parseFloat($(this).text()) || 0; });
$(".sumth5").text(sum5);

$(".net").text(sum4 - sum5);



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





