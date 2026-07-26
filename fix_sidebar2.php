<?php
$content = file_get_contents('includes/sidebar.php');
$lines = explode("\n", $content);
$newLines = [];
$inserted = false;

for ($i = 0; $i < count($lines); $i++) {
    $newLines[] = $lines[$i];
    
    // Check if this is the line with employees.php in HR section (around line 603)
    if (strpos($lines[$i], 'href="employees.php"') !== false && !$inserted) {
        // Add the next 4 lines (the rest of the employees li)
        $newLines[] = $lines[$i+1];
        $newLines[] = $lines[$i+2];
        $newLines[] = $lines[$i+3];
        $newLines[] = $lines[$i+4];
        
        // Insert delivery link
        $newLines[] = '';
        $newLines[] = '                <li class="nav-item">';
        $newLines[] = '                  <a href="acc_report.php?acc=delivery" class="nav-link">';
        $newLines[] = '                    <i class="far "> <i class="nav-icon fas fa-motorcycle"></i> </i>';
        $newLines[] = '                    <p>ديليفري</p>';
        $newLines[] = '                  </a>';
        $newLines[] = '                </li>';
        
        $i += 4; // Skip the lines we already added
        $inserted = true;
    }
}

file_put_contents('includes/sidebar.php', implode("\n", $newLines));
echo "Done";
?>
