<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Aula;
use App\Models\Docente;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HorarioController extends Controller
{
    public function index()
    {
        $this->authorize('view', 'horarios');
        
        $horarios = Horario::with('aula')
            ->paginate(15);

        return Inertia::render('Horarios/Index', [
            'horarios' => $horarios,
        ]);
    }

    public function create()
    {
        $this->authorize('create', 'horarios');
        
        $aulas = Aula::where('estado', 'activa')->get();
        
        return Inertia::render('Horarios/Create', [
            'aulas' => $aulas,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', 'horarios');
        
        $validated = $request->validate([
            'aula_id' => 'required|exists:aulas,id',
            'dia_semana' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $horario = Horario::create($validated);

        BitacoraService::registrar('CREAR', 'horarios', $horario->id, 'Horario creado');

        return redirect()->route('horarios.index')
            ->with('success', 'Horario creado exitosamente');
    }

    public function edit(Horario $horario)
    {
        $this->authorize('edit', 'horarios');
        
        $aulas = Aula::where('estado', 'activa')->get();
        
        return Inertia::render('Horarios/Edit', [
            'horario' => $horario,
            'aulas' => $aulas,
        ]);
    }

    public function update(Request $request, Horario $horario)
    {
        $this->authorize('edit', 'horarios');
        
        $validated = $request->validate([
            'aula_id' => 'required|exists:aulas,id',
            'dia_semana' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $horario->update($validated);

        BitacoraService::registrar('ACTUALIZAR', 'horarios', $horario->id, 'Horario actualizado');

        return redirect()->route('horarios.index')
            ->with('success', 'Horario actualizado exitosamente');
    }

    public function destroy(Horario $horario)
    {
        $this->authorize('delete', 'horarios');
        
        BitacoraService::registrar('ELIMINAR', 'horarios', $horario->id, 'Horario eliminado');
        
        $horario->delete();

        return redirect()->route('horarios.index')
            ->with('success', 'Horario eliminado exitosamente');
    }

    public function semanales(Request $request)
    {
        $this->authorize('view', 'horarios');
        
        $grupoMateria = $request->input('grupo_materia_id');

        $horarios = Horario::whereHas('grupoMaterias', function ($query) use ($grupoMateria) {
            $query->where('id', $grupoMateria);
        })->get();

        return response()->json($horarios);
    }

    public function verificarDisponibilidad(Request $request)
    {
        $request->validate([
            'docente_id' => 'required|exists:docentes,id',
            'horario_id' => 'required|exists:horarios,id',
        ]);

        $docente = \App\Models\Docente::find($request->input('docente_id'));
        $horarioId = $request->input('horario_id');

        $conflicto = $docente->verificarConflictoHorario($horarioId);

        if ($conflicto) {
            return response()->json([
                'disponible' => false,
                'mensaje' => $conflicto['mensaje'],
            ], 422);
        }

        return response()->json([
            'disponible' => true,
            'mensaje' => 'Horario disponible para este docente',
        ]);
    }
}
