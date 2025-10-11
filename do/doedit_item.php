<?php
session_start();
include('../includes/connect.php');

$item_id = $_GET['edit'];
$usid = $_SESSION['userid'];

// Ensure user is authenticated
if (!isset($usid)) {
    header('location:login.php');
    exit();
} 

// Barcode Handling
if (isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];
} else {
    // Get the last barcode from the database and generate a new one
    $last_barcode_result = $conn->query('SELECT barcode FROM myitems ORDER BY id DESC LIMIT 1');
    if ($last_barcode_result && $last_barcode_result->num_rows > 0) {
        $last_barcode = $last_barcode_result->fetch_assoc()['barcode'];
        $barcode = $last_barcode + 1;
    } else {
        $barcode = 1000001; // Starting point if no barcodes exist
    }
}

// Item Name Validation (Check for duplicate names)
$iname = $_POST['iname']; 
$sqlchkname  = "SELECT * FROM myitems WHERE iname = ? AND id != ?";
$stmt = $conn->prepare($sqlchkname);
$stmt->bind_param('si', $iname, $item_id);
$stmt->execute();
$chkname = $stmt->get_result()->fetch_assoc();

if ($chkname !== null) {
    echo "يوجد صنف بنفس الاسم " . $iname;
    die;
}

// Prepare to update the main item
$code = $_POST['code'];
$name2 = $_POST['name2']; 
$group1 = $_POST['group1']; 
$group2 = $_POST['group2']; 
$info = $_POST['info']; 
$cost_price = $_POST['cost_price'][0]; 
$price1 = $_POST['price1'][0];
$price2 = $_POST['price2'][0]; 




$sql = "UPDATE  myitems  SET   iname ='$iname', name2 ='$name2', code ='$code',info ='$info', cost_price ='$cost_price', group1 ='$group1', group2 ='$group2',price1 = '$price1'  WHERE id = '$item_id'";
$conn->query($sql);

    foreach ($_POST['unit_id'] as $index => $unit_id) {
        $u_val = $_POST['u_val'][$index];
        if (!empty($_POST['unit_barcode'][$index])) {
            $unit_barcode = $_POST['unit_barcode'][$index];
        } else {
            $unit_barcode = "99" . $index . $_POST['unit_barcode'][0]; // توليد باركود تلقائي
        }
        $cost_price = $_POST['cost_price'][$index]; // نأخذ القيمة الأولى
        $price1 = $_POST['price1'][$index]; // نأخذ القيمة الأولى
        $price2 = $_POST['price2'][$index]; // نأخذ القيمة الأولى
        $market_price = $_POST['market_price'][$index]; // نأخذ القيمة الأولى
        $unit_id = $_POST['unit_id'][$index]; // نأخذ القيمة الأولى
        $u_val = $_POST['u_val'][$index]; // نأخذ القيمة الأولى


        if (!empty($_POST['unit_barcode'][$index])) {
            $unit_barcode = $_POST['unit_barcode'][$index];
        } else {
            $unit_barcode = "99" . $index . $_POST['unit_barcode'][0];
        }


        $sqlunit = "UPDATE item_units SET price1='$price1',price2='$price2',price3 = '$market_price', u_val = '$u_val',unit_barcode='$unit_barcode' WHERE id =  $item_id  AND unit_id = '$unit_id'";

        $conn->query($sqlunit);

    }


    // Redirect to the items page
    header('location:../myitems.php');
?>
