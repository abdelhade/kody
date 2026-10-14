<?php
/**
 * حفظ بنود الإنتاجية: الموظف بيتسجل بالـ id، والاسم بيتخزن للعرض بس.
 */

function production_save_rows(mysqli $conn, array $post): void
{
    $sndId = (int) ($post['snd_id'] ?? 0);
    $date = $conn->real_escape_string((string) ($post['date'] ?? date('Y-m-d')));
    $info = $conn->real_escape_string((string) ($post['info'] ?? ''));
    $empIds = $post['emp_id'] ?? [];
    $qtys = $post['qty'] ?? [];
    $prices = $post['price'] ?? [];
    $vals = $post['val'] ?? [];
    $info2s = $post['info2'] ?? [];

    foreach ($empIds as $i => $empIdRaw) {
        $empId = (int) $empIdRaw;
        if ($empId <= 0) {
            continue;
        }
        $rowEmp = $conn->query("SELECT name FROM employees WHERE id = $empId")->fetch_assoc();
        if (!$rowEmp) {
            continue;
        }
        $empName = $conn->real_escape_string($rowEmp['name']);
        $qty = (float) ($qtys[$i] ?? 0);
        $price = (float) ($prices[$i] ?? 0);
        $val = (float) ($vals[$i] ?? 0);
        $info2 = $conn->real_escape_string((string) ($info2s[$i] ?? ''));

        $conn->query("INSERT INTO productions (snd_id, date, emp_id, emp_name, qty, price, value, info, info2)
            VALUES ($sndId, '$date', $empId, '$empName', $qty, $price, $val, '$info', '$info2')");
    }
}
