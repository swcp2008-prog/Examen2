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

        // Verificar conflicto por aula (solapamiento de intervalos) para cada horario seleccionado
        foreach ($horariosSeleccionados as $horId) {
            $h = Horario::find($horId);
            if (!$h) continue;
            if ($h->aula_id) {
                $aulaConflict = \DB::table('horarios')
                    ->join('grupo_materia_horario', 'horarios.id', '=', 'grupo_materia_horario.horario_id')
                    ->where('horarios.dia_semana', $h->dia_semana)
                    ->where('horarios.aula_id', $h->aula_id)
                    // overlapping condition: inicioA < finB AND finA > inicioB
                    ->where('horarios.hora_inicio', '<', $h->hora_fin)
                    ->where('horarios.hora_fin', '>', $h->hora_inicio)
                    ->exists();

                if ($aulaConflict) {
                    $msg = "Conflicto de aula: la aula ya está ocupada en {$h->dia_semana} {$h->hora_inicio}-{$h->hora_fin}";
                    \Illuminate\Support\Facades\Log::info('GrupoMateria store blocked - conflicto aula', ['horario_id' => $horId, 'detalle' => $msg]);
                    if (request()->wantsJson()) {
                        return response()->json(['error' => $msg], 422);
                    }
                    return back()->withErrors(['error' => $msg]);
                }
            }
        }

        $grupoMateria = GrupoMateria::create([
            'grupo_id' => $validated['grupo_id'],
            'materia_id' => $validated['materia_id'],
        ]);

        // Adjuntar horarios en pivot
        $grupoMateria->horarios()->attach($horariosSeleccionados);

        BitacoraService::registrar('CREAR', 'grupo_materias', $grupoMateria->id, 'GrupoMateria creado');

        // Flash UI message
        BitacoraService::flash('Grupo-Materia asignado correctamente', 'success');

        return redirect()->route('grupo-materias.index');
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

        Log::info('GrupoMateria update called', ['id' => $grupoMateria->id, 'horario_ids' => $nuevosHorarios, 'user_id' => auth()->id(), 'payload' => $request->all()]);

        // Validaciones: revisar conflictos por cada horario a asignar
        $docentes = $grupoMateria->docentes()->get();
        foreach ($nuevosHorarios as $horarioId) {
            // Verificar conflicto por aula (solapamiento de intervalos)
            $hCheck = Horario::find($horarioId);
            if ($hCheck && $hCheck->aula_id) {
                $aulaConflict = \DB::table('horarios')
                    ->join('grupo_materia_horario', 'horarios.id', '=', 'grupo_materia_horario.horario_id')
                    ->where('horarios.dia_semana', $hCheck->dia_semana)
                    ->where('horarios.aula_id', $hCheck->aula_id)
                    ->where('grupo_materia_horario.grupo_materia_id', '!=', $grupoMateria->id)
                    ->where('horarios.hora_inicio', '<', $hCheck->hora_fin)
                    ->where('horarios.hora_fin', '>', $hCheck->hora_inicio)
                    ->exists();

                if ($aulaConflict) {
                    $msg = "Conflicto de aula: la aula ya está ocupada en {$hCheck->dia_semana} {$hCheck->hora_inicio}-{$hCheck->hora_fin}";
                    Log::info('GrupoMateria update blocked - conflicto aula', ['grupo_materia_id' => $grupoMateria->id, 'horario_id' => $horarioId, 'detalle' => $msg]);
                    if ($request->wantsJson()) {
                        return response()->json(['error' => $msg], 422);
                    }
                    return back()->withErrors(['error' => $msg]);
                }
            }

            // Verificar que el horario no esté ocupado por otra asignación
            $ocupadoPor = GrupoMateria::whereHas('horarios', fn($q) => $q->where('horarios.id', $horarioId))
                ->where('id', '!=', $grupoMateria->id)
                ->first();
            if ($ocupadoPor) {
                $msg = "El horario {$horarioId} ya está ocupado por {$ocupadoPor->grupo->nombre} - {$ocupadoPor->materia->nombre}";
                Log::info('GrupoMateria update blocked - horario ocupado', ['grupo_materia_id' => $grupoMateria->id, 'horario_id' => $horarioId, 'ocupado_por' => $ocupadoPor->id]);
                if ($request->wantsJson()) {
                    return response()->json(['error' => $msg], 422);
                }
                return back()->withErrors(['error' => $msg]);
            }

            // Verificar conflicto con docentes
            foreach ($docentes as $docente) {
                $conflicto = $docente->verificarConflictoHorario($horarioId, $grupoMateria->id);
                if ($conflicto) {
                    $msg = "No se puede asignar el horario: {$conflicto['mensaje']}";
                    Log::info('GrupoMateria update blocked - conflicto docente', ['grupo_materia_id' => $grupoMateria->id, 'horario_id' => $horarioId, 'docente_id' => $docente->id, 'conflicto' => $conflicto]);
                    if ($request->wantsJson()) {
                        return response()->json(['error' => $conflicto], 422);
                    }
                    return back()->withErrors(['error' => $msg]);
                }
            }
        }

        // Sin conflictos, sincronizar pivot
        try {
            $grupoMateria->horarios()->sync($nuevosHorarios);
        } catch (\Exception $e) {
            Log::error('Error syncing horarios for GrupoMateria', ['id' => $grupoMateria->id, 'error' => $e->getMessage()]);
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Error al sincronizar horarios', 'exception' => $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Error al sincronizar horarios']);
        }

        BitacoraService::registrar('ACTUALIZAR', 'grupo_materias', $grupoMateria->id, 'GrupoMateria actualizado');

        $count = $grupoMateria->horarios()->count();
        Log::info('GrupoMateria update successful', ['id' => $grupoMateria->id, 'now_count' => $count]);

        // Flash UI message
        BitacoraService::flash('Grupo-Materia actualizado correctamente', 'success');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'grupo_materia_id' => $grupoMateria->id, 'horario_ids' => $nuevosHorarios, 'count' => $count], 200);
        }

        return redirect()->route('grupo-materias.index');
    }

    public function destroy(GrupoMateria $grupoMateria)
    {
        $this->authorize('delete', 'grupos');
        
        BitacoraService::registrar('ELIMINAR', 'grupo_materias', $grupoMateria->id, 'GrupoMateria eliminado');
        
        $grupoMateria->delete();

        BitacoraService::flash('Grupo-Materia eliminada', 'success');

        return redirect()->route('grupo-materias.index');
    }

    /**
     * Retorna horarios disponibles para una grupoMateria específica
     * (considera horarios ya asignados y excluye conflictos)
     */
    public function getHorariosDisponibles(GrupoMateria $grupoMateria)
    {
        $this->authorize('view', 'grupos');

        $horariosActuales = $grupoMateria->horarios()->pluck('horarios.id')->toArray();
        
        // Obtener todos los horarios
        $todosHorarios = Horario::with('aula')->get();
        
        // Obtener horarios ocupados por OTRAS grupoMaterias
        $ocupados = \DB::table('grupo_materia_horario')
            ->where('grupo_materia_id', '!=', $grupoMateria->id)
            ->pluck('horario_id')
            ->toArray();

        $disponibles = [];
        foreach ($todosHorarios as $h) {
            $esActual = in_array($h->id, $horariosActuales);
            $esOcupado = in_array($h->id, $ocupados);
            
            // Disponible si: es actual O (no ocupado Y no hay conflicto aula/docente)
            if ($esActual) {
                $h->disponible = true;
                $h->razon = 'Ya asignado';
                $disponibles[] = $h;
            } elseif (!$esOcupado) {
                // Verificar conflictos de aula
                $aulaConflict = false;
                if ($h->aula_id) {
                    $aulaConflict = \DB::table('horarios')
                        ->join('grupo_materia_horario', 'horarios.id', '=', 'grupo_materia_horario.horario_id')
                        ->where('horarios.dia_semana', $h->dia_semana)
                        ->where('horarios.aula_id', $h->aula_id)
                        ->where('grupo_materia_horario.grupo_materia_id', '!=', $grupoMateria->id)
                        ->where('horarios.hora_inicio', '<', $h->hora_fin)
                        ->where('horarios.hora_fin', '>', $h->hora_inicio)
                        ->exists();
                }

                // Verificar conflictos de docentes
                $docenteConflict = false;
                $docentes = $grupoMateria->docentes()->get();
                foreach ($docentes as $docente) {
                    if ($docente->verificarConflictoHorario($h->id, $grupoMateria->id)) {
                        $docenteConflict = true;
                        break;
                    }
                }

                if (!$aulaConflict && !$docenteConflict) {
                    $h->disponible = true;
                    $h->razon = 'Disponible';
                    $disponibles[] = $h;
                } else {
                    $h->disponible = false;
                    $h->razon = $aulaConflict ? 'Conflicto de aula' : 'Conflicto de docente';
                }
            } else {
                $h->disponible = false;
                $h->razon = 'Ocupado por otra materia';
            }
        }
        
        return response()->json(['horarios' => $disponibles]);
    }
}
