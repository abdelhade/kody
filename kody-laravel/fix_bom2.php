<?php
$files = array_merge(
    glob('resources/js/Pages/Hr/*.vue'),
    glob('resources/js/Pages/Reports/*.vue')
);
foreach($files as $f) {
    $c = file_get_contents($f);
    if(substr($c, 0, 3) == "\xef\xbb\xbf") {
        file_put_contents($f, substr($c, 3));
        echo "Fixed $f\n";
    }
}
