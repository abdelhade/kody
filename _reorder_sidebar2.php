<?php
$path = __DIR__ . '/includes/sidebar.php';
$text = file_get_contents($path);

/**
 * Extract a PHP if-block starting at needle (unique string inside the block).
 * Matches from "<?php if (...)" that precedes the needle through its closing "<?php } ?>"
 * at the same nesting level of php if braces for that outer if only — we match by
 * finding the if line before needle and the first "<?php } ?>" after the section's </li>.
 */
function extractPhpIfBlock(string $text, string $needle): array {
    $pos = strpos($text, $needle);
    if ($pos === false) {
        throw new Exception("needle not found: $needle");
    }
    $before = substr($text, 0, $pos);
    if (!preg_match('/<\?php\s+if\s*\([^)]+\)\s*\{\s*\?>\s*$/s', $before, $m, PREG_OFFSET_CAPTURE)) {
        // search last <?php if before needle
        if (!preg_match_all('/<\?php\s+if\s*\([^\n]+\)\s*\{\s*\?>/', $before, $all, PREG_OFFSET_CAPTURE)) {
            throw new Exception("php if not found for $needle");
        }
        $last = end($all[0]);
        $start = $last[1];
    } else {
        $start = $m[0][1];
    }

    // find matching end: after needle, look for </li> then <?php } ?>
    // More robust: count { from the if line
    $i = $start;
    $len = strlen($text);
    // find first { after start
    $brace = strpos($text, '{', $start);
    $depth = 0;
    $i = $brace;
    while ($i < $len) {
        $ch = $text[$i];
        if ($ch === '{') {
            $depth++;
        } elseif ($ch === '}') {
            $depth--;
            if ($depth === 0) {
                // include through optional whitespace and ?>
                $end = $i + 1;
                if (substr($text, $end, 2) === '?>') {
                    $end += 2;
                }
                return [$start, $end, substr($text, $start, $end - $start)];
            }
        }
        $i++;
    }
    throw new Exception("end not found for $needle");
}

function extractLiByUnique(string $text, string $uniqueNeedle): array {
    $pos = strpos($text, $uniqueNeedle);
    if ($pos === false) {
        throw new Exception('needle not found: ' . $uniqueNeedle);
    }
    $liStart = strrpos(substr($text, 0, $pos), '<li class="nav-item');
    if ($liStart === false) {
        throw new Exception('li start not found for ' . $uniqueNeedle);
    }
    $i = $liStart;
    $len = strlen($text);
    $depth = 0;
    while ($i < $len) {
        if (substr($text, $i, 3) === '<li' && preg_match('/^<li\b/', substr($text, $i))) {
            $depth++;
            $i += 3;
            continue;
        }
        if (substr($text, $i, 5) === '</li>') {
            $depth--;
            $i += 5;
            if ($depth === 0) {
                return [$liStart, $i, substr($text, $liStart, $i - $liStart)];
            }
            continue;
        }
        $i++;
    }
    throw new Exception('li end not found for ' . $uniqueNeedle);
}

// Extract blocks
[$cs, $ce, $cards] = extractLiByUnique($text, '<?= $lang_card_management_title ?>');
[$js, $je, $jobsIf] = extractPhpIfBlock($text, '<?= $lang_job_data ?>');
[$ps, $pe, $pulseIf] = extractPhpIfBlock($text, 'Pulse (تقييم لحظي)');
[$as, $ae, $attIf] = extractPhpIfBlock($text, '<?= $lang_sideattendance ?>');
[$hs, $he, $hrIf] = extractPhpIfBlock($text, '<?= $lang_hr ?>');

echo "cards $cs-$ce\n";
echo "jobs $js-$je\n";
echo "pulse $ps-$pe\n";
echo "att $as-$ae\n";
echo "hr $hs-$he\n";

