<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RolController extends Controller
{
    public function index()
    {
        $this->authorize('view', 'roles');
        
        $roles = Rol::with('permisos')
            ->paginate(15);

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        $this->authorize('create', 'roles');
        
        $permisos = Permiso::all();
        
        return Inertia::render('Roles/Create', [
            'permisos' => $permisos,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', 'roles');
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre',
            'descripcion' => 'nullable|string',
            'permisos' => 'array|required',
            'permisos.*' => 'exists:permisos,id',
        ]);

        $rol = Rol::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        $rol->permisos()->sync($validated['permisos']);

        BitacoraService::registrar('CREAR', 'roles', $rol->id, 'Rol creado: ' . $rol->nombre);
        BitacoraService::flash('Rol creado exitosamente', 'success');

        return redirect()->route('roles.index');
    }

    public function edit(Rol $rol)
    {
        $this->authorize('edit', 'roles');
        
        $permisos = Permiso::all();
        $permisosSeleccionados = $rol->permisos->pluck('id')->toArray();
        
        return Inertia::render('Roles/Edit', [
            'rol' => $rol,
            'permisos' => $permisos,
            'permisosSeleccionados' => $permisosSeleccionados,
        ]);
    }

    public function update(Request $request, Rol $rol)
    {
        $this->authorize('edit', 'roles');
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre,' . $rol->id,
            'descripcion' => 'nullable|string',
            'permisos' => 'array|required',
            'permisos.*' => 'exists:permisos,id',
        ]);

        $rol->update([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        $rol->permisos()->sync($validated['permisos']);

        BitacoraService::registrar('ACTUALIZAR', 'roles', $rol->id, 'Rol actualizado');
        BitacoraService::flash('Rol actualizado exitosamente', 'success');

        return redirect()->route('roles.index');
    }

    public function destroy(Rol $rol)
    {
        $this->authorize('delete', 'roles');
        
        if ($rol->usuarios()->exists()) {
            return back()->withErrors(['error' => 'No puede eliminar un rol con usuarios asignados']);
        }

        BitacoraService::registrar('ELIMINAR', 'roles', $rol->id, 'Rol eliminado: ' . $rol->nombre);
        
        $rol->delete();

        BitacoraService::flash('Rol eliminado exitosamente', 'success');

        return redirect()->route('roles.index');
    }
}
