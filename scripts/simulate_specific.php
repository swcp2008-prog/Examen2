<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GrupoMateria;
use App\Models\Horario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

echo "Simulador específico: 'SA - Calculo 2' -> Viernes 09:00 Aula B-202\n";

DB::listen(function ($query) {
    $sql = $query->sql;
    $bindings = $query->bindings ?? [];
    $time = property_exists($query, 'time') ? $query->time : 'n/a';
    echo "SQL: {$sql} | bindings: " . json_encode($bindings) . " | time: {$time}\n";
});

// Buscar GrupoMateria por nombres de grupo y materia
$gm = GrupoMateria::whereHas('grupo', function($q){ $q->where('nombre', 'SA'); })
    ->whereHas('materia', function($q){ $q->where('nombre', 'Calculo 2')->orWhere('nombre', 'Calculo2')->orWhere('nombre', 'Calculo 1')->orWhere('nombre', 'Calculo'); })
    ->with(['horarios', 'docentes', 'grupo', 'materia'])
    ->first();

if (!$gm) {
    echo "No se encontró GrupoMateria con Grupo='SA' y Materia='Calculo 2' exactamente. Intentaré encontrar por materia LIKE 'Calculo%'.\n";
    $gm = GrupoMateria::whereHas('grupo', function($q){ $q->where('nombre', 'SA'); })
        ->whereHas('materia', function($q){ $q->where('nombre', 'like', 'Calculo%'); })
        ->with(['horarios', 'docentes', 'grupo', 'materia'])
        ->first();
}

if (!$gm) {
    echo "Aún no encontrado. Por favor confirma el texto exacto del Grupo o Materia.\n";
    exit(1);
}

echo "Encontrado GrupoMateria id={$gm->id} ({$gm->grupo->nombre} - {$gm->materia->nombre})\n";
echo "Horarios actuales: ".json_encode($gm->horarios->pluck('id')->toArray())."\n";

// Buscar Horario por dia/hora/aula
$aulaName = 'B-202';
$dia = 'Viernes';
$horaInicio = '09:00:00';

$horario = Horario::where('dia_semana', $dia)
    ->where('hora_inicio', 'like', '09:00%')
    ->whereHas('aula', function($q) use ($aulaName) { $q->where('nombre_aula', $aulaName); })
    ->first();

if (!$horario) {
    echo "No se encontró un horario para {$dia} {$horaInicio} en aula {$aulaName}. Listando horarios aproximados:\n";
    $cands = Horario::where('dia_semana', $dia)->where('hora_inicio', 'like', '09:%')->with('aula')->get();
    foreach ($cands as $c) {
        echo " - id={$c->id} dia={$c->dia_semana} inicio={$c->hora_inicio} aula=".($c->aula?->nombre ?? 'N/A')."\n";
    }
    exit(1);
}

echo "Horario encontrado id={$horario->id} ({$horario->dia_semana} {$horario->hora_inicio} - aula: ".($horario->aula?->nombre ?? 'N/A').")\n";

// Construir nuevos horario_ids combinando existentes + candidato
$existing = $gm->horarios->pluck('id')->toArray();
$nuevos = array_values(array_unique(array_merge($existing, [$horario->id])));

echo "Intentando asignar horarios: ".json_encode($nuevos)."\n";

// Validar
$validator = Validator::make(['horario_ids' => $nuevos], [
    'horario_ids' => 'required|array',
    'horario_ids.*' => 'exists:horarios,id',
]);
if ($validator->fails()) {
    echo "Validación falló: ".json_encode($validator->errors()->all())."\n";
    exit(1);
}

// Validaciones servidor (ocupado por otra asignación, conflicto docente)
foreach ($nuevos as $horId) {
    $ocupadoPor = GrupoMateria::whereHas('horarios', fn($q) => $q->where('horarios.id', $horId))
        ->where('id', '!=', $gm->id)
        ->first();
    if ($ocupadoPor) {
        echo "Error: el horario {$horId} ya está ocupado por GrupoMateria id={$ocupadoPor->id} ({$ocupadoPor->grupo->nombre} - {$ocupadoPor->materia->nombre}).\n";
        exit(1);
    }

    foreach ($gm->docentes as $docente) {
        $conf = $docente->verificarConflictoHorario($horId, $gm->id);
        if ($conf) {
            echo "Error de conflicto docente id={$docente->id}: ".json_encode($conf)."\n";
            exit(1);
        }
    }
}

// Intentar sync
try {
    $gm->horarios()->sync($nuevos);
    echo "Sync exitosa. Horarios ahora: ".json_encode($gm->horarios()->pluck('horarios.id')->toArray())."\n";
} catch (Exception $e) {
    echo "Excepción en sync: ".$e->getMessage()."\n";
    exit(1);
}

// Mostrar pivot rows
$rows = DB::table('grupo_materia_horario')->where('grupo_materia_id', $gm->id)->get();
echo "Filas pivot para grupo_materia_id={$gm->id}:\n";
foreach ($rows as $r) {
    echo " - pivot_id={$r->id} horario_id={$r->horario_id}\n";
}

exit(0);
