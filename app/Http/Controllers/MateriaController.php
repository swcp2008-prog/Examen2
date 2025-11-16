<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MateriaController extends Controller
{
    public function index()
    {
        $this->authorize('view', 'materias');
        
        $materias = Materia::paginate(15);

        return Inertia::render('Materias/Index', [
            'materias' => $materias,
        ]);
    }

    public function create()
    {
        $this->authorize('create', 'materias');
        
        return Inertia::render('Materias/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', 'materias');
        
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:materias,codigo',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $materia = Materia::create($validated);

        BitacoraService::registrar('CREAR', 'materias', $materia->id, 'Materia creada: ' . $materia->nombre);
        BitacoraService::flash('Materia creada exitosamente', 'success');

        return redirect()->route('materias.index');
    }

    public function edit(Materia $materia)
    {
        $this->authorize('edit', 'materias');
        
        return Inertia::render('Materias/Edit', [
            'materia' => $materia,
        ]);
    }

    public function update(Request $request, Materia $materia)
    {
        $this->authorize('edit', 'materias');
        
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:materias,codigo,' . $materia->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $materia->update($validated);

        BitacoraService::registrar('ACTUALIZAR', 'materias', $materia->id, 'Materia actualizada');
        BitacoraService::flash('Materia actualizada exitosamente', 'success');

        return redirect()->route('materias.index');
    }

    public function destroy(Materia $materia)
    {
        $this->authorize('delete', 'materias');
        
        if ($materia->grupoMaterias()->exists()) {
            return back()->withErrors(['error' => 'No puede eliminar una materia con grupos asignados']);
        }

        BitacoraService::registrar('ELIMINAR', 'materias', $materia->id, 'Materia eliminada: ' . $materia->nombre);
        
        $materia->delete();

        BitacoraService::flash('Materia eliminada exitosamente', 'success');

        return redirect()->route('materias.index');
    }
}
