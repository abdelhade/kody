<?php include '../includes/connect.php';

$tables = [];
$res = $conn->query("SHOW TABLES ");
while ($row = mysqli_fetch_row($res)) {
    $tables[] = $row[0];
}

$return = '';
foreach ($tables as $table) {
    $res = $conn->query("SELECT * FROM " . $table);
    if (!$res) {
        continue;
    }
    $num_fields = mysqli_num_fields($res);

    $return .= 'DROP TABLE IF EXISTS ' . $table . ';';
    $row2 = mysqli_fetch_row(mysqli_query($conn, 'SHOW CREATE TABLE ' . $table));
    $return .= "\n\n" . $row2[1] . ";\n\n";

    while ($row = mysqli_fetch_row($res)) {
        $return .= 'INSERT INTO ' . $table . ' VALUES (';
        for ($j = 0; $j < $num_fields; $j++) {
            if ($row[$j] === null) {
                $return .= 'NULL';
            } else {
                $return .= '"' . addslashes((string) $row[$j]) . '"';
            }
            if ($j < $num_fields - 1) {
                $return .= ',';
            }
        }
        $return .= ");\n";
    }
    $return .= "\n\n\n";
}

$time = date("Ymd_Hi");
$handle = fopen('../BACKUP/backup_' . $time . '.sql', 'w+');
fwrite($handle, $return);
fclose($handle);
echo "successfully backed up";
