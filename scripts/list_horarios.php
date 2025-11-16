<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$horarios = \DB::table('horarios')->get();
$usados = \DB::table('grupo_materia_horario')->pluck('horario_id')->toArray();

foreach ($horarios as $h) {
    $aula = \DB::table('aulas')->where('id', $h->aula_id)->value('nombre_aula') ?: 'N/A';
    echo $h->id . ' | ' . $h->dia_semana . ' ' . $h->hora_inicio . '-' . $h->hora_fin . ' | Aula: ' . $aula . ' | usado: ' . (in_array($h->id, $usados) ? 'SI' : 'NO') . PHP_EOL;
}
