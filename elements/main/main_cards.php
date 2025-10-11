
<div class="row row1" style="margin-top: 50px;">
      <?php if($role['sid_rents'] == 1){?>
              <div class="col-md hadi-fade-in rounded-0">
              <div class="small-box text-slate-600 bg-cyan-500">
              <div class="inner">
              <h3>
              <?php $cnt0 =$conn->query("SELECT COUNT(*) FROM myinstallments where ins_case = 2")->fetch_assoc();echo $cnt0['COUNT(*)']; ?> 
            </h3>
              <p>عدد الاقساط المستحقة</p>
              </div>
              <div class="icon">
              <i class="fa fa-search"></i>
              </div>
              <a href="" class="small-box-footer">المزيد <i class="fas fa-arrow-circle-left"></i></a>
              </div>
              </div>
              <?php } ?>


              <div class="col-md hadi-fade-in rounded-0">

              <div class="small-box text-slate-600 bg-cyan-400">
              <div class="inner">
              <h3>
                
              <?php $cnt1 =$conn->query("SELECT COUNT(*) FROM acc_head where is_basic = 0 AND isdeleted = 0 AND code like '122%'  ")->fetch_assoc();echo $cnt1['COUNT(*)']; ?>
            
            </h3>
              
              <p>عدد العملاء</p>
              </div>
              <div class="icon">
              <i class="fa fa-search"></i>
              </div>
              <a href="acc_report.php?acc=clients" class="small-box-footer">المزيد <i class="fas fa-arrow-circle-left"></i></a>
              </div>
              </div>

              <div class="col-md hadi-fade-in rounded-0">

              <div class="small-box text-slate-600 bg-cyan-300">
              <div class="inner">
              <h3><?php $cnt1 =$conn->query("SELECT COUNT(*) FROM session_time ")->fetch_assoc();echo $cnt1['COUNT(*)']; ?></h3>
              <p> عدد مرات الدخول</p>
              </div>
              <div class="icon">
              <i class="fa fa-handshake"></i>
              </div>
              <a href="" class="small-box-footer">المزيد <i class="fas fa-arrow-circle-left"></i></a>
              </div>
              </div>

              <div class="col-md hadi-fade-in rounded-0">
              <div class="small-box text-slate-600 bg-cyan-300">
              <div class="inner">
              <h3><?php 
              $cnt7 =$conn->query("SELECT COUNT(*) FROM ot_head where pro_tybe = 3 OR pro_tybe = 9")->fetch_assoc(); 
              $cnt8 =$conn->query("SELECT sum(pro_value) FROM ot_head where pro_tybe = 3 OR pro_tybe = 9")->fetch_assoc();
                $cnt09 = number_format($cnt8['sum(pro_value)'] / 1000, 2, '.', '');
              echo $cnt7['COUNT(*)'] ." / ". $cnt09."K"; 
              
              ?></h3>
              <p>المبيعات</p>
              </div>
              <div class="icon">
              <i class="fa fa-dollar-sign"></i>
              </div>
              <a href="operations_summary.php?q=buy" class="small-box-footer">المزيد <i class="fas fa-arrow-circle-left"></i></a>
              </div>
              </div>




              <?php if($role['sid_sales'] == 1){?>
              <div class="col-md hadi-fade-in rounded-0">
              <div class="small-box text-slate-600 bg-cyan-200">
              <div class="inner">
              <h3>
              <?php $cnt1 =$conn->query("SELECT COUNT(*) FROM tasks")->fetch_assoc();echo $cnt1['COUNT(*)']; ?>
              </h3>
              <p>اجمالي الطلبات</p>
              </div>
              <div class="icon">
              <i class="fa fa-store"></i>
              </div>
              <a href="#" class="small-box-footer">المزيد <i class="fas fa-arrow-circle-left"></i></a>
              </div>
              </div>
              <?php } ?>




              <?php if($role['sid_hr'] == 1){?>
              <div class="col-md hadi-fade-in rounded-0">
              <div class="small-box text-slate-600 bg-cyan-100">
              <div class="inner">
              <h3>
              <?php $cnt1 =$conn->query("SELECT COUNT(*) FROM tasks where isdeleted is null")->fetch_assoc();echo $cnt1['COUNT(*)']; ?>
              </h3>
              <p>المهمات المعلقة</p>
              </div>
              <div class="icon">
              <i class="fa fa-list"></i>
              </div>
              <a href="tasks.php" class="small-box-footer">المزيد <i class="fas fa-arrow-circle-left"></i></a>
              </div>
              </div>
              <?php } ?>

              
              <?php if($role['sid_clinics'] == 1){?>
              <div class="col-md hadi-fade-in rounded-0">
              <div class="small-box text-slate-600 bg-cyan-300">
              <div class="inner">
              <h3>
              <?php $cnt1 = $conn->query("SELECT COUNT(*) FROM reservations where duration is not null")->fetch_assoc();echo $cnt1['COUNT(*)']; ?>
              </h3>
              <p>الزيارات المعلقة</p>
              </div>
              <div class="icon">
              <i class="fa fa-list"></i>
              </div>
              <a href="reservations.php" class="small-box-footer">المزيد <i class="fas fa-arrow-circle-left"></i></a>
              </div>
              </div>
              <?php } ?>
              </div>