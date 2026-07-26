<?php
$content = file_get_contents('includes/sidebar.php');

// Find the line with employees.php and add delivery link after it
$lines = explode("\n", $content);
$newLines = [];
$found = false;

for ($i = 0; $i < count($lines); $i++) {
    $newLines[] = $lines[$i];
    
    // After the employees li closing tag, add delivery link
    if (strpos($lines[$i], 'lang_sideemployees') !== false && !$found) {
        $newLines[] = '                </li>';
        $newLines[] = '';
        $newLines[] = '                <li class="nav-item">';
        $newLines[] = '                  <a href="acc_report.php?acc=delivery" class="nav-link">';
        $newLines[] = '                    <i class="far "> <i class="nav-icon fas fa-motorcycle"></i> </i>';
        $newLines[] = '                    <p>ديليفري</p>';
        $newLines[] = '                  </a>';
        $newLines[] = '                </li>';
        $found = true;
    }
}

file_put_contents('includes/sidebar.php', implode("\n", $newLines));
echo "Done";
?>
