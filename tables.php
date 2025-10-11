<?php include('includes/header.php') ?>
<?php
$sql = "CREATE TABLE IF NOT EXISTS tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tname VARCHAR(255) NOT NULL,
    table_case INT NOT NULL DEFAULT 0,
    crtime DATETIME DEFAULT CURRENT_TIMESTAMP,
    mdtime DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    isdeleted TINYINT(1) NOT NULL DEFAULT 0,
    branch VARCHAR(255) DEFAULT NULL,
    tatnet VARCHAR(255) DEFAULT NULL
)";
$conn->query($sql);
?>

<div class="nav">

</div>
<div class="row">
    <div class="col-1">

    </div>
    <div class="col">
<div class="card">
    <div class="card-head">
    <center>
        <p class="bg-zinc-50 text-lg">ادارة الطاولات</p>
    </center>
    </div>

    <div class="card-body overflow-scroll max-h-96">
        <div class="btn bg-sky-400 p-4 m-4">
        النقيب</div>
        <div class="btn bg-sky-400 p-4 m-4">
        2</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        <div class="btn bg-red-300 p-4 m-4">
        3</div>
        
    </div>
   
   
   
    <div class="card-footer">
        <table>
            <thead>
                <tr>
                    <th class="border-4 m-2 p-2">قيمه الطلب</th>
                    <th class="border-4 m-2 p-2">خصم</th>
                    <th class="border-4 m-2 p-2">اضافي</th>
                    <th class="border-4 m-2 p-2">صافي</th>
                    <th class="border-4 m-2 p-2">مسدد</th>
                    <th class="border-4 m-2 p-2">باقي</th>
                </tr>
                
                <tr>
                    <th class="border-4 m-2 p-2 text-blue-700">0.00</th>
                    <th class="border-4 m-2 p-2 text-blue-700">0.00</th>
                    <th class="border-4 m-2 p-2 text-blue-700">0.00</th>
                    <th class="border-4 m-2 p-2 text-blue-700">0.00</th>
                    <th class="border-4 m-2 p-2 text-blue-700">0.00</th>
                    <th class="border-4 m-2 p-2 text-blue-700">0.00</th>
                </tr>
            </thead>
        </table>
        <div class="row">
            <div class="col-8 border-4 p-4">
                <table class="table table-responsive table-bordered">
                    <thead>
                        <tr>
                        <th>م</th>
                        <th class="w-80">اسم الصنف</th>
                        <th class="w-64">ملاحظات</th>
                        <th>كميه</th>
                        <th>سعر</th>
                        <th>القيمه</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <td>1</td>
                        <td>شاي</td>
                        <td></td>
                        <td>كميه</td>
                        <td>سعر</td>
                        <td>القيمه</td>
                        </tr>
                        <tr>
                        <td>م</td>
                        <td>اسم الصنف</td>
                        <td>ملاحظات</td>
                        <td>كميه</td>
                        <td>سعر</td>
                        <td>القيمه</td>
                        </tr>
                        <tr>
                        <td>م</td>
                        <td>اسم الصنف</td>
                        <td>ملاحظات</td>
                        <td>كميه</td>
                        <td>سعر</td>
                        <td>القيمه</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-4 border-4 p-4">
                <div class="row">
                    <div class="col-12 btn bg-sky-200 m-1">طلب جديد</div>
                    <div class="col-4 btn bg-zinc-200 m-1">سداد نقدي</div>
                    <div class="col-4 btn bg-zinc-200 m-1">طباعه التحضير</div>
                    <div class="col-4 btn bg-zinc-200 m-1">طباعه </div>
               
                </div>
            </div>
        </div>
    </div>

</div>
</div>

</div>

<?php include('includes/footer.php') ?>
