<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GrupoMateria;
use App\Models\Horario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

echo "Simulador PUT GrupoMateria::update\n";

// Escuchar y mostrar todas las queries SQL para depuración
DB::listen(function ($query) {
    $sql = $query->sql;
    $bindings = $query->bindings ?? [];
    $time = property_exists($query, 'time') ? $query->time : 'n/a';
    echo "SQL: {$sql} | bindings: " . json_encode($bindings) . " | time: {$time}\n";
});

// 1) Encontrar un grupo_materia con exactamente 1 horario
$gmId = DB::table('grupo_materia_horario')
    ->select('grupo_materia_id', DB::raw('count(*) as cnt'))
    ->groupBy('grupo_materia_id')
    ->havingRaw('count(*) = ?', [1])
    ->pluck('grupo_materia_id')
    ->first();

if (!$gmId) {
    echo "No se encontró ningún grupo_materia con exactamente 1 horario.\n";
    exit(1);
}

$gm = GrupoMateria::with(['horarios', 'docentes', 'grupo', 'materia'])->find($gmId);
if (!$gm) {
    echo "GrupoMateria id={$gmId} no encontrada.\n";
    exit(1);
}

$existing = $gm->horarios->pluck('id')->toArray();
echo "Seleccionado GrupoMateria id={$gm->id} ({$gm->grupo->nombre} - {$gm->materia->nombre})\n";
echo "Horarios existentes: ".json_encode($existing)."\n";

// 2) Encontrar un horario disponible (no usado por otras asignaciones)
$used = DB::table('grupo_materia_horario')->pluck('horario_id')->toArray();
$candidate = Horario::whereNotIn('id', $used)->first();
if (!$candidate) {
    echo "No hay horarios completamente libres; intentaré encontrar uno que NO esté ocupado por otro que no sea este grupo.\n";
    // Buscar horario que esté usado únicamente por este grupo (no ideal) o cualquier horario distinto al existente
    $candidate = Horario::whereNotIn('id', $existing)->first();
    if (!$candidate) {
        echo "No hay horario candidato disponible.\n";
        exit(1);
    }
}

echo "Horario candidato: id={$candidate->id} (Aula: ".($candidate->aula?->nombre ?? 'N/A').")\n";

// 3) Construir nuevos horario_ids como existing + candidate
$nuevos = array_values(array_unique(array_merge($existing, [$candidate->id])));

echo "Intentando asignar horarios: ".json_encode($nuevos)."\n";

// 4) Validar formato (simula Request validate en controller)
$validator = Validator::make(['horario_ids' => $nuevos], [
    'horario_ids' => 'required|array',
    'horario_ids.*' => 'exists:horarios,id',
]);

if ($validator->fails()) {
    echo "Validación falló: ".json_encode($validator->errors()->all())."\n";
    exit(1);
}

// 5) Validaciones ad-hoc: verificar ocupación por otra asignación y conflictos con docentes
foreach ($nuevos as $horId) {
    $ocupadoPor = GrupoMateria::whereHas('horarios', fn($q) => $q->where('horarios.id', $horId))
        ->where('id', '!=', $gm->id)
        ->first();
    if ($ocupadoPor) {
        echo "Error: el horario {$horId} ya está ocupado por GrupoMateria id={$ocupadoPor->id} ({$ocupadoPor->grupo->nombre} - {$ocupadoPor->materia->nombre}).\n";
        exit(1);
    }

    foreach ($gm->docentes as $docente) {
        // llamar al método del docente
        $conf = $docente->verificarConflictoHorario($horId, $gm->id);
        if ($conf) {
            echo "Error de conflicto docente id={$docente->id}: ".json_encode($conf)."\n";
            exit(1);
        }
    }
}

// 6) Si todo ok, sincronizar pivot
try {
    $gm->horarios()->sync($nuevos);
    echo "Sincronización exitosa. Ahora el grupo tiene horarios: ".json_encode($gm->horarios()->pluck('horarios.id')->toArray())."\n";
} catch (Exception $e) {
    echo "Excepción al sync: ".$e->getMessage()."\n";
    exit(1);
}

// 7) Mostrar estado de la tabla pivot para este grupo
$rows = DB::table('grupo_materia_horario')->where('grupo_materia_id', $gm->id)->get();
echo "Filas pivot para grupo_materia_id={$gm->id}:\n";
foreach ($rows as $r) {
    echo " - id={$r->id} grupo_materia_id={$r->grupo_materia_id} horario_id={$r->horario_id}\n";
}

    // Mostrar filas pivot para el horario candidato
    $rowsHorario = DB::table('grupo_materia_horario')->where('horario_id', $candidate->id)->get();
    echo "Filas pivot para horario_id={$candidate->id}:\n";
    foreach ($rowsHorario as $r) {
        echo " - pivot_id={$r->id} grupo_materia_id={$r->grupo_materia_id} horario_id={$r->horario_id}\n";
    }

    // Mostrar conteo total pivot
    $total = DB::table('grupo_materia_horario')->count();
    echo "Total filas pivot: {$total}\n";

exit(0);
