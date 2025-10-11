<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>
<style>
    .abs{
        position: fixed;
        top: 7px;
        left: 100px;
        width: 60px;
        height: 50px;
        z-index: 99999;
        opacity: 1;
        animation-name: shadow;
        animation-duration: 2s;
        animation-delay: 1s;
        animation-iteration-count: 2;
    }
    
    /* Kanban Board Styling */
    .kanban-board {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        padding: 20px 0;
        min-height: 600px;
    }
    
    .kanban-column {
        flex: 0 0 300px;
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        position: relative;
    }
    
    .kanban-column.drag-over {
        background-color: #e3f2fd;
        border: 2px dashed #2196f3;
    }
    
    .column-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px;
        font-weight: bold;
        position: sticky;
        top: 0;
        z-index: 10;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .kanban-cards {
        padding: 15px;
        min-height: 400px;
        max-height: 500px;
        overflow-y: auto;
    }
    
    .chance-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 15px;
        cursor: grab;
        position: relative;
        background: white;
        border-left: 4px solid #dee2e6;
    }
    
    .chance-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .chance-card.dragging {
        transform: rotate(5deg);
        opacity: 0.8;
        cursor: grabbing;
        z-index: 1000;
    }
    
    .priority-high {
        border-left: 4px solid #dc3545;
    }
    
    .priority-medium {
        border-left: 4px solid #ffc107;
    }
    
    .priority-low {
        border-left: 4px solid #28a745;
    }
    
    .drag-handle {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: grab;
        color: #ccc;
        font-size: 12px;
    }
    
    .drag-handle:hover {
        color: #999;
    }
    
    .user-badge {
        background-color: #e3f2fd;
        color: #1976d2;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .time-badge {
        background-color: #f5f5f5;
        color: #666;
        padding: 2px 6px;
        border-radius: 6px;
        font-size: 11px;
    }
    
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    @media (max-width: 768px) {
        .kanban-board {
            flex-direction: column;
        }
        .kanban-column {
            flex: 1;
            margin-bottom: 20px;
        }
    }
</style>
<!-- Statistics Dashboard -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="stats-card">
            <div class="row text-center">
                <div class="col-md-3">
                    <?php 
                    $total_chances = $conn->query("SELECT COUNT(*) as total FROM tasks WHERE isdeleted = 0")->fetch_assoc()['total'];
                    ?>
                    <h3><?= $total_chances ?></h3>
                    <p>إجمالي الفرص</p>
                </div>
                <div class="col-md-3">
                    <?php 
                    $high_priority = $conn->query("SELECT COUNT(*) as total FROM tasks WHERE isdeleted = 0 AND important = 2")->fetch_assoc()['total'];
                    ?>
                    <h3><?= $high_priority ?></h3>
                    <p>فرص مهمة جداً</p>
                </div>
                <div class="col-md-3">
                    <?php 
                    $today_chances = $conn->query("SELECT COUNT(*) as total FROM tasks WHERE isdeleted = 0 AND DATE(crtime) = CURDATE()")->fetch_assoc()['total'];
                    ?>
                    <h3><?= $today_chances ?></h3>
                    <p>فرص اليوم</p>
                </div>
                <div class="col-md-3">
                    <?php 
                    $active_types = $conn->query("SELECT COUNT(DISTINCT ch_tybe) as total FROM tasks WHERE isdeleted = 0")->fetch_assoc()['total'];
                    ?>
                    <h3><?= $active_types ?></h3>
                    <p>أنواع نشطة</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="abs">
        <button id="add" type="button" class="btn btn-warning btn-lg" data-toggle="modal" data-target="#addchance">
            <i class="fas fa-plus"></i> جديد
        </button>
    </div>










    <!-- Enhanced Add Chance Modal -->
    <div class="modal fade" id="addchance">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> إضافة فرصة جديدة</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="do/doadd_chance.php" method="post" id="addChanceForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name"><i class="fas fa-user"></i> اسم العميل</label>
                                    <input name="name" id="name" type="text" class="form-control" placeholder="اسم العميل" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone"><i class="fas fa-phone"></i> رقم الهاتف</label>
                                    <input name="phone" id="phone" type="tel" class="form-control" placeholder="رقم الهاتف" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="important"><i class="fas fa-flag"></i> مستوى الأولوية</label>
                                    <select name="important" class="form-control" id="important" required>
                                        <option value="0">غير مهم</option>
                                        <option value="1">مهم</option>
                                        <option value="2">مهم جداً</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tybe"><i class="fas fa-tags"></i> نوع الفرصة</label>
                                    <select name="tybe" class="form-control" id="tybe" required>
                                        <?php 
                                        $restybe = $conn->query("SELECT * FROM chances_tybes order by cname");
                                        while ($rowtybe = $restybe->fetch_assoc()) {
                                        ?>
                                            <option value="<?= $rowtybe['id'] ?>"><?= $rowtybe['cname']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user"><i class="fas fa-user-tie"></i> المسؤول</label>
                                    <select name="user" class="form-control" id="user" required>
                                        <?php 
                                        $resuser = $conn->query("SELECT * FROM users order by uname");
                                        while ($rowuser = $resuser->fetch_assoc()) {
                                        ?>
                                            <option value="<?= $rowuser['id'] ?>"><?= $rowuser['uname']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cdate"><i class="fas fa-calendar"></i> تاريخ الفرصة</label>
                                    <input name="cdate" id="cdate" type="date" class="form-control" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ الفرصة</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>









<?php
    $sqlchatyb = "SELECT * FROM chances_tybes";
    $reschatyb = $conn->query($sqlchatyb);
    while ($rowchatyb = $reschatyb->fetch_assoc()) {
    ?>
    <div class="col-lg-3">
        <div class="card card-default">
            <div class="card-header">
                
            <h4><?= $rowchatyb['cname'] ?></h4>
            </div>
        </div>
        <div class="card-body">
            <?php 
                    $tybid = $rowchatyb['id']; 
            $sqlcha = "SELECT * FROM tasks where ch_tybe = $tybid AND isdeleted = 0 ";
            $rescha = $conn->query($sqlcha);
            while ($rowcha = $rescha->fetch_assoc()) {
            ?>
            <div class="card card-primary card-outline">
             
              
            <div class="card-body">
                

              <button class="btn btn-default" type="button" data-toggle="modal" data-target="#details<?= $rowcha['id']?>"><h6 ><?= $rowcha['name'] ?>
              <?php
              $usid = $rowcha['user'];
              $rowusr = $conn->query("SELECT * FROM users where id = $usid")->fetch_assoc();
              echo "/ ".$rowusr['uname'];
              ?>
              </h6></button>


                <?php if ($rowcha['important'] == 0 ) {
                 echo "<div class='bg-success'> -- ";
                } ?>
                <?php if ($rowcha['important'] == 1 ) {
                echo "<div class='bg-warning'> مهم ";
               } ?>
               <?php if ($rowcha['important'] == 2 ) {
                echo "<div class='bg-danger'> مهم جدا ";
               } ?>
                <?= $rowcha['crtime'] ?></div>
            </div>  
            <div class="row">
            <div class="col">
            <button id="edit" type="button" class="btn btn-block btn-light" data-toggle="modal" data-target="#editchance<?= $rowcha['id']?>">
                تعديل</button></div>
             
               <div class="col">
                <a href="edit_task.php?id=<?= $rowcha['id'] ?>"  class="btn btn-block btn-light" >
              تعهيد </a></div>
              <div class="col">
              <button id="delete" type="button" class="btn btn-block btn-light" data-toggle="modal" data-target="#delchance<?= $rowcha['id']?>">
                حذف</button>
              </div>
            

            
            </div>
            </div>
            




            
        
<div class="modal fade" id="details<?= $rowcha['id']?>">
            
            <div class="modal-dialog" role="document">
              <div class="modal-content bg-warning">
                <div class="modal-header">
                </div>
              <div class="modal-body">

              </div>
              <div class="modal-footer">
              <button type="reset" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
              </div>


              </div>
            </div>
</div>





        
        
<div class="modal fade" id="editchance<?= $rowcha['id']?>">
            
            <div class="modal-dialog" role="document">
              <div class="modal-content bg-warning">
                <div class="modal-header">
                  <h5 class="modal-title">تعديل الطلب <?= $rowcha['id']?></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
          
                <form action="do/doedit_chance.php?id=<?= $rowcha['id']?>" method="post">
                <div class="modal-body">
                  <p>الفرص فقط للصيادين</p>
                  <input value="<?= $rowcha['name']?>"  name="name" id="name" type="text" class="form-control" placeholder="اسم العميل">
                  <input value="<?= $rowcha['phone']?>"  name="phone" id="" type="text" class="form-control" placeholder="تليفون العميل">

                  <select name="important" class="form-control" id="">
                    <option <?php if ($rowcha['important'] == 0 ) {
                      echo " selected ";
                    }?> value="0">غير مهم</option>
                    <option <?php if ($rowcha['important'] == 1 ) {
                      echo " selected ";
                    }?> value="1">مهم</option>
                    <option <?php if ($rowcha['important'] == 2 ) {
                      echo " selected ";
                    }?> value="2">مهم جدا</option>
                  </select>
          
                  
                  <select name="ch_tybe" class="form-control" id="">
                   <?php $restybe = $conn->query("SELECT * FROM chances_tybes order by id");
                   while ($rowtybe = $restybe->fetch_assoc() ) {
                    ?>
                    <option <?php if ($rowtybe['id'] == $rowcha['ch_tybe']) {
                      echo " selected ";
                    } ?> value="<?= $rowtybe['id'] ?>"><?= $rowtybe['cname']?></option>
                   <?php } ?>
                  </select>

                  <select name="user" class="form-control" id="">
                   <?php $resuser = $conn->query("SELECT * FROM users order by uname");
                   while ($rowuser = $resuser->fetch_assoc() ) {
                    ?>
                    <option <?php if ($rowuser['id'] == $rowcha['user']) {
                      echo " selected ";
                    } ?> value="<?= $rowuser['id'] ?>"><?= $rowuser['uname']?></option>
                   <?php } ?>
                  </select>


                </div>
                <div class="modal-footer">
                 
                <button type="submit" class="btn btn-primary">حفظ</button>
                  <button type="reset" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                
                </div>
                </form>
              </div>
            </div>
          </div>

          
<div class="modal fade" id="delchance<?= $rowcha['id']?>">
            
            <div class="modal-dialog" role="document">
              <div class="modal-content bg-danger">
                <div class="modal-header">
                  <h5 class="modal-title">حذف <?= $rowcha['id']?></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
          
                <form action="do/dodel_task.php?id=<?= $rowcha['id']?>" method="post">
                <div class="modal-body">
                  <p>رجاء تأكد من انك تريد حذف هذا الطلب</p>
                  <input value="<?= $rowcha['id']?>"  name="id" id="" type="text" class="form-control" placeholder="تعليق المندوب" hidden>

                  <input value="<?= $rowcha['emp_comment']?>"  name="emp_comment" id="" type="text" class="form-control" placeholder="تعليق المندوب">

                </div>
                <div class="modal-footer">
                 
                <button type="submit" class="btn btn-danger">حذف</button>
                  <button type="reset" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                
                </div>
                </form>
              </div>
            </div>
          </div>










          <div class="modal fade" id="deletechance<?= $rowcha['id']?>">
            
            <div class="modal-dialog" role="document">
              <div class="modal-content bg-danger">
                <div class="modal-header">
                  <h5 class="modal-title">تعديل الفرصه <?= $rowcha['id']?></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
          
                <form action="do/dodelete_chance.php?id=<?= $rowcha['id']?>" method="post">
                <div class="modal-body">
         <h4>هل تريد بالتأكيد حذف <?= $rowcha['name'] ?></h4> 
              </div>
                <div class="modal-footer">
                 
                <button type="submit" class="btn btn-warning">حذف</button>
                  <button type="reset" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                
                </div>
                </form>
              </div>
            </div>
          </div>







        
            <?php } ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>







<script>
$(document).ready(function() {
    // Focus on name field when add modal opens
    $('#addchance').on('shown.bs.modal', function () {
        $('#name').focus();
    });
    
    // Search and Filter functionality
    $('#searchName, #filterPriority, #filterUser').on('input change', function() {
        filterChances();
    });
    
    $('#clearFilters').click(function() {
        $('#searchName, #filterPriority, #filterUser').val('');
        filterChances();
    });
    
    function filterChances() {
        var searchName = $('#searchName').val().toLowerCase();
        var filterPriority = $('#filterPriority').val();
        var filterUser = $('#filterUser').val();
        
        $('.chance-card').each(function() {
            var card = $(this);
            var name = card.data('name');
            var priority = card.data('priority').toString();
            var user = card.data('user').toString();
            
            var showCard = true;
            
            if (searchName && name.indexOf(searchName) === -1) showCard = false;
            if (filterPriority && priority !== filterPriority) showCard = false;
            if (filterUser && user !== filterUser) showCard = false;
            
            if (showCard) {
                card.slideDown();
            } else {
                card.slideUp();
            }
        });
    }
    
    // Form validation
    $('#addChanceForm').on('submit', function(e) {
        var name = $('#name').val().trim();
        var phone = $('#phone').val().trim();
        
        if (!name) {
            alert('يرجى إدخال اسم العميل');
            e.preventDefault();
            return false;
        }
        
        if (!phone) {
            alert('يرجى إدخال رقم الهاتف');
            e.preventDefault();
            return false;
        }
    });
    
    // Smooth animations for cards
    $('.chance-card').hover(
        function() { $(this).addClass('shadow-lg'); },
        function() { $(this).removeClass('shadow-lg'); }
    );
});
</script>

<?php include('includes/footer.php') ?>

