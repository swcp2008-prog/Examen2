<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $this->authorize('view', 'usuarios');
        
        $usuarios = User::with('rol')
            ->paginate(15);

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
        ]);
    }

    public function create()
    {
        $this->authorize('create', 'usuarios');
        
        $roles = Rol::all();
        
        return Inertia::render('Usuarios/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', 'usuarios');
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'rol_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $usuario = User::create($validated);

        BitacoraService::registrar('CREAR', 'usuarios', $usuario->id, 'Usuario creado: ' . $usuario->email);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente');
    }

    public function edit(User $usuario)
    {
        $this->authorize('edit', 'usuarios');
        
        $roles = Rol::all();
        
        return Inertia::render('Usuarios/Edit', [
            'usuario' => $usuario,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $this->authorize('edit', 'usuarios');
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'rol_id' => 'required|exists:roles,id',
            'estado' => 'required|in:activo,inactivo',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $usuario->update($validated);

        BitacoraService::registrar('ACTUALIZAR', 'usuarios', $usuario->id, 'Usuario actualizado');

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy(User $usuario)
    {
        $this->authorize('delete', 'usuarios');
        
        BitacoraService::registrar('ELIMINAR', 'usuarios', $usuario->id, 'Usuario eliminado: ' . $usuario->email);
        
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente');
    }

    public function cambiarContrasena(Request $request)
    {
        $validated = $request->validate([
            'password_actual' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $usuario = auth()->user();

        if (!Hash::check($validated['password_actual'], $usuario->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual es incorrecta']);
        }

        $usuario->update([
            'password' => Hash::make($validated['password']),
        ]);

        BitacoraService::registrar('CAMBIAR_CONTRASEÑA', 'usuarios', $usuario->id, 'Contraseña cambiad');

        return redirect()->back()
            ->with('success', 'Contraseña actualizada exitosamente');
    }
}