// Build pulse as nested submenu item (without outer php if wrapping the top-level li —
// keep showpulse check inside HR)
$pulseInner = <<<'HTML'
                <?php if (($rowstg['showpulse'] ?? 1) == 1) { ?>
                <li class="nav-item has-treeview">
                  <a href="#" class="nav-link nav-link-basic">
                    <i class="nav-icon fas fa-bolt text-warning"></i>
                    <p>
                      Pulse (تقييم لحظي)
                      <i class="fas fa-angle-left right"></i>
                    </p>
                  </a>
                  <ul class="nav nav-treeview shadow-inner shadow-slate-500" style="display: none;">
                    <li class="nav-item">
                      <a href="pulse.php" class="nav-link">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <p>تسجيل تقييم</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="pulse_stats.php" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>الإحصائيات واللوحة</p>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="pulse_types.php" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>أنواع التقييم</p>
                      </a>
                    </li>
                  </ul>
                </li>
                <?php } ?>

HTML;

// Insert pulse into HR before the closing </ul> of the HR top-level menu
// Find the outer ul of HR menu: after <?= $lang_hr ?> ... </a> <ul ...>
$hrBlock = $hrIf;
// The HR structure: if { <li> <a>..</a> <ul> ... </ul> </li> }
// Insert pulse before the last </ul> that closes the main HR children list —
// that's the </ul> just before </li> before <?php } ?>
$closeUl = strrpos($hrBlock, '</ul>');
if ($closeUl === false) {
    throw new Exception('HR ul not found');
}
$hrMerged = substr($hrBlock, 0, $closeUl) . "\n" . $pulseInner . substr($hrBlock, $closeUl);

// Remove all five blocks from end to start, then insert ordered block after accounts
$ranges = [
    ['cards', $cs, $ce],
    ['jobs', $js, $je],
    ['pulse', $ps, $pe],
    ['att', $as, $ae],
    ['hr', $hs, $he],
];
usort($ranges, fn($a, $b) => $b[1] <=> $a[1]);

$newText = $text;
foreach ($ranges as [$name, $s, $e]) {
    // trim surrounding whitespace
    $left = $s;
    while ($left > 0 && ctype_space($newText[$left - 1])) {
        $left--;
    }
    // eat preceding HTML comment lines
    $chunkBefore = substr($newText, max(0, $left - 200), min(200, $left));
    if (preg_match('/(?:\r?\n)[ \t]*<!--[\s\S]*?-->[ \t]*$/u', $chunkBefore, $cm)) {
        $left -= strlen($cm[0]);
    }
    $right = $e;
    while ($right < strlen($newText) && ctype_space($newText[$right])) {
        $right++;
    }
    $newText = substr($newText, 0, $left) . substr($newText, $right);
    echo "removed $name\n";
}

// Find end of accounts block to insert after
[$accS, $accE, $accB] = extractLiByUnique($newText, '<?= $lang_general_accounts ?>');
$insert = "\n\n\n        " . $jobsIf
    . "\n\n\n        " . $attIf
    . "\n\n\n        " . $hrMerged
    . "\n\n\n        " . $cards
    . "\n\n";

$newText = substr($newText, 0, $accE) . $insert . substr($newText, $accE);
file_put_contents($path, $newText);

// Verify order
$checks = [
    'accounts' => '<?= $lang_general_accounts ?>',
    'jobs' => '<?= $lang_job_data ?>',
    'att' => '<?= $lang_sideattendance ?>',
    'hr' => '<?= $lang_hr ?>',
    'pulse' => 'Pulse (تقييم لحظي)',
    'cards' => '<?= $lang_card_management_title ?>',
];
$prev = -1;
foreach ($checks as $n => $needle) {
    $p = strpos($newText, $needle);
    echo "$n @ $p\n";
    if ($p === false || $p < $prev) {
        echo "ORDER PROBLEM: $n\n";
    }
    $prev = $p === false ? $prev : $p;
}
echo 'pulse count: ' . substr_count($newText, 'Pulse (تقييم لحظي)') . "\n";
echo "OK\n";
