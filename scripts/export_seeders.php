<?php
// Usage: php scripts/export_seeders.php --tables=aulas,horarios
// or    php scripts/export_seeders.php --all

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

$options = [];
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--')) {
        [$k, $v] = array_pad(explode('=', $arg, 2), 2, null);
        $options[substr($k, 2)] = $v === null ? true : $v;
    }
}

$seedersDir = __DIR__ . '/../database/seeders';
if (!is_dir($seedersDir)) {
    mkdir($seedersDir, 0755, true);
}

// Backup existing seeders
$backupDir = $seedersDir . '/backups_' . date('Ymd_His');
mkdir($backupDir);
foreach (glob($seedersDir . '/*.php') as $f) {
    copy($f, $backupDir . '/' . basename($f));
}

$tables = [];
if (!empty($options['tables'])) {
    $tables = array_map('trim', explode(',', $options['tables']));
} elseif (!empty($options['all'])) {
    // Get all tables for the current connection (Postgres, MySQL, sqlite)
    $driver = DB::getDriverName();
    if ($driver === 'sqlite') {
        $rows = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
        $tables = array_map(function($r){ return $r->name; }, $rows);
    } elseif ($driver === 'pgsql') {
        $rows = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
        $tables = array_map(function($r){ return $r->tablename; }, $rows);
    } else {
        $rows = DB::select('SHOW TABLES');
        $tables = [];
        foreach ($rows as $r) {
            $tables[] = array_values((array)$r)[0];
        }
    }
    // Exclude migrations and failed_jobs or sessions tables by default
    $exclude = ['migrations', 'failed_jobs', 'jobs', 'sessions', 'cache', 'personal_access_tokens'];
    $tables = array_values(array_filter($tables, fn($t) => !in_array($t, $exclude)));
} else {
    echo "Specify --tables=table1,table2 or --all\n";
    exit(1);
}

foreach ($tables as $table) {
    echo "Exporting table: $table\n";
    $rows = DB::table($table)->get()->toArray();

    $className = 'Exported' . Str::studly($table) . 'Seeder';
    $filePath = $seedersDir . '/' . $className . '.php';

    $items = [];
    foreach ($rows as $r) {
        $arr = (array)$r;
        // Normalize values for PHP code
        $items[] = var_export($arr, true);
    }

    $itemsCode = implode(",\n        ", array_map(fn($s)=>$s, $items));

    $content = <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class $className extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        DB::table('$table')->truncate();

        DB::table('$table')->insert([
            $itemsCode
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}
PHP;

    file_put_contents($filePath, $content);
    echo "Wrote $filePath (" . count($rows) . " rows)\n";
}

echo "Backup of previous seeders is at: $backupDir\n";

echo "Done. To run the generated seeders: php artisan db:seed --class=ExportedYourTableSeeder\n";
