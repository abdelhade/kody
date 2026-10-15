<?php
include '../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['aname'])) {
    $id = (int) ($_GET['id'] ?? 0);
    Can::authorizeAccountId($conn, $id, 'edit');

    $code      = trim((string) ($_POST['code'] ?? ''));
    $aname     = trim((string) ($_POST['aname'] ?? ''));
    $parent_id = (int) ($_POST['parent_id'] ?? 0);
    $is_basic  = (int) ($_POST['is_basic'] ?? 0);
    $is_stock  = isset($_POST['is_stock']) ? (int) $_POST['is_stock'] : 0;
    $secret    = isset($_POST['secret']) ? (int) $_POST['secret'] : 0;
    $is_fund   = isset($_POST['is_fund']) ? (int) $_POST['is_fund'] : 0;
    $rentable  = isset($_POST['rentable']) ? (int) $_POST['rentable'] : 0;
    $cash_only = isset($_POST['cash_only']) ? 1 : 0;

    $cur = $conn->prepare('SELECT parent_id FROM acc_head WHERE id = ? LIMIT 1');
    $cur->bind_param('i', $id);
    $cur->execute();
    $currentParent = (int) ($cur->get_result()->fetch_row()[0] ?? 0);
    $cur->close();
    if ($parent_id !== $currentParent) {
        // نقل الحساب تحت أب تاني = إضافة تحت الأب الجديد
        if ($parent_id > 0) {
            Can::authorizeAccountId($conn, $parent_id, 'add');
        } else {
            Can::authorize('accounts.chart');
        }
    }

    if (isset($_POST['price_list'])) {
        $price_list = max(1, min(3, (int) $_POST['price_list']));
        $stmt = $conn->prepare('UPDATE acc_head SET code = ?, aname = ?, is_fund = ?, rentable = ?, is_stock = ?, parent_id = ?, is_basic = ?, secret = ?, cash_only = ?, price_list = ? WHERE id = ?');
        $stmt->bind_param('ssiiiiiiiii', $code, $aname, $is_fund, $rentable, $is_stock, $parent_id, $is_basic, $secret, $cash_only, $price_list, $id);
    } else {
        $stmt = $conn->prepare('UPDATE acc_head SET code = ?, aname = ?, is_fund = ?, rentable = ?, is_stock = ?, parent_id = ?, is_basic = ?, secret = ?, cash_only = ? WHERE id = ?');
        $stmt->bind_param('ssiiiiiiii', $code, $aname, $is_fund, $rentable, $is_stock, $parent_id, $is_basic, $secret, $cash_only, $id);
    }
    $stmt->execute();
    $stmt->close();

    header("location:../accounts.php");
}
