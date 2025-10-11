<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<style>
  ul, #myUL {
  list-style-type: none;
  
}

/* Remove margins and padding from the parent ul */
#myUL {
  margin: 0;
  padding: 0;
}

/* Style the caret/arrow */
.caret {
  cursor: pointer;
  user-select: none; /* Prevent text selection */
}

/* Create the caret/arrow with a unicode, and style it */
.caret::before {
  content: "\25c0";	
  color: black;
  font-size: 18px;
  display: inline-block;
  margin-right: 10px;
}

/* Rotate the caret/arrow icon when clicked on (using JavaScript) */
.caret-down::before {
  transform: rotate(-90deg);
}

/* Hide the nested list */
.nested {
  display: none;
}

/* Show the nested list when the user clicks on the caret/arrow (with JavaScript) */
.active {
  display: block;
}
li.tree{
  font-size: 24px;
}
</style>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
    <div class="card card">
            <div class="card-header">
                <div class="row">
                    <div class="col"><a href="add_account.php"><div class="btn  btn-primary">جديد</div></a></div>
                    <div class="col">
            <div class="h3">قائمه الحسابات شجري
</div>
</div>
            <div class="col">
            </div>
                </div>
            </div>
            <div class="row">
              <div class="col">



              <ul id="myUL">
                <?php
                $sqlacc = 'SELECT * FROM acc_head where parent_id = 0  AND isdeleted < 1 ';
                $resacc = $conn->query ($sqlacc);
                $x = 0;
                while ($rowacc = $resacc->fetch_assoc()) {
                ?>
              <li class="num0 tree"><span class="caret"><?= $rowacc['code'] ?>--<?= $rowacc['aname'] ?></span>
    
              <ul class="nested">
                            <?php
                              $p2id = $rowacc['id'];
                                      $sqlacc2 = "SELECT * FROM acc_head where parent_id = '$p2id'  AND isdeleted < 1 ";
                                $resacc2 = $conn->query ($sqlacc2);
                                $x = 0;
                                
                                while ($rowacc2 = $resacc2->fetch_assoc()) {
                                ?>  
                                
                                  <li class="tree">-<span class="caret"><?= $rowacc2['code'] ?>--<?= $rowacc2['aname'] ?></span>
                                <ul class="nested">
                                        <?php
                                                $p3id = $rowacc2['id'];
                                                $sqlacc3 = "SELECT * FROM acc_head where parent_id = '$p3id'  AND isdeleted < 1 ";
                                                $resacc3 = $conn->query ($sqlacc3);
                                                if ($resacc3 != null) {        
                                                while ($rowacc3 = $resacc3->fetch_assoc()) {
                                          ?>  
                                    
                                          <li class="tree">
                                          --<span class="caret"><?= $rowacc3['code'] ?>--<?= $rowacc3['aname'] ?></span>
                                          <ul class="nested">
                                        <?php
                                                $p4id = $rowacc3['id'];
                                                $sqlacc4 = "SELECT * FROM acc_head where parent_id = '$p4id'  AND isdeleted < 1 ";
                                                $resacc4 = $conn->query ($sqlacc4);
                                                if ($resacc4 != null) {        
                                                while ($rowacc4 = $resacc4->fetch_assoc()) {
                                          ?>  
                                    
                                          <li class="tree">
                                          ---<span class="caret"><?= $rowacc4['code'] ?>--<?= $rowacc4['aname'] ?></span>
                                                                                            
  
                                                            </li>
                                                            <?php } } ?>
                                </ul>
                                  </li>
                                  <?php } } ?>
                                </ul>
                                  </li>
                                  <?php } ?>
                                  </ul>
                                  </li>
                                  <?php } ?>
                                  </ul>
                                  </li>
</ul>
              </div>
            </div>

            <div class="card-body">
            <div class="table-responsive">
            <table class="table table-hover table-stripped ">
            <thead>
                <tr>
                <th>x</th>
                <th>الاسم</th>
                <th>الكود</th>
                <th>النوع</th>
                <th>اساسي</th>
                <th>يتبع ل</th>
                <th></th>
                </tr>
            </thead>

            <tbody>
                <?php 
                $sqlacc = 'SELECT * FROM acc_head where isdeleted < 1 ';
                $resacc = $conn->query ($sqlacc);
                $x = 0;
                while ($rowacc = $resacc->fetch_assoc()) {
                    $x++;
                ?>
            <tr>
                <td><?=  $x ?></td>
                <td><?= $rowacc['aname'] ?></td>
                <td><?= $rowacc['code'] ?></td>
                <td><?php 
                if ($rowacc['kind'] == 1) {
                    echo "ميزانيه";
                }elseif ($rowacc['kind'] == 2) {
                    echo " ارباح و خسائر";
                }
                
                 ?></td>
                
                <td><?php if ($rowacc['is_basic'] == 1){
                    echo 'حساب اساسي';
                }else{echo "حساب عادي";} ?></td>
                <td><?php
                $accheadid = $rowacc['parent_id'];
                if ($accheadid != 0) {
                 
                $rowacchead = $conn->query("SELECT * FROM acc_head where id = '$accheadid'")->fetch_assoc();
                 echo $rowacchead['aname'];
                }else {echo "__";}
                 ?></td>
                 <td><a href="edit_account.php?id=<?= $rowacc['id'] ?>" class="btn btn-warning">تعديل</a> 
                
                 <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete<?= $rowacc['id']?>">
                      <?= $rowacc['id']?> حذف
                      </button>

                </td>
            </tr>


            
                      
            <div class="modal fade" id="delete<?= $rowacc['id']?>">
                        <div class="moaccheaddal-dialog">
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
                                   <p> هل تريد بالتأكيد الحذف <?= $rowacc['aname']?> </p>
                                  </div>
                                  <div class="modal-footer justify-content-between">
                                   <button type="button" class="btn btn-outline-light" data-dismiss="modal">الغاء</button>
                                     <a href="do/dodel_account.php?id=<?= $rowacc['id'] ?>"class="btn btn-outline-light">حذف</a>
                               </div>

                          </div>
                          <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                      </div>



            <?php } ?>


            </tbody>
            
            </table>
            </div>
            </div>
        </div>
    </div>
  </section>
</div>


<script>
  var toggler = document.getElementsByClassName("caret");
var i;

for (i = 0; i < toggler.length; i++) {
  toggler[i].addEventListener("click", function() {
    this.parentElement.querySelector(".nested").classList.toggle("active");
    this.classList.toggle("caret-down");
  });
}
</script>
<?php include('includes/footer.php') ?>
