<?php

// Rebuild the importable demo SQL without connecting to a database.
require dirname(__DIR__) . '/vendor/autoload.php';
date_default_timezone_set('Asia/Manila');
$sql = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
$sql .= "\n-- DEMO ONLY: admin / TamarawDemo!2026. Change this password before using real data.\n";
$sql .= "START TRANSACTION;\n";
foreach (\App\Database\Seeds\DemoData::rows() as $table => $rows) {
    foreach ($rows as $row) {
        $values = array_map(static function ($value) {
            if ($value === null) {
                return 'NULL';
            }
            return "'" . str_replace("'", "''", (string) $value) . "'";
        }, array_values($row));
        $sql .= 'INSERT INTO ' . $table . ' (' . implode(', ', array_keys($row)) . ') VALUES (' . implode(', ', $values) . ");\n";
    }
}
$sql .= "COMMIT;\n";
file_put_contents(dirname(__DIR__) . '/database/feu_pos.sql', $sql);
echo "Created database/feu_pos.sql\n";

