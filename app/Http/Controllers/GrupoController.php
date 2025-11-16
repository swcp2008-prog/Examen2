<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GrupoController extends Controller
{
    public function index()
    {
        $this->authorize('view', 'grupos');
        
        $grupos = Grupo::paginate(15);

        return Inertia::render('Grupos/Index', [
            'grupos' => $grupos,
        ]);
    }

    public function create()
    {
        $this->authorize('create', 'grupos');
        
        return Inertia::render('Grupos/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', 'grupos');
        
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:grupos,codigo',
            'nombre' => 'required|string|max:255',
        ]);

        $grupo = Grupo::create($validated);

        BitacoraService::registrar('CREAR', 'grupos', $grupo->id, 'Grupo creado: ' . $grupo->nombre);
        BitacoraService::flash('Grupo creado exitosamente', 'success');

        return redirect()->route('grupos.index');
    }

    public function edit(Grupo $grupo)
    {
        $this->authorize('edit', 'grupos');
        
        return Inertia::render('Grupos/Edit', [
            'grupo' => $grupo,
        ]);
    }

    public function update(Request $request, Grupo $grupo)
    {
        $this->authorize('edit', 'grupos');
        
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:grupos,codigo,' . $grupo->id,
            'nombre' => 'required|string|max:255',
        ]);

        $grupo->update($validated);

        BitacoraService::registrar('ACTUALIZAR', 'grupos', $grupo->id, 'Grupo actualizado');
        BitacoraService::flash('Grupo actualizado exitosamente', 'success');

        return redirect()->route('grupos.index');
    }

    public function destroy(Grupo $grupo)
    {
        $this->authorize('delete', 'grupos');
        
        if ($grupo->grupoMaterias()->exists()) {
            return back()->withErrors(['error' => 'No puede eliminar un grupo con materias asignadas']);
        }

        BitacoraService::registrar('ELIMINAR', 'grupos', $grupo->id, 'Grupo eliminado: ' . $grupo->nombre);
        
        $grupo->delete();

        BitacoraService::flash('Grupo eliminado exitosamente', 'success');

        return redirect()->route('grupos.index');
    }
}
