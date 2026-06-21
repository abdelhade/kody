<?php include('includes/header.php') ?>
<?php include('includes/navbar.php') ?>
<?php include('includes/sidebar.php') ?>

<?php
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$sqlemp = "SELECT * FROM `employees` WHERE `id` = '$id' LIMIT 1";
$resemp = $conn->query($sqlemp);
$rowemp = $resemp ? $resemp->fetch_assoc() : null;

if (!$rowemp || !isset($rowemp['id'])) {
    echo '<div class="content-wrapper p-4"><div class="alert alert-danger text-center">';
    echo '<h4>موظف غير موجود</h4><a href="employees.php" class="btn btn-success mt-2">العودة للقائمة</a></div></div>';
    include 'includes/footer.php';
    exit;
}

$empFormMode = 'edit';
$empFormId = 'validate_form';
$empUnlockBtnId = 'editbtn';
?>

<form class="validate_form" id="validate_form" action="do/doedit_employee.php?id=<?= $id ?>" method="post" enctype="multipart/form-data">
    <div class="content-wrapper">
        <?php include('elements/employee_form_tabs.php') ?>
    </div>
</form>

<script>
$(document).ready(function() {
    window.Parsley.addMessages('<?= $_SESSION['lang'] ?? 'ar' ?>', {
        defaultMessage: "<?= $lang_validation_required ?>",
        type: {
            email: "<?= $lang_validation_email ?>",
            url: "<?= $lang_validation_url ?>",
            number: "<?= $lang_validation_number ?>",
            integer: "<?= $lang_validation_integer ?>",
            digits: "<?= $lang_validation_digits ?>",
            alphanum: "<?= $lang_validation_pattern ?>"
        },
        notblank: "<?= $lang_validation_required ?>",
        required: "<?= $lang_validation_required ?>",
        pattern: "<?= $lang_validation_pattern ?>",
        min: "<?= sprintf($lang_validation_min, '%s') ?>",
        max: "<?= sprintf($lang_validation_max, '%s') ?>",
        range: "<?= sprintf($lang_validation_range, '%s', '%s') ?>",
        minlength: "<?= sprintf($lang_validation_minlength, '%s') ?>",
        maxlength: "<?= sprintf($lang_validation_maxlength, '%s') ?>",
        length: "<?= sprintf($lang_validation_length, '%s', '%s') ?>",
        equalto: "<?= $lang_validation_equalto ?>"
    });
    window.Parsley.setLocale('<?= $_SESSION['lang'] ?? 'ar' ?>');
    $('#validate_form').parsley();
});
</script>

<?php include 'includes/footer.php' ?>
