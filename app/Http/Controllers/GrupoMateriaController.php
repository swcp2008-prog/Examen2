<?php

namespace App\Http\Controllers;

use App\Models\GrupoMateria;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Horario;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class GrupoMateriaController extends Controller
{
    public function index()
    {
        $this->authorize('view', 'grupos');
        
        $gruposMaterias = GrupoMateria::with(['grupo', 'materia', 'horarios.aula'])
            ->paginate(15);
        
        $horarios = Horario::with('aula')->get();

        // Determinar qué horarios ya están ocupados por alguna asignación
        if (\Schema::hasTable('grupo_materia_horario')) {
            $horariosUsados = \DB::table('grupo_materia_horario')->pluck('horario_id')->toArray();
        } else {
            // Fallback: revisar columna antigua
            $horariosUsados = GrupoMateria::whereNotNull('horario_id')->pluck('horario_id')->toArray();
        }

        foreach ($horarios as $horario) {
            $horario->disponible = !in_array($horario->id, $horariosUsados);
        }

        return Inertia::render('GrupoMaterias/Index', [
            'gruposMaterias' => $gruposMaterias,
            'horarios' => $horarios,
        ]);
    }

    public function create()
    {
        $this->authorize('create', 'grupos');
        
        $grupos = Grupo::all();
        $materias = Materia::all();
        $horarios = Horario::with('aula')->get();

        // Marcar disponibilidad de horarios (para crear asignaciones)
        if (\Schema::hasTable('grupo_materia_horario')) {
            $horariosUsados = \DB::table('grupo_materia_horario')->pluck('horario_id')->toArray();
        } else {
            $horariosUsados = GrupoMateria::whereNotNull('horario_id')->pluck('horario_id')->toArray();
        }
        foreach ($horarios as $horario) {
            $horario->disponible = !in_array($horario->id, $horariosUsados);
        }
        
        return Inertia::render('GrupoMaterias/Create', [
            'grupos' => $grupos,
            'materias' => $materias,
            'horarios' => $horarios,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', 'grupos');
        
        $validated = $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'materia_id' => 'required|exists:materias,id',
            'horario_ids' => 'required|array',
            'horario_ids.*' => 'exists:horarios,id',
        ]);

        // Validar que no exista una combinación igual
        $existe = GrupoMateria::where('grupo_id', $validated['grupo_id'])
            ->where('materia_id', $validated['materia_id'])
            ->exists();

        if ($existe) {
            return back()->withErrors(['error' => 'Esta combinación de grupo y materia ya existe']);
        }

        // Verificar que los horarios seleccionados estén disponibles
        $horariosSeleccionados = $validated['horario_ids'];
        if (\Schema::hasTable('grupo_materia_horario')) {
            $ocupados = \DB::table('grupo_materia_horario')->whereIn('horario_id', $horariosSeleccionados)->pluck('horario_id')->toArray();
        } else {
            $ocupados = GrupoMateria::whereNotNull('horario_id')->whereIn('horario_id', $horariosSeleccionados)->pluck('horario_id')->toArray();
        }

        if (!empty($ocupados)) {
            return back()->withErrors(['error' => 'Algunos horarios seleccionados ya están ocupados']);
        }

        $grupoMateria = GrupoMateria::create([
            'grupo_id' => $validated['grupo_id'],
            'materia_id' => $validated['materia_id'],
        ]);

        // Adjuntar horarios en pivot
        $grupoMateria->horarios()->attach($horariosSeleccionados);

        BitacoraService::registrar('CREAR', 'grupo_materias', $grupoMateria->id, 'GrupoMateria creado');

        return redirect()->route('grupo-materias.index')
            ->with('success', 'Asignación creada exitosamente');
    }

    public function edit(GrupoMateria $grupoMateria)
    {
        $this->authorize('edit', 'grupos');
        
        $grupos = Grupo::all();
        $materias = Materia::all();
        $horarios = Horario::with('aula')->get();

        // Para edición, considerar como disponible el horario que ya tiene esta asignación
        if (\Schema::hasTable('grupo_materia_horario')) {
            $horariosUsados = \DB::table('grupo_materia_horario')
                ->where('grupo_materia_id', '!=', $grupoMateria->id)
                ->pluck('horario_id')
                ->toArray();
        } else {
            $horariosUsados = GrupoMateria::whereNotNull('horario_id')
                ->where('id', '!=', $grupoMateria->id)
                ->pluck('horario_id')
                ->toArray();
        }

        foreach ($horarios as $horario) {
            $horario->disponible = !in_array($horario->id, $horariosUsados);
        }
        
        return Inertia::render('GrupoMaterias/Edit', [
            'grupoMateria' => $grupoMateria,
            'grupos' => $grupos,
            'materias' => $materias,
            'horarios' => $horarios,
        ]);
    }

    public function update(Request $request, GrupoMateria $grupoMateria)
    {
        $this->authorize('edit', 'grupos');
        
        // Ahora soportamos asignar/desasignar varios horarios via pivot
        $validated = $request->validate([
            'horario_ids' => 'required|array',
            'horario_ids.*' => 'exists:horarios,id',
        ]);

        $nuevosHorarios = $validated['horario_ids'];

        Log::info('GrupoMateria update called', ['id' => $grupoMateria->id, 'horario_ids' => $nuevosHorarios, 'user_id' => auth()->id()]);

        // Validaciones: revisar conflictos por cada horario a asignar
        $docentes = $grupoMateria->docentes()->get();
        foreach ($nuevosHorarios as $horarioId) {
            // Verificar que el horario no esté ocupado por otra asignación
            $ocupadoPor = GrupoMateria::whereHas('horarios', fn($q) => $q->where('horarios.id', $horarioId))
                ->where('id', '!=', $grupoMateria->id)
                ->first();
            if ($ocupadoPor) {
                return back()->withErrors(['error' => "El horario {$horarioId} ya está ocupado por {$ocupadoPor->grupo->nombre} - {$ocupadoPor->materia->nombre}"]);
            }

            // Verificar conflicto con docentes
            foreach ($docentes as $docente) {
                $conflicto = $docente->verificarConflictoHorario($horarioId, $grupoMateria->id);
                if ($conflicto) {
                    return back()->withErrors(['error' => "No se puede asignar el horario: {$conflicto['mensaje']}"]);
                }
            }
        }

        // Sin conflictos, sincronizar pivot
        $grupoMateria->horarios()->sync($nuevosHorarios);

        BitacoraService::registrar('ACTUALIZAR', 'grupo_materias', $grupoMateria->id, 'GrupoMateria actualizado');

        Log::info('GrupoMateria update successful', ['id' => $grupoMateria->id, 'now_count' => $grupoMateria->horarios()->count()]);

        return redirect()->route('grupo-materias.index')
            ->with('success', 'Asignación actualizada exitosamente');
    }

    public function destroy(GrupoMateria $grupoMateria)
    {
        $this->authorize('delete', 'grupos');
        
        BitacoraService::registrar('ELIMINAR', 'grupo_materias', $grupoMateria->id, 'GrupoMateria eliminado');
        
        $grupoMateria->delete();

        return redirect()->route('grupo-materias.index')
            ->with('success', 'Asignación eliminada exitosamente');
    }
}
