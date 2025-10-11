<?php

require_once 'InvoiceElementBase.php';

/**
 * فئة تفاصيل الفاتورة - تطبيق polymorphism
 * Invoice Details class - implementing polymorphism
 */
class InvoiceDetails extends InvoiceElementBase
{
    private $items = [];
    private $existingDetails = [];

    public function __construct($invoiceType, $isEditMode = false, $data = null, $conn = null)
    {
        parent::__construct($invoiceType, $isEditMode, $data, $conn);
        $this->loadItems();
        if ($this->isEditMode) {
            $this->loadExistingDetails();
        }
    }

    /**
     * تحميل الأصناف
     */
    private function loadItems()
    {
        if (!$this->conn) return;

        try {
            $query = "SELECT id, iname, name2 FROM myitems WHERE isdeleted = 0 ORDER BY iname";
            $result = $this->executeSecureQuery($query);
            $this->items = $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error loading items: " . $e->getMessage());
        }
    }

    /**
     * تحميل التفاصيل الموجودة (في حالة التعديل)
     */
    private function loadExistingDetails()
    {
        if (!$this->conn || !$this->data) return;

        try {
            $invoiceId = intval($this->data['id'] ?? 0);
            if ($invoiceId > 0) {
                $query = "SELECT fd.*, mi.iname 
                         FROM fat_details fd 
                         JOIN myitems mi ON fd.item_id = mi.id 
                         WHERE fd.pro_id = ? AND fd.isdeleted = 0";
                $result = $this->executeSecureQuery($query, [$invoiceId], 'i');
                $this->existingDetails = $result->fetch_all(MYSQLI_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Error loading existing details: " . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل الفاتورة
     */
    public function render()
    {
        ob_start();
        ?>
        <div class="row">
            <div class="col">
                <div class="table-responsive itemtable" style="height: 300px">
                    <table id="fatTable" class="table table-hover table-striped table-bordered">
                        <thead class="bg-light">
                            <tr class="bg- border">
                                <th>م</th>
                                <th class="col-5">اسم الصنف</th>
                                <th>الوحدة</th>
                                <th>كمية</th>
                                <th>سعر</th>
                                <th>خصم</th>
                                <th>القيمة</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="itmrow">
                            <?php $this->renderExistingRows(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * عرض الصفوف الموجودة (في حالة التعديل)
     */
    private function renderExistingRows()
    {
        if (!$this->isEditMode || empty($this->existingDetails)) {
            return;
        }

        foreach ($this->existingDetails as $index => $detail) {
            $rowNumber = $index + 1;
            $this->renderDetailRow($detail, $rowNumber);
        }
    }

    /**
     * عرض صف تفاصيل واحد
     */
    private function renderDetailRow($detail, $rowNumber)
    {
        $quantity = abs($detail['qty_in'] - $detail['qty_out']) / $detail['u_val'];
        $price = $detail['price'] * $detail['u_val'];
        
        ?>
        <tr>
            <td class="col-1">
                <?php echo $rowNumber; ?>
                <input type="text" name="det_id[]" hidden value="<?php echo $detail['id']; ?>">
                <input type="text" name="detcrtime[]" hidden value="<?php echo $detail['crtime']; ?>">
            </td>
            
            <!-- الصنف -->
            <td id="itmTd" class="col-lg-5">
                <p><?php echo $this->sanitizeInput($detail['iname']); ?></p>
                <input id="itmprice2" type="number" name="itmname[]" hidden 
                       onclick="sT(this)" value="<?php echo $detail['item_id']; ?>">
            </td>
            
            <!-- الوحدة -->
            <td>
                <?php $this->renderUnitSelect($detail['item_id'], $detail['u_val']); ?>
            </td>
            
            <!-- الكمية -->
            <td>
                <input id="itmqty" value="<?php echo $quantity; ?>" type="number" 
                       name="itmqty[]" onclick="sT(this)" 
                       class="itmqty form-control form-control-sm" style="width:90px;">
            </td>
            
            <!-- السعر -->
            <td>
                <input id="itmprice" type="number" name="itmprice[]" onclick="sT(this)" 
                       class="itmprice form-control form-control-sm" style="width:90px;" 
                       value="<?php echo $price; ?>">
            </td>
            
            <!-- الخصم -->
            <td>
                <input id="itmdisc" value="<?php echo $detail['discount']; ?>" 
                       type="number" name="itmdisc[]" onclick="sT(this)" 
                       class="itmdisc form-control form-control-sm" style="width:120px;">
            </td>
            
            <!-- القيمة -->
            <td>
                <input readonly id="itmval" value="<?php echo $detail['det_value']; ?>" 
                       type="number" name="itmval[]" 
                       class="itmval bg-light form-control form-control-sm" style="width:150px;">
            </td>
            
            <td>
                <input id="itmprofit" name="itmprofit" hidden>
                <button class="deleteRow btn btn-danger">X</button>
            </td>
        </tr>
        <?php
    }

    /**
     * عرض قائمة الوحدات للصنف
     */
    private function renderUnitSelect($itemId, $selectedUnitVal = null)
    {
        if (!$this->conn) {
            echo '<select name="u_val[]" class="form-control form-control-sm" style="width:100px;"><option value="">لا توجد وحدات</option></select>';
            return;
        }

        try {
            $query = "SELECT iu.*, mu.uname 
                     FROM item_units iu 
                     JOIN myunits mu ON iu.unit_id = mu.id 
                     WHERE iu.item_id = ?";
            $result = $this->executeSecureQuery($query, [$itemId], 'i');
            $units = $result->fetch_all(MYSQLI_ASSOC);

            echo '<select name="u_val[]" class="form-control form-control-sm" style="width:100px;">';
            foreach ($units as $unit) {
                $selected = ($selectedUnitVal && $unit['u_val'] == $selectedUnitVal) ? 'selected' : '';
                echo "<option value='{$unit['u_val']}' {$selected}>{$this->sanitizeInput($unit['uname'])}</option>";
            }
            echo '</select>';

        } catch (Exception $e) {
            echo '<select name="u_val[]" class="form-control form-control-sm" style="width:100px;"><option value="">خطأ في التحميل</option></select>';
        }
    }

    /**
     * التحقق من صحة البيانات
     */
    public function validate()
    {
        $errors = [];

        // التحقق من وجود أصناف
        if (empty($_POST['itmname']) || !is_array($_POST['itmname'])) {
            $errors[] = 'يجب إضافة صنف واحد على الأقل';
            return $errors;
        }

        // التحقق من كل صنف
        foreach ($_POST['itmname'] as $index => $itemId) {
            if (empty($itemId)) {
                continue; // تجاهل الصفوف الفارغة
            }

            // التحقق من الكمية
            $quantity = $_POST['itmqty'][$index] ?? 0;
            if ($quantity <= 0) {
                $errors[] = "الكمية غير صحيحة في الصف " . ($index + 1);
            }

            // التحقق من السعر
            $price = $_POST['itmprice'][$index] ?? 0;
            if ($price < 0) {
                $errors[] = "السعر غير صحيح في الصف " . ($index + 1);
            }
        }

        return $errors;
    }

    /**
     * إنشاء صف جديد فارغ لإضافة صنف
     */
    public function renderNewRow()
    {
        ob_start();
        ?>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-condensed table-hover table-striped table-bordered" id="searchTable">
                    <tbody>
                        <tr>
                            <td class="col-1">
                                <div class="tool">
                                    <a id="addNewElement" class="btn bg-lime-200 btn-sm hadi-white-flash" 
                                       href="add_item.php" target="_blank">+</a>
                                    <div class="tooltext">إضافة صنف جديد</div>  
                                </div>
                            </td>
                            
                            <!-- الصنف -->
                            <td id="itmTd" class="col-lg-5">
                                <select style="width:100%" name="myitm[]" id="mySelectitm" 
                                        class="frst mySelectitm form-control">
                                    <option value="">اختر صنف</option>
                                    <?php $this->renderItemOptions(); ?>
                                </select>
                                <input id="itmprice2" type="number" name="itmname[]" hidden onclick="sT(this)">
                            </td>
                            
                            <!-- الوحدة -->
                            <td>
                                <select name="u_val[]" class="form-control form-control-sm" style="width:100px;">
                                    <option value="">اختر وحدة</option>
                                </select>
                            </td>
                            
                            <!-- الكمية -->
                            <td>
                                <input type="number" name="itmname[]" hidden>
                                <input id="itmqty" value="1.00" type="number" name="itmqty[]" 
                                       onclick="sT(this)" class="itmqty form-control form-control-sm nozero" 
                                       style="width:90px;">
                            </td>
                            
                            <!-- السعر -->
                            <td>
                                <input id="itmprice" value="0.00" type="number" name="itmprice[]" 
                                       onclick="sT(this)" class="itmprice form-control form-control-sm nozero" 
                                       style="width:90px;" step="0.001">
                            </td>
                            
                            <!-- الخصم -->
                            <td>
                                <input id="itmdisc" value="0.00" type="number" name="itmdisc[]" 
                                       onclick="sT(this)" class="itmdisc form-control form-control-sm nozero" 
                                       style="width:120px;" step="0.001">
                            </td>
                            
                            <!-- القيمة -->
                            <td>
                                <input readonly id="itmval" value="0.00" type="number" name="itmval[]" 
                                       class="itmval bg-light form-control form-control-sm nozero" 
                                       style="width:150px;" step="0.001">
                            </td>
                            
                            <td>
                                <input id="itmprofit" name="itmprofit" hidden>
                                <button id="addRow" class="btn btn-light">إضافة</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * عرض خيارات الأصناف
     */
    private function renderItemOptions()
    {
        foreach ($this->items as $item) {
            $displayName = $item['iname'];
            if (!empty($item['name2'])) {
                $displayName .= ' // ' . $item['name2'];
            }
            echo "<option value='{$item['id']}'>{$this->sanitizeInput($displayName)}</option>";
        }
    }
}