<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$rows = \DB::table('grupo_materia_horario')->select('grupo_materia_id', \DB::raw('count(*) as cnt'))->groupBy('grupo_materia_id')->get();
foreach ($rows as $r) {
    echo $r->grupo_materia_id . ': ' . $r->cnt . PHP_EOL;
}
