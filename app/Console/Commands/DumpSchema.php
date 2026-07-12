<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class DumpSchema extends Command
{
    protected $signature = 'db:dump-schema';
    protected $description = 'Dump database schema to JSON';

    public function handle()
    {
        $schemaInfo = [];
        $tables = Schema::getTables();

        foreach ($tables as $tableInfo) {
            $tableName = $tableInfo['name'];
            $columns = Schema::getColumns($tableName);
            $columnNames = array_column($columns, 'name');
            $schemaInfo[$tableName] = $columnNames;
        }

        file_put_contents(storage_path('app/schema.json'), json_encode($schemaInfo, JSON_PRETTY_PRINT));
        $this->info('Schema dumped to storage/app/schema.json');
    }
}
