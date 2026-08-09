<?php

include('../../includes/connect.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// جمع البيانات من الطلب
$clname = $_POST['clname'];
$phone = $_POST['phone'];
$phone2 = $_POST['phone2'];
$address = $_POST['address'];
$address2 = $_POST['address2'];
$address3 = $_POST['address3'];

$response = array();

// التحقق من وجود رقم الهاتف في قاعدة البيانات
$sqlchk = $conn->prepare("SELECT * FROM clients WHERE phone = ?");
$sqlchk->bind_param("s", $phone);
$sqlchk->execute();
$reschk = $sqlchk->get_result();

if ($reschk->num_rows > 0) {
    // رقم الهاتف موجود في قاعدة البيانات
    $response['exists'] = true;
} else {
    // إدراج البيانات في قاعدة البيانات
    $sql = $conn->prepare("INSERT INTO clients (name, phone, phone2, address, address2, address3) VALUES (?, ?, ?, ?, ?, ?)");
    $sql->bind_param("ssssss", $clname, $phone, $phone2, $address, $address2, $address3);

    if ($sql->execute() === TRUE) {
        $response['exists'] = false;
    } else {
        echo "Error: " . $sql->error;
    }
}

$conn->close();

echo json_encode($response);
?>
