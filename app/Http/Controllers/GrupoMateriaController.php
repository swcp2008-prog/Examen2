<?php

namespace App\Http\Controllers;

use App\Models\GrupoMateria;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Horario;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GrupoMateriaController extends Controller
{
    public function index()
    {
        $this->authorize('view', 'grupos');
        
        $gruposMaterias = GrupoMateria::with(['grupo', 'materia', 'horario', 'horario.aula'])
            ->paginate(15);
        
        $horarios = Horario::with('aula')->get();

        // Determinar qué horarios ya están ocupados por alguna asignación
        $horariosUsados = GrupoMateria::whereNotNull('horario_id')->pluck('horario_id')->toArray();

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
        $horariosUsados = GrupoMateria::whereNotNull('horario_id')->pluck('horario_id')->toArray();
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
            'horario_id' => 'required|exists:horarios,id',
        ]);

        // Validar que no exista una combinación igual
        $existe = GrupoMateria::where('grupo_id', $validated['grupo_id'])
            ->where('materia_id', $validated['materia_id'])
            ->exists();

        if ($existe) {
            return back()->withErrors(['error' => 'Esta combinación de grupo y materia ya existe']);
        }

        $grupoMateria = GrupoMateria::create($validated);

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
        $horariosUsados = GrupoMateria::whereNotNull('horario_id')
            ->where('id', '!=', $grupoMateria->id)
            ->pluck('horario_id')
            ->toArray();

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
        
        $validated = $request->validate([
            'horario_id' => 'required|exists:horarios,id',
        ]);

        // Si el horario cambió, verificar conflictos con docentes asignados
        if ($grupoMateria->horario_id !== $validated['horario_id']) {
            $docentes = $grupoMateria->docentes()->get();
            
            foreach ($docentes as $docente) {
                $conflicto = $docente->verificarConflictoHorario($validated['horario_id'], $grupoMateria->id);
                
                if ($conflicto) {
                    return back()->withErrors(['error' => "No se puede cambiar el horario: {$conflicto['mensaje']}"]);
                }
            }
        }

        $grupoMateria->update($validated);

        BitacoraService::registrar('ACTUALIZAR', 'grupo_materias', $grupoMateria->id, 'GrupoMateria actualizado');

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
