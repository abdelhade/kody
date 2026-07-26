<?php
$content = file_get_contents('includes/sidebar.php');

// Remove the incorrectly added delivery link from basic data section
$wrong = '              <li class="nav-item">
                <a href="acc_report.php?acc=employees" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p><?= $lang_sideemployees ?></p>
                </li>

                <li class="nav-item">
                  <a href="acc_report.php?acc=delivery" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-motorcycle"></i> </i>
                    <p>ديليفري</p>
                  </a>
                </li>
                </a>
              </li>';

$correct = '              <li class="nav-item">
                <a href="acc_report.php?acc=employees" class="nav-link">
                  <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                  <p><?= $lang_sideemployees ?></p>
                  </a>
              </li>';

$content = str_replace($wrong, $correct, $content);

// Add delivery link in the HR section (after employees.php link)
$hr_old = '                <li class="nav-item">
                  <a href="employees.php" class="nav-link">
                    <i class="far "> <i class="nav-icon fas fa-list"></i> </i>
                    <p><?= $lang_sideemployees ?></p>
                  </a>
                </li>

                <li class="nav-item">
                  <a href="shifts.php" class="nav-link">';

$hr_new = '                <li class="nav-item">
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

$content = str_replace($hr_old, $hr_new, $content);

file_put_contents('includes/sidebar.php', $content);
echo "Fixed";
?>
