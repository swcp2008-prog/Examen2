<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$gm = \DB::table('grupo_materias')->where('id', 1)->first();
print_r($gm);
$docentes = \DB::table('docente_grupo_materias')->where('grupo_materia_id', 1)->pluck('docente_id')->toArray();
echo 'docentes: '.json_encode($docentes).PHP_EOL;
