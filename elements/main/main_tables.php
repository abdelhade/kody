

<?php if($role['sid_accounts'] == 1){?>
    
    <div class="col-md-4 float-left">
      <div class="card card-fuchia hadi-fade-in">
<div class="card-header border-2">
<h4>اخر حسابات تم انشاءها</h4>
</div>
<div class="card-body table-responsive p-0">
<table class="table table-striped table-bordered p-0">
<thead>
<tr>
<th>م</th>
<th>اسم الحساب</th>
<th>رصيد الحساب</th>
<th>يتبع ل</th>
</tr>
</thead>
<tbody>
  <?php
  $resacc= $conn->query("SELECT * FROM acc_head order by id desc limit 5");
  $x = 0;
  while ($rowacc= $resacc->fetch_assoc()) {
   $x++;
   ?>
<tr>
<td><?= $x ?></td>
<td><?= $rowacc['code']?>-<?= $rowacc['aname']?></td>
<td><?= $rowacc['balance']?></td>
<td><?php 
$p = $rowacc['parent_id']; 
$pname = ($p > 0) ? $conn->query("SELECT aname FROM acc_head WHERE id = $p")->fetch_assoc()['aname'] : "-";
echo $pname;
?>
</td>

<?php } ?>
</tbody>
</table>
</div>

</div>
    </div>
    <?php } ?>





    <?php if($role['sid_accounts'] == 1){?>
    <div class="col-md-4 float-left">
      <div class="card card-fuchia hadi-fade-in1">
<div class="card-header border-2">
        <h3>محلل العمل اليومي</h3>
        </div>
        <div class="card-body table-responsive p-0">
         
<table class="table table-striped table-bordered p-0">
           <thead>                   
                        <tr>
                            <th>#</th>
                            <th>تاريخ</th>
                            <th>اسم العملية</th>
                            <th>قيمة العملية</th>
                            <th>user</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
            $x= 0;
            $resop = $conn->query("SELECT * FROM ot_head where isdeleted = 0  order by id desc limit 5");
            while ($rowop = $resop->fetch_assoc()) {
                $x++;
            ?>
                    <tr>
                            <td><?= $x ?></td>
                            <td><?= $rowop['pro_date'] ?></td>
                            <td><?php 
                            $tybe = $rowop['pro_tybe'];$rowtybe = $conn->query("SELECT pname from pro_tybes where id = $tybe ")->fetch_assoc();echo $rowtybe['pname']; ?></td>
                            <td><?= $rowop['pro_value'] ?></td>
                            <td><?php 
                            $user = $rowop['user'];$rowuser = $conn->query("SELECT uname from users where id = $user ")->fetch_assoc();echo $rowuser['uname']; ?></td>
                         </tr>
        <?php }?>
                    </tbody>
            </table>
        </div>
      
        </div>
    </div>
    <?php } ?>





    <?php if($role['sid_stock'] == 1){?>
    <div class="col-md-4 float-left">
    <div class="card card-fuchia hadi-fade-in1 ">
<div class="card-header border-2 hadi-fade-in1">
<h4 class="hadi-fade-in"> اخر اصناف تم انشاءها</h4>
</div>
<div class="card-body table-responsive p-0">
<table class="table table-striped table-bordered">
<thead>
<tr>
<th>م</th>
<th>اسم الصنف</th>
<th>رصيد الصنف</th>
</tr>
</thead>
<tbody>
  <?php
  $resitm= $conn->query("SELECT * FROM myitems order by id desc limit 5");
  $x = 0;
  while ($rowitm= $resitm->fetch_assoc()) {
   $x++;
   ?>
<tr>
<td><?= $x ?></td>
<td ><?= $rowitm['id']?>-<?= $rowitm['iname']?></td>
<td><?= $rowitm['itmqty']?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>

</div>
    </div>
    <?php } ?>



    <div class="col-md-4 float-left ">
      <div class="card card-fuchia hadi-fade-in2">
        <div class="card-header">
          <h3>آخر 5 زيارات</h3>

        </div>
        <div class="card-body">
          <div class="table table-responsive">
            <table class="table table-responsive talbe-hoverable table-stripped table-bordered">
              <thead>
                <tr>
                  <th>م</th>
                  <th>الاسم</th>
                  <th>الوقت</th>
                </tr>
              </thead>
              <tbody>
                <?php
              $restime = $conn->query("SELECT * FROM session_time order by crtime desc limit 5");
              $d=0;
              while ($rowtime = $restime->fetch_assoc() ) { 
                $d++;
                ?>
                <tr>
                  <td><?= $d ?></td>
                  <td><?php $usid = $rowtime['user'];
                  echo ($uname = $conn->query("SELECT uname FROM users WHERE id = $usid")) ? $uname->fetch_assoc()['uname'] : "__"; ?></td>
                  <td><?= $rowtime['crtime'] ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

            </div>
              </div>
              </div>




              <div class="col-md-4 float-left ">
      <div class="card card-fuchia hadi-fade-in2">
        <div class="card-header">
          <h3>المبيعات</h3>

        </div>
        <div class="card-body">
          <div class="table table-responsive">
            <table class="table table-responsive talbe-hoverable table-stripped table-bordered">
              <thead>
              <?php 
              $sales1 = $conn->query("SELECT pro_value FROM ot_head where pro_tybe = 3 OR pro_tybe = 9 AND isdeleted = 0 order by id desc")->fetch_assoc();
              $sales2 = $conn->query("SELECT sum(pro_value) FROM ot_head where pro_tybe = 3 OR pro_tybe = 9 AND isdeleted = 0  AND pro_date BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()")->fetch_assoc();
              $sales3 = $conn->query("SELECT sum(pro_value) FROM ot_head where pro_tybe = 3 OR pro_tybe = 9 AND isdeleted = 0  AND pro_date BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()")->fetch_assoc();
              $sales4 = $conn->query("SELECT sum(pro_value) FROM ot_head where pro_tybe = 3 OR pro_tybe = 9 AND isdeleted = 0  ")->fetch_assoc();
              
              
              ?>
                <tr>
                  <th>الاسم</th>
                  <th>المبلغ</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>آخر فاتورة مبيعات</td>
                  <td><?php if($sales1 != null){echo $sales1['pro_value'];} ?></td>
                </tr>
                <tr>
                  <td>اجمالي المبيعات آخر اسبوع</td>
                  <td><?= $sales2['sum(pro_value)']  ?></td>
                </tr>
                <tr>
                  <td>اجمالي المبيعات آخر 10 ايام</td>
                  <td><?= $sales3['sum(pro_value)']  ?></td>
                </tr>
                <tr>
                  <td>اجمالي المبيعات خلال الفترة</td>
                  <td><?= $sales4['sum(pro_value)'] ?></td>
                </tr>
              </tbody>
            </table>
          </div>

            </div>
              </div>
              </div>




              

              <div class="col-md-4 float-left ">
      <div class="card card-fuchia hadi-fade-in2">
        <div class="card-header">
          <h3>الزيارات الاخيره</h3>
        </div>
        <div class="card-body">
          <div class="table table-responsive">
            <table class="table table-responsive table-hoverable table-stripped">
              <thead>
                <tr>
                  <th>م</th>
                  <th>الاسم</th>
                  <th>الوقت</th>
                  <th>التاريخ</th>
                  
                </tr>
              </thead>
              <tbody>
                <?php
              $resres = $conn->query("SELECT * FROM reservations order by id desc limit 5");
              $d=0;
              while ($rowres = $resres->fetch_assoc() ) { 
                $d++;
                ?>
                
                <tr>
                  <td><?= $d ?></td>
                  <td>
                    <?php $clid = $rowres['client'];
                  echo ($rowcl = $conn->query("SELECT name FROM clients WHERE id = $clid")) ? $rowcl->fetch_assoc()['name'] : "__"; ?></td>
                  <td>-<?= $rowres['time'] ?>-</td>
                  <td>-<?= $rowres['date'] ?>-</td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
  </div>
              </div>
              </div>






              <?php if($role['sid_rents'] == 1){?>
              <div class="col-md-8 float-left ">
                <div class="card card-fuchia hadi-fade-in3">
            <div class="card-header border-2">
            <h4>الاقساط المستحقة</h4>
            </div>
            <div class="card-body table-responsive p-0">
            <div class="table table-responsive">
        <table class="table table-bordered table-hover" id="">
        <thead>
            <tr>
            
                <th class="">م</th>
                <th class="">اسم الوحده</th>
                <th class="">اسم العميل</th>
                <th class="">تاريخ الاستحقاق</th>
                <th class="">المستحق</th>
                <th class="">المدفوع</th>
                <th class="">الحالة</th>
                
            </tr>
        </thead>
        <tbody>
            <?php 
        
            $x=0;
            $resins = $conn->query("SELECT * FROM myinstallments WHERE ins_date < NOW() ORDER BY ins_date LIMIT 5;");
            while ($rowins = $resins->fetch_assoc()) {
            $x++;
            ?>


            <tr class="
            
                ">
                <td><?= $x ?></td>
                <td>
                <?php echo $conn->query("SELECT * FROM acc_head where id = {$rowins['rent_id']}")->fetch_assoc()['aname']; ?>
                </td>
                <td>
                <?php echo $conn->query("SELECT * FROM acc_head where id = {$rowins['cl_id']}")->fetch_assoc()['aname']; ?>
                </td>
                <td><?= $rowins['ins_date'] ?></td>
                <td><?= $rowins['ins_value'] ?></td>
                <td><?= $rowins['ins_paid'] ?></td>
                <td><?php echo ($rowins['ins_case'] == 2) ? "مستحق" : (($rowins['ins_case'] == 3) ? "مدفوع" : "___"); ?> </td>
                
            </tr>
            <?php }?>
            <?php
             $resworth = $conn->query("SELECT * FROM myinstallments WHERE ins_date < NOW() and ins_case = 1");
             if ($resworth->num_rows > 0) {   
              while ($rowworth =$resworth->fetch_assoc()) {
              $clid_worth = $rowworth['cl_id'];
              $total = $rowworth['ins_value'];
              $user = $_SESSION['userid'];
              $res_jrnl = $conn->query("SELECT journal_id from journal_heads order by journal_id desc limit 1")->fetch_assoc(); 
              if($res_jrnl){$jrnl_id = $res_jrnl['journal_id']+1;}else{$jrnl_id = 1;};
              $wehda = $rowworth["rent_id"];
              $pro_date = date("Y-m-d");
              $acc_rent = $rowstg['acc_rent'] ;
              $sqlot = "INSERT INTO ot_head (pro_id, pro_tybe, is_journal, journal_tybe, info , pro_date, acc1,  acc2, pro_value, profit,  user) VALUES ('$jrnl_id', 5 , 1, 5, 'استحقاق ايجار عن الوحدة $wehda','$pro_date', '$clid_worth', '$acc_rent', '$total', '$total',  $user)";
              $conn->query($sqlot);
              $sql1 = "INSERT INTO journal_heads (journal_id, total, details,user) VALUES ('$jrnl_id','$total','استحقاق ايجار عن الوحدة $wehda ','$user')";
              $conn->query($sql1);
              $journal_lastid =  $conn->insert_id;
              $sql2 = "INSERT INTO journal_entries ( journal_id, account_id, debit, credit,tybe) VALUES ('$journal_lastid',' $clid_worth','$total','0','0')";
              
              $conn->query($sql2);
              $sql3 = "INSERT INTO journal_entries ( journal_id, account_id, debit, credit,tybe) VALUES ('$journal_lastid','$acc_rent','0','$total','1')";
              $conn->query($sql3);

             $conn->query("UPDATE myinstallments SET ins_case = 2  where ins_case = 1 and ins_date < NOW()");
              }}
            ?>
        </tbody>
        </table>
        </div>
                          
          </div>
      
                          </div>
              </div>
              
              <?php }?>