<?php
session_start();
include('../includes/connect.php');
require_once('../classes/Manufacturing.php');

Can::authorize('items.edit');

$itemId = (int) ($_POST['item_id'] ?? 0);
if ($itemId < 1) {
    header('Location: ../myitems.php');
    exit;
}

try {
    Manufacturing::saveRecipe(
        $conn,
        $itemId,
        is_array($_POST['component_id'] ?? null) ? $_POST['component_id'] : [],
        is_array($_POST['qty'] ?? null) ? $_POST['qty'] : []
    );
    header('Location: ../item_recipe.php?id=' . $itemId . '&ok=1');
} catch (RuntimeException $e) {
    $_SESSION['recipe_error'] = $e->getMessage();
    header('Location: ../item_recipe.php?id=' . $itemId);
}
exit;
