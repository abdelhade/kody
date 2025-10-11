<div class="row">
      <div class="table-responsive">
        <table class="table table-condensed table-hover table-striped table-bordered" id="searchTable">
    
          <tbody id="">
            <tr>
              <td class="col-1">
               
              <div class="tool">
              <a id="addNewElement" class="btn bg-lime-200 btn-sm hadi-white-flash" href="add_item.php" target="_blank">+</a>
            <div class="tooltext">اضافه صنف جديد</div>  
            </div>



              </td>
              <!-- الصنف -->
              <td id="itmTd" class="col-lg-5">
                <select style="width:100%" name="myitm[]" id="mySelectitm" class="frst mySelectitm form-control">
                  <option value="">اختر صنف</option>
                  <?php
                  $resitm = $conn->query("SELECT id , iname , name2 from myitems");
                  while ($rowitm = $resitm->fetch_assoc()) {?>
                  <option value="<?= $rowitm['id']?>"><?= $rowitm['iname']?> // <?= $rowitm['name2']?></option>
                   
                  <?php }?>
                </select>
                <input id="itmprice2" type="number" name="itmname[]" hidden onclick=sT(this)>
              </td>

                 <!-- الوحدة -->

                 <td>
                <select name="u_val[]" id="" class="form-control form-control-sm" style="width:100px;">
                <option value="">اختر وحدة</option>
                </select>
              </td>

              <!-- الكمية -->
              <td>
                <input type="number" name="itmname[]" hidden>
                <input id="itmqty" value="1.00" type="number" name="itmqty[]" onclick=sT(this) class="itmqty form-control form-control-sm nozero" style="width:90px;">
              </td>
           
             
              <!-- السعر -->
              <td>
                <input id="itmprice" value="0.00" type="number" name="itmprice[]" onclick=sT(this) class="itmprice form-control form-control-sm nozero" style="width:90px;" step="0.001" 
                >
              </td>
              <!-- الخصم -->
              <td>
                <input id="itmdisc" value="0.00" type="number" name="itmdisc[]" onclick=sT(this) class="itmdisc form-control form-control-sm nozero" style="width:120px;" step="0.001" 
                >
              </td>
              <!-- القيمة -->
              <td>
                <input readonly id="itmval" value="0.00" type="number" name="itmval[]" class="itmval bg-light form-control form-control-sm nozero" style="width:150px;" step="0.001" 
                >
              </td>
              <input id="itmprofit" name="itmprofit" hidden>
              <td>
                <button id="addRow" class="btn btn-light">إضافة</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

