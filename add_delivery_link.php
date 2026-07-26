<?php
$content = file_get_contents('includes/sidebar.php');

$old = '<li class="nav-item">
                  <a href="employees.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p><?= $lang_sideemployees ?></p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="shifts.php" class="nav-link">';

$new = '<li class="nav-item">
                  <a href="employees.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p><?= $lang_sideemployees ?></p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="acc_report.php?acc=delivery" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-motorcycle"></i> </i>
                    <p>ديليفري</p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="shifts.php" class="nav-link">';

$content = str_replace($old, $new, $content);
file_put_contents('includes/sidebar.php', $content);
echo "Done";
?>
