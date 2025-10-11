
<div class="row  d-flex flex-column" id="upRight">
            <div class="row  " id="upRight1">
                <div class="table font-lg">    
                    <table class="table bg-light shadow">
                        <thead>
                            <tr>
                                <td>م</td>
                                <td>الاسم</td>
                                <td>عدد</td>
                                <td>سعر</td>
                                <td>قيمه</td>
                            </tr>
                        </thead> 
                        <tbody class="overflow-y-scroll" style="max-height:150px" id="itemData">
                            <?php
                            if (isset($_GET['edit'])){
                                $id = $_GET['edit'];
                                $sqldet = "SELECT * FROM fat_details where pro_id = $id AND isdeleted  = 0";
                                $resdet = $conn->query($sqldet);
                                $x = 0;
                                while ($rowdet = $resdet->fetch_assoc()) {
                               $x++;
                               ?>
                                <tr data-itemid="${itemData.barcode}">
                                    <td><?= $x?></td>
                                    <td class="barcode" hidden>${itemData.barcode}</td>
                                    <td class="iname"><input hidden value='${itemData.id}' name="itmname[]">${itemData.iname}</td>
                                    <td class="qty"><input type="number" class="cashInput quantityInput select-all nozero bg-slate-100" value="${qty}" name="itmqty[]"><input type="text" name="u_val[]" value="1" hidden></td>
                                    <td class="price"><input type="number" class="cashInput priceInput select-all nozero bg-slate-100" value="${price.toFixed(2)}" name="itmprice[]"> ج</td>
                                    <td><input hidden name="itmdisc[]"><input type="text" class="subtotal cashInput" readonly value="${subtotal.toFixed(2)}" name="itmval[]"></td>
                                    <td class="delRow"><button class="btn btn-danger">X</button></td>
                                </tr>
                                <?php    }}; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row bg-orange-50 mt-auto text-lg" id="upRight2" style="bottom:0px;">
                <div class="row" style="width:100%;">
                    <div class="col-12">
                        <input type="text" class="form-control bg-light border-3" name="info" id="info" placeholder="اكتب ملاحظة">
                    </div>
                    <div class="col-md-2"><p for="" class="font-bold ">اجمالي</p></div>
                    <div class="col-md-3">
                        <input class="nozero form-control form-control-sm" type="text" readonly name="headtotal" id="total" value="0.00">
                        <input name="headplus" hidden>
                    </div>
                    <div class="col-md-2">
                        <p class="font-bold" for="">(F6)خصم</p>
                    </div>
                    <div class="col-md-2">
                        <input class="nozero form-control form-control-sm" type="text" name="" id="discperc" value="0">
                    </div>%
                    <div class="col-md-2">
                    <script>
                    $('#discperc').keyup(() => {
                        let total = parseFloat($('#total').val()) || 0;
                        let discount = (total * (parseFloat($('#discperc').val()) || 0) / 100).toFixed(2);
                        $('#discount').val(discount);
                        $('#net_val').val((total - discount).toFixed(0));
                    });

                </script>
                        <input class="nozero form-control form-control-sm" type="text" name="headdisc" id="discount" value="0">
                    </div>
                </div>
                
                <div class="row" style="width:100%;">
                    <div class="col-md-4"><p class="font-bold" for="">صافي</p></div>
                    <div class="col-md-8 p-0 m-0">    
                        <input class="form-control form-control-sm text-lg font-normal" type="text" name="headnet" id="net_val" value="0">
                    </div>
                </div>

                <div class="row" style="width:100%;">
                    <div class="col-md-2">المدفوع</div>
                    <div class="col-md-4">
                        <input class="nozero form-control form-control-sm" type="text" id="paid" value="0.00">
                    </div>
                    <div class="col-md-2">الباقي</div>
                    <div class="col-md-4">
                        <input class="nozero form-control form-control-sm" type="text" id="change" value="0.00">
                    </div>
                </div>
            </div>
        </div>