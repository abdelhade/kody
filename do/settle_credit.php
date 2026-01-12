<?php
include('../includes/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if ($id > 0) {
        $date = date('Y-m-d H:i A');
        $note_append = "\n- تم سداد الأجل بتاريخ " . $date;
        
        $sql = "UPDATE ot_head SET jal_amount = 0, jal_notes = CONCAT(COALESCE(jal_notes, ''), ?) WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $note_append, $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header("Location: ../operations_summary.php?success=settled&q=all");
            } else {
                header("Location: ../operations_summary.php?error=no_change&q=all");
            }
        } else {
            header("Location: ../operations_summary.php?error=sql&q=all");
        }
        $stmt->close();
    } else {
        header("Location: ../operations_summary.php?error=invalid_id&q=all");
    }
} else {
    header("Location: ../operations_summary.php");
}
?>
