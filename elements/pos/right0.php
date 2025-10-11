

<div class="row bg-slate-50 " id="upRight0">

                <div class="col-md-12">
                <p>نوع الطلب</p> 
                <input type="radio" id="age1" name="age" value="1" checked>
                <label for="age1">تيك اواي</label>
                <input type="radio" id="age2" name="age" value="2" <?php if (isset($_GET['table'])) {echo " checked ";} ?>>
                <label for="age2">طاولة</label>  
                <input type="radio" id="age3" name="age" value="3">
                <label for="age3">دليفري</label><br>
                </div>
                <script>
                    $('input[name="age"]').change(() => {
                        if ($('#age2').is(':checked')) {
                            $('#table_name').show(); 
                        } else {
                            $('#table_name').hide(); 
                        }
                    });
                </script>

            <input type="text" name="pro_tybe" value="9" hidden>
            <input type="text" name="pro_serial" value="0" hidden>
            <input type="text" name="pro_id" value="1" hidden>
            <?php
            $posdate = isset($_GET['edit']) ? $rowed['pro_date'] : date('Y-m-d', strtotime('-4 hours'));
                ?>

            <input type="date" name="pro_date" value="<?= $posdate?>">
            <input type="date" name="accural_date" value="<?php echo isset($_GET['edit']) ? $rowed['accural_date'] : date('Y-m-d'); ?>">
            <select name="store_id" class="" id="">
                <?php
                $resstore = $conn->query("SELECT * FROM `acc_head` WHERE is_stock =1 AND isdeleted = 0;");
                while ($rowstore = $resstore->fetch_assoc()) { ?>
                <option <?php if($rowstg['def_pos_store'] == $rowstore['id']){echo "selected";} ?> value="<?= $rowstore['id'] ?>"><?= $rowstore['aname'] ?></option>
                <?php } ?>
            </select>

            <select name="emp_id" class="" id="">
                <?php
                $resemp = $conn->query("SELECT * FROM `acc_head` WHERE parent_id = 35 AND is_basic = 0 AND isdeleted = 0;");
                while ($rowemp = $resemp->fetch_assoc()) { ?>
                <option 
                <?php if($rowstg['def_pos_employee'] == $rowemp['id']){echo " selected ";} ?> 
                <?php if(isset($_GET['edit']) && $rowed['emp_id'] == $rowemp['id']){echo " selected ";} ?>  value="<?= $rowemp['id'] ?>"><?= $rowemp['aname'] ?></option>
                <?php } ?>
            </select>

            <select name="acc2_id" class="" id="">
                <?php
                $resclient = $conn->query("SELECT * FROM `acc_head` WHERE code like '122%'  AND is_basic = 0 AND isdeleted = 0;");
                if(isset($_GET['edit'])){$rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();};

                while ($rowclient = $resclient->fetch_assoc()) { ?>
                <option 
                <?php if($rowstg['def_pos_client'] == $rowclient['id']){echo " selected ";} ?>
                <?php 
                if(isset($_GET['edit']) && $rowed['acc1'] == $rowclient['id']){echo " selected ";} ?>
                 value="<?= $rowclient['id'] ?>"><?= $rowclient['aname'] ?></option>
                <?php } ?>
            </select>

            <select name="fund_id" class="" id="">
                <?php
                    if(isset($_GET['edit'])){$rowed = $conn->query("SELECT * FROM ot_head where id = $id")->fetch_assoc();};

                    $resfund = $conn->query("SELECT * FROM `acc_head` WHERE is_fund =1 AND is_basic = 0 AND isdeleted = 0;");
                while ($rowfund = $resfund->fetch_assoc()) { ?>
                <option 
                <?php if($rowstg['def_pos_fund'] == $rowfund['id']){echo " selected ";} ?>
                <?php if((isset($_GET['edit'])) && $rowed['acc_fund'] == $rowfund['id']){echo " selected ";} ?>
                 value="<?= $rowfund['id'] ?>"><?= $rowfund['aname'] ?></option>
                <?php } ?>
            </select>
            <br>
          
            <input class="form-control" type="text" placeholder="اسم الطاولة" id="table_name"  name="table" <?php if(!isset($_GET['table'])){echo 'style="display: none;"';}?>  value="<?php if(isset($_GET['table'])){$table = $_GET['table'];echo $table;}?>">
     
            <input type="text" class="form-control form-control-sm  focus:bg-orange-200 focus:text-slate-950 frst" placeholder="برجاء قراءة الباركود" id="barcodeInput">
        </div>
       