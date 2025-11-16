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
        
        $dia = $request->input('dia');
        $horaInicio = $request->input('hora_inicio');
        $horaFin = $request->input('hora_fin');

        $aulasDisponibles = Aula::whereDoesntHave('horarios', function ($query) use ($dia, $horaInicio, $horaFin) {
            $query->where('dia_semana', $dia)
                ->where('estado', 'activo')
                ->where(function ($q) use ($horaInicio, $horaFin) {
                    $q->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                        ->orWhereBetween('hora_fin', [$horaInicio, $horaFin]);
                });
        })->where('estado', 'activa')
        ->get();

        return response()->json($aulasDisponibles);
    }
}
