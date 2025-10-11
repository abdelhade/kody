
<div id="operations" class=""  style="display: none;">
 
        <div class="card" >
          <div class="card-header">

          </div>
          <div class="card-body">
          <div class="card-body">
                    <div class="table-responsive" id=>
                        
                    <table class="table table-hover table-bordered" id="myTable">
                            <thead>                   
                                <tr>
                                    <th>#</th>
                                    <th>اسم العملية</th>
                                    <th>الحساب المقابل</th>
                                    <th>قيمة العملية</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $resop = $conn->query("SELECT id,pro_tybe,acc1,acc2,pro_value from ot_head where isdeleted = 0 order by id desc ") ;
                    $x= 0;
                    while ($rowop = $resop->fetch_assoc()) {
                        $x++;
                    ?>
                            <tr>
                                    <th><?= $x ?></th>
                                    <th><a class="btn btn-block btn-light border"  href="print/print_sales.php?id=<?= $rowop['id']?>" target="_blank"><p><?php 
                                    $tybe = $rowop['pro_tybe'];$rowtybe = $conn->query("SELECT pname from pro_tybes where id = $tybe ")->fetch_assoc();echo $rowtybe['pname']; ?></p></a></th>
                                    <th><?php 
                                    $acc2 = $rowop['acc2'];$rowacc2 = $conn->query("SELECT aname from acc_head where id = $acc2 ")->fetch_assoc();echo $rowacc2['aname']; ?></th>
                                    <th><?= $rowop['pro_value'] ?></th>
                                </tr>
                <?php }?>
                            </tbody>
                        </table>


                       

                    
                   

                   
                    </div>
          </div>
        </div>
        </div>
        </div>