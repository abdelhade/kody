<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<?php include('includes/connect.php') ?>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        
      <div class="bg-danger">
            <?php 
$id = $_GET['id'];

$sqlemp = "SELECT * FROM `employees`  where id = '$id' ";
$resemp = $conn->query($sqlemp);
$rowemp = $resemp->fetch_assoc();
if (!isset($rowemp['id'])) {
  ?>
<h2>لقد دخلت هذه الصفحه من مكان غير المكان المخصص .. من فضلك عدم التلاعب بالعنوان..ارجع الي 
  
<a href="dashboard.php" class="btn btn-success"><h2>الرئيسية</h2></a></h2>
<?php die; } ?>
</div>
        <div class="row mb-2">
          <div class="col-sm-6">

          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php"><?= $lang_main ?></a></li>
              <li class="breadcrumb-item active"><a href="employees.php"></a></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-header">
              </div>

              <div class="card-body box-profile">
                <div class="text-center">
                  <img onerror="this.src='assets/alt/altemprofile.png';" class="profile-user-img img-fluid img-circle"
                       src="assets/<?= $rowemp['imgs']?>"
                       alt="User profile picture">
                </div>

                <h3 class="profile-username text-center"><?= $rowemp['name'] ?></h3>


                
                <p class="text-muted text-center"><?= $rowemp['info'] ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>الوظيفه</b> <a class="float-right"><?php
                    $jopid = $rowemp['jop'];

                    $rowjop = $conn->query("SELECT * FROM `jops` where id = $jopid ")->fetch_assoc();
                     echo $rowjop['name'] ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>الادراه</b> <a class="float-right"><?php
                    $dprtid = $rowemp['department'];
                    $rowdprt = $conn->query("SELECT * FROM `departments` where id = $dprtid ")->fetch_assoc();
                     echo $rowdprt['name'] ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>المرتب</b> <a class="float-right"><?= $rowemp['salary'] ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>التقييم العالم</b> <b class="float-right"><input type="text" id="totalSum">
                    </b>
                  </li>
                </ul>

                <a href="edit_employee.php?id=<?= $id?>" class="btn btn-warning btn-block"><b>تعديل</b></a>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">نبذة عني</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <strong><i class="fas fa-book mr-1"></i> التعليم</strong>
                <div class="btn"><?= $rowemp['education']?></div>
                <hr>

                <strong><i class="fas fa-map-marker-alt mr-1"></i> الموقع</strong>

                <p class="text-muted"><?= $rowemp['town']?> , <?= $rowemp['address']?> </p>
                <hr>
                <strong><i class="fas fa-pencil-alt mr-1"></i> المهارات</strong>

                <p><?= $rowemp['skills'] ?></p>

                <hr>

                <strong><i class="far fa-file-alt mr-1"></i> معلومات</strong>

                <p class="text-muted"><?= $rowemp['info'] ?></p>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab"><?= $lang_emprofilemainentry ?></a></li>
                  <li class="nav-item"><a class="nav-link " href="#emprofilejop" data-toggle="tab"><?= $lang_emprofilejopentry ?></a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">K B I </a></li>
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">

                  <div class="active tab-pane" id="activity">
                  
                    <div class="post clearfix">

                        <ul class="list-group card fw-bold mt-4">

                         <li class="list-group-item bg-body-secondary text-light card-title bg-primary"><?=$lang_addemployee_personalinfo?></li>
                         <li class="list-group-item"><?=$lang_publicname?> : <?= $rowemp['name'] ?></li>
                         <li class="list-group-item"><?=$lang_addemployee_email?> :<?= $rowemp['email']?></li>
                         <li class="list-group-item"><?=$lang_addemployee_phone?> :<?= $rowemp['number']?>   </li>
                         <li class="list-group-item"> <?=$lang_addemployee_dateofbirth?> :<?= $rowemp['dateofbirth']?></li>
                         <li class="list-group-item"><?=$lang_addemployee_gender?> :<?php if ($rowemp['gender'] == 0 ) {
                         echo "ذكر";
                         }else {echo "انثي";}?></li>
                         <li class="list-group-item"><?=$lang_addemployee_info?> :<?= $rowemp['info']?></li>
                         <li class="list-group-item"><?=$lang_addemployee_address1?> :<?= $rowemp['address']?></li>
                         <li class="list-group-item"><?=$lang_addemployee_address2?> :<?= $rowemp['address2']?></li>
                         <li class="list-group-item"><?=$lang_addemployee_country?> :<?php  $twnid = $rowemp['town'];
                         $rowtwn = $conn->query("SELECT * FROM towns where id = '$twnid'")->fetch_assoc();
                         echo $rowtwn['name'];
                         ?></li>

                       </ul>
                    </div>
                    <!-- /.post -->

                    <!-- Post -->
                    <div class="post">
                      <!-- /.user-block -->
                      <div class="row mb-3">
                        <div class="col-sm-6">
                          
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                          <div class="row">
                            <!-- /.col -->
                          </div>
                          <!-- /.row -->
                        </div>
                        <!-- /.col -->
                      </div>
                      <!-- /.row -->

                      <p>
                        
                        <span class="float-right">
                         
                        </span>
                      </p>

                    </div>
                    <!-- /.post -->
                  </div>
                  <div class=" tab-pane" id="emprofilejop">
                    <!-- /.post -->
                    <!-- Post -->
                    <div class="post clearfix">
  
                   <ul class="list-group card fw-bold mt-4 mb-3">

                     <li class="list-group-item bg-body-secondary text-light card-title bg-primary"><?=$lang_emprofilejop?></li>
                      <li class="list-group-item"> <?=$lang_addemployee_job?> : <?php $jopid = $rowemp['jop'];
                      $conn->query("SELECT name from jops where id = '$jopid'");echo $rowjop['name'] ?></li>
                      <li class="list-group-item"><?=$lang_addemployee_jobdepart?> : <?php $dprtid = $rowemp['department'];
                      $conn->query("SELECT name from departments where id = '$dprtid'");echo $rowdprt['name'] ?></li>
                      <li class="list-group-item"> <?=$lang_addemployee_jobtype?> :<?php $tybid = $rowemp['joptybe'];
                      $rowtyb = $conn->query("SELECT name from joptybes where id = '$tybid'")->fetch_assoc();
                      echo $rowtyb['name'] ?> </li>
                      <li class="list-group-item"> <?=$lang_addemployee_jobstart?> : <?= $rowemp['dateofhire'] ?></li>
                      <li class="list-group-item"> <?=$lang_addemployee_jobend?> : <?= $rowemp['dateofend'] ?></li>
                      <li class="list-group-item"> <?=$lang_addemployee_salary?> : <?= $rowemp['salary'] ?></li>
                      <li class="list-group-item"> <?=$lang_addemployee_shift?> : <?php $shftid = $rowemp['shift'];
                      $rowshft = $conn->query("SELECT name from shifts where id = '$shftid'")->fetch_assoc();
                      echo $rowshft['name'] ?></li>

                   </ul>

                    </div>
                    <!-- /.post -->

                    <!-- Post -->
                    <div class="post">
                      <!-- /.user-block -->
                      <div class="row mb-3">
                        <div class="col-sm-6">
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                          <div class="row">
                            <!-- /.col -->
                          </div>
                          <!-- /.row -->
                        </div>
                        <!-- /.col -->
                      </div>
                      <!-- /.row -->
                      <p>
                        <span class="float-right">
                        </span>
                      </p>
                    </div>
                    <!-- /.post -->
                  </div>

                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="timeline">
                    <div class="table">
                    <form id="kbiForm" action="" method="post">

                                    <table id="mytable" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>المعدل</th>
                                                <th>الوزن</th>
                                                <th>التقييم</th>
                                                <th>القيمة</th>
                                                <th></th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                                
                                                <?php 
                                                $resemkbi = $conn->query("SELECT * FROM `emp_kbis`  where emp_id = '$id'");
                                                while ($rowemkbi = $resemkbi->fetch_assoc()) {
                                                ?>
                                                <tr>
                                                    <th>
                                                      <?php
                                                      $kbi = $rowemkbi['kbi_id'];
                                                      $rowkname = $conn->query("SELECT * FROM kbis where id = $kbi")->fetch_assoc();?>
                                                    <p title="<?= $rowkname['info']?>"><?= $rowkname['kname']?></p>  
                                                    </th>
                                                    <th><input type="text" hidden value="<?= $rowemkbi['id']?>" name="kbi_id[]">
                                                      <input type="text" id="kbi_weight" class="form-control decimalInput" placeholder="" pattern="^\d+(\.\d{0,2})?$" title="exm: 0.15" name="kbi_weight[]"  required value="<?= $rowemkbi['kbi_weight']?>"></th>

                                                    <th><input type="text" id="kbi_rate" class="form-control decimalInput" placeholder="" pattern="^\d+(\.\d{0,2})?$" title="exm: 0.15" name="kbi_rate[]" required value="<?= $rowemkbi['kbi_rate']?>"></th>

                                                    <th><input readonly type="text" id="kbi_sum" class="form-control decimalInput" placeholder="" pattern="^\d+(\.\d{0,2})?$" title="exm: 0.15" name="kbi_sum[]" required value="<?= $rowemkbi['kbi_sum']?>"></th>
                                                    </th>
                                                </tr>
                                                
                                                <?php } ?>
                                            </tbody>
                                            <tfoot>
                                              <tr>
                                                <th>المعدل</th>
                                                <th>الوزن <p id="total_weight"></p></th>
                                                <th>التقييم</th>
                                                <th>القيمة</th>
                                                <th></th>
                                                
                                                </tr>
                                            </tfoot>
                                            </form>

                            </table>
                            <div class="row">
                              <div class="col"><button class="btn bg-lime-600 text-slate-50 btn-lg">حفظ</button></div>
                            </div>
                            
                            </form>

                            </div>
                            <div id="successMessage" style="display:none;" class="alert alert-success"></div>
                            <div id="errorMessage" style="display:none;" class="alert alert-danger"></div>


                                </div>
                    

                  <div class="tab-pane" id="settings">
                    <form class="form-horizontal">
                      <div class="form-group">
                        <label for="inputName" class="col-sm-2 control-label">Name</label>

                        <div class="col-sm-10">
                          <input type="email" class="form-control" id="inputName" placeholder="Name">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                        <div class="col-sm-10">
                          <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputName2" class="col-sm-2 control-label">Name</label>

                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputName2" placeholder="Name">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputExperience" class="col-sm-2 control-label">Experience</label>

                        <div class="col-sm-10">
                          <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputSkills" class="col-sm-2 control-label">Skills</label>

                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                          <div class="checkbox">
                            <label>
                              <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                          <button type="submit" class="btn btn-danger">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

  <script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to update the sum for each row
    function updateRowSum(row) {
        const kbiWeight = parseFloat(row.querySelector('[id^="kbi_weight"]').value) || 0;
        const kbiRate = parseFloat(row.querySelector('[id^="kbi_rate"]').value) || 0;
        const kbiSum = row.querySelector('[id^="kbi_sum"]');
        const sum = kbiWeight * (kbiRate / 100);
        kbiSum.value = sum.toFixed(2);
    }

    // Function to update the total sum
    function updateTotalSum() {
        let total = 0;
        document.querySelectorAll('input[name^="kbi_sum"]').forEach(function(input) {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('totalSum').value = total.toFixed(2);
    }
    



    document.querySelectorAll('.decimalInput').forEach(function(input) {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            updateRowSum(row);
            updateTotalSum();
        });
    });

    updateTotalSum();

    document.getElementById('kbiForm').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        fetch('js/ajax/update_kbi.php', {
            method: 'POST',
            body: new URLSearchParams(new FormData(this))
        })
        .then(response => response.text())
        .then(data => {
            console.log('AJAX request successful. Response:', data);
            document.getElementById('successMessage').textContent = "تم التعديل بنجاح";
            document.getElementById('successMessage').style.display = 'block';
            setTimeout(() => {
                document.getElementById('successMessage').style.display = 'none';
            }, 2000);
            updateTotalSum();
        })
        .catch(error => {
            console.error('AJAX request failed:', error);
            document.getElementById('errorMessage').textContent = 'تأكد من البيانات و الاتصال';
            document.getElementById('errorMessage').style.display = 'block';
            setTimeout(() => {
                document.getElementById('errorMessage').style.display = 'none';
            }, 2000);
        });
    });
});
</script>
<script>
  $(document).ready(function() {
    function updateTotal() {
        var sum = $('[name="kbi_weight[]"]').get().reduce((s, el) => s + (parseFloat($(el).val()) || 0), 0);
        $('#total_weight').text(sum.toFixed(2));
    }
    $('[name="kbi_weight[]"]').on('input', updateTotal);
    updateTotal();
});
</script>

<?php include('includes/footer.php') ?>