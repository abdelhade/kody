<?php 
include('includes/header.php');
include('includes/navbar.php');
include('includes/sidebar.php');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="card card-light">
            <div class="card-header">
                <h2>تقرير الوحدات الايجاريه</h2>

            </div>
            <div class="card-body">
                <div class="row">
            <?php
            $canAddRent = Can::check('rent_contracts.add');
            $canDeleteRent = Can::check('rent_contracts.delete');
            $resrent = $conn->query("SELECT * FROM acc_head where rentable = 1 OR rentable = 2  AND is_basic = 0");
                    while ($rowrent = $resrent->fetch_assoc()) {?>
              
              <div class="small-box col-lg-2 <?php if ($rowrent['rentable'] == 1 ){echo "bg-info";}elseif($rowrent['rentable'] == 2  ){echo "bg-warning";}?>" style="margin:10px;">
              <div class="inner">
               
              <h5><?= $rowrent['aname']?></h5>
                                     <?php if ($rowrent['rentable'] == 2  ) {
                        $rent_id = $rowrent['id'];
                        
                        $rowdet = $conn->query("SELECT * from myrents where rent_id = $rent_id")->fetch_assoc();
                        
                        $clid = $rowdet['cl_id'];
                        $rowcl = $conn->query("SELECT * from acc_head where id = $clid")->fetch_assoc(); ?>
                        <p><?= $rowcl['aname'] ?></p>
                        <p><?= $rowcl['phone'] ?></p>
                        <p><?= $rowdet['start_date']?> الي <?= $rowdet['end_date']?></p>
                        <p>قيمة الايجار = <?= $rowdet['r_value'] ?></p>
                        <?php if ($canDeleteRent) { ?>
                        <p>
                        <a class='btn btn-light btn-sm' href="do/start/dodel_rent.php?id=<?= $rowdet['id']?>&r=<?= $rent_id ?>" onclick="return confirm('هل تريد بالتأكيد اخلاء الوحدة و حذف العقد و مسح الاقساط المتبقية ')">اخلاء الوحده</a></p>
                        <?php } ?>
                        <?php } ?>
                        <p><?= $rowrent['info']?></p>
              </div>
              <?php if ($canAddRent) { ?>
              <a href="<?php if ($rowrent['rentable'] == 1 ){echo "add_rent.php";}elseif($rowrent['rentable'] == 2  ){echo "add_rent.php?id=".$rowrent['id'];}?>">
              <?php } ?>
              <div class="icon">
              <i class="fa fa-store"></i>
              </div>
              <?php if ($canAddRent) { ?>
              </a>
              <?php } ?>
              </div>
              
                <?php }?>
        </div>
      </div>
    </section>
</div>
<?php include('includes/footer.php'); ?>