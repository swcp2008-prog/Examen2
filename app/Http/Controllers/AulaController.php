<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AulaController extends Controller
{
    public function index()
    {
        $this->authorize('view', 'aulas');
        
        $aulas = Aula::paginate(15);

        return Inertia::render('Aulas/Index', [
            'aulas' => $aulas,
        ]);
    }

    public function create()
    {
        $this->authorize('create', 'aulas');
        
        return Inertia::render('Aulas/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', 'aulas');
        
        $validated = $request->validate([
            'nombre_aula' => 'required|string|max:255|unique:aulas,nombre_aula',
            'tipo' => 'required|string|in:Salón,Laboratorio,Auditorio',
            'capacidad' => 'required|integer|min:1|max:500',
            'estado' => 'required|in:activa,inactiva,mantenimiento',
        ]);

        $aula = Aula::create($validated);

        BitacoraService::registrar('CREAR', 'aulas', $aula->id, 'Aula creada: ' . $aula->nombre_aula);
        BitacoraService::flash('Aula creada exitosamente', 'success');

        return redirect()->route('aulas.index');
    }

    public function edit(Aula $aula)
    {
        $this->authorize('edit', 'aulas');
        
        return Inertia::render('Aulas/Edit', [
            'aula' => $aula,
        ]);
    }

    public function update(Request $request, Aula $aula)
    {
        $this->authorize('edit', 'aulas');
        
        $validated = $request->validate([
            'nombre_aula' => 'required|string|max:255|unique:aulas,nombre_aula,' . $aula->id,
            'tipo' => 'required|string|in:Salón,Laboratorio,Auditorio',
            'capacidad' => 'required|integer|min:1|max:500',
            'estado' => 'required|in:activa,inactiva,mantenimiento',
        ]);

        $aula->update($validated);

        BitacoraService::registrar('ACTUALIZAR', 'aulas', $aula->id, 'Aula actualizada');
        BitacoraService::flash('Aula actualizada exitosamente', 'success');

        return redirect()->route('aulas.index');
    }

    public function destroy(Aula $aula)
    {
        $this->authorize('delete', 'aulas');
        
        if ($aula->horarios()->exists()) {
            return back()->withErrors(['error' => 'No puede eliminar un aula con horarios asignados']);
        }

        BitacoraService::registrar('ELIMINAR', 'aulas', $aula->id, 'Aula eliminada: ' . $aula->nombre_aula);
        
        $aula->delete();

        BitacoraService::flash('Aula eliminada exitosamente', 'success');

        return redirect()->route('aulas.index');
    }

    public function disponibles(Request $request)
    {
        $this->authorize('view', 'aulas');
        
        $dia_semana = $request->input('dia_semana');
        $hora_inicio = $request->input('hora_inicio');
        $capacidad_minima = $request->input('capacidad_minima', 0);

        // Base query: aulas activas
        $query = Aula::where('estado', 'activa');

        // Si hay capacidad mínima, filtrar
        if ($capacidad_minima > 0) {
            $query->where('capacidad', '>=', $capacidad_minima);
        }

        // Si hay día y hora, filtrar por disponibilidad
        if ($dia_semana && $hora_inicio) {
            // Convertir hora_inicio (formato HH:MM) a rango de 1 hora
            $horaFin = date('H:i', strtotime($hora_inicio . ' +1 hour'));

            // Excluir aulas que tienen horarios ocupados en ese día y horario
            $query->whereDoesntHave('horarios', function ($subquery) use ($dia_semana, $hora_inicio, $horaFin) {
                $subquery->where('dia_semana', $dia_semana)
                    ->where(function ($q) use ($hora_inicio, $horaFin) {
                        // Conflicto: hora_inicio < horaFin AND hora_fin > hora_inicio
                        $q->where('hora_inicio', '<', $horaFin)
                          ->where('hora_fin', '>', $hora_inicio);
                    });
            });
        } elseif ($dia_semana) {
            // Si solo hay día, excluir aulas con cualquier horario en ese día
            $query->whereDoesntHave('horarios', function ($subquery) use ($dia_semana) {
                $subquery->where('dia_semana', $dia_semana);
            });
        }

        $aulasDisponibles = $query->get();

        return response()->json($aulasDisponibles);
    }
}
