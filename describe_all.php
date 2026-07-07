<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
foreach($tables as $tableObj) {
    $tableName = current((array)$tableObj);
    echo "=== TABLE: $tableName ===\n";
    $cols = \Illuminate\Support\Facades\DB::select("DESCRIBE `$tableName`");
    foreach($cols as $col) {
        echo "  {$col->Field} | {$col->Type} | NULL:{$col->Null} | Key:{$col->Key} | Default:" . ($col->Default ?? 'NULL') . "\n";
    }
    echo "\n";
}
