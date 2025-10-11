<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<?php
// تضمين فئات العناصر الجديدة
require_once 'classes/InvoiceElementFactory.php';

// تعريف ثوابت أنواع الفواتير
define('INVOICE_TYPES', [
    'sale' => 4,
    'buy' => 3,
    'resale' => 10,
    'rebuy' => 11,
    'po' => 12,
    'so' => 13
]);

// تعريف أسماء الفواتير بالعربية
define('INVOICE_NAMES', [
    4 => 'فاتورة مشتريات',
    3 => 'فاتورة مبيعات',
    10 => 'فاتورة مردود مشتريات',
    11 => 'فاتورة مردود مبيعات',
    12 => 'أمر شراء',
    13 => 'أمر بيع'
]);

// تعريف أسماء التعديل
define('EDIT_NAMES', [
    4 => 'تعديل فاتورة المشتريات',
    3 => 'تعديل فاتورة المبيعات',
    10 => 'تعديل فاتورة مردود المبيعات',
    11 => 'تعديل فاتورة مردود المشتريات'
]);

// معالجة نوع الفاتورة بشكل آمن
$pro_tybe = null;
$invoice_title = 'غير محدد';
$is_edit_mode = false;
$invoice_data = null;

if (!empty($_GET['q']) && isset(INVOICE_TYPES[$_GET['q']])) {
    $pro_tybe = INVOICE_TYPES[$_GET['q']];
    $invoice_title = INVOICE_NAMES[$pro_tybe];
} elseif (!empty($_GET['e']) && is_numeric($_GET['e'])) {
    $is_edit_mode = true;
    $opid = intval($_GET['e']);
    
    // استخدام Prepared Statement للأمان
    $stmt = $conn->prepare("SELECT * FROM ot_head WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $opid);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $invoice_data = $result->fetch_assoc();
            $pro_tybe = $invoice_data['pro_tybe'];
            $invoice_title = EDIT_NAMES[$pro_tybe] ?? 'تعديل فاتورة غير معروفة';
        } else {
            $invoice_title = 'لم يتم العثور على السجل';
        }
        $stmt->close();
    }
}

// تحديد لون الخلفية بناء على نوع الفاتورة
function getBackgroundClass($pro_tybe, $is_edit_mode) {
    if ($is_edit_mode) {
        return 'bg-red-500';
    }
    
    switch ($pro_tybe) {
        case 3:
        case 4:
            return 'bg-teal-500';
        case 10:
        case 11:
        case 12:
        case 13:
            return 'bg-red-500';
        default:
            return 'bg-gray-500';
    }
}

$background_class = getBackgroundClass($pro_tybe, $is_edit_mode);

// إنشاء عناصر الفاتورة باستخدام Factory Pattern
try {
    $invoice_elements = InvoiceElementFactory::createAllElements(
        $pro_tybe, 
        $is_edit_mode, 
        $invoice_data, 
        $conn
    );
} catch (Exception $e) {
    error_log("Error creating invoice elements: " . $e->getMessage());
    $invoice_elements = [];
}
?>

<link rel="stylesheet" href="dist/css/sales.css">
<div class="content-wrapper bg-teal-50">
<section class="content-header">
<div class="container-fluid p-0 m-0">

<!-- 
OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO

                                                      

OOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
-->

<input type="text" name="pro_tybe" hidden value="<?php echo htmlspecialchars($pro_tybe ?? ''); ?>">
<center>
<h4 class="font-thin text-md <?php echo $background_class; ?> text-teal-50 hadi-wonder" style="font-size:2em;padding:10px">
    <?php echo htmlspecialchars($invoice_title); ?>
</h4>
</center>

<?php
// عرض نافذة إضافة الأصناف
if (isset($invoice_elements['add_item_modal'])) {
    echo $invoice_elements['add_item_modal']->render();
}
?>
<div class="card 111 ">
  <div class="card-body p-0 m-0">
          <?php
          // عرض صف إضافة جديد (مؤقت - سيتم استبداله بالعناصر الجديدة)
          if (isset($invoice_elements['details'])) {
              echo $invoice_elements['details']->renderNewRow();
          } else {
              // عرض مؤقت للصف القديم
              include('elements/sales/add_row.php');
          }
          ?>
            <?php 
            // تحديد ما إذا كان في وضع التعديل وتحميل البيانات
            if($is_edit_mode && $invoice_data){
                $e = intval($_GET['e']);
                $rowedit = $invoice_data; // البيانات محملة مسبقاً
            }
            if(isset($_GET['q'])){?>
        
                <form action="do/doadd_invoice.php" method="post" id="myForm2">
                      <?php }elseif(isset($_GET['e'])){?>
                <form action="do/doedit_invoice.php" method="post" id="myForm2">
                      <?php }?>
      <input type="text" hidden value="<?php if(isset($_GET['e']) && is_numeric($_GET['e'])){echo intval($_GET['e']);}?>" name="ot_id">
          <?php
          // عرض رأس الفاتورة
          if (isset($invoice_elements['header'])) {
              echo $invoice_elements['header']->render();
          }
          
          // عرض تفاصيل الفاتورة
          if (isset($invoice_elements['details'])) {
              echo $invoice_elements['details']->render();
              // عرض صف إضافة جديد
              echo $invoice_elements['details']->renderNewRow();
          }
          
          // عرض ذيل الفاتورة
          if (isset($invoice_elements['footer'])) {
              echo $invoice_elements['footer']->render();
          }
          ?>
                    
          
                </form>
        </div>    
                  </div>    


            <?php include('elements/sales/ops.php')?>

</div>
</section>
</div>

























<script>

</script>

<script src="js/sales.js"></script>
<script src="js/sales0.js"></script>
<?php include('includes/footer.php') ?>