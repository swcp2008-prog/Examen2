<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\GrupoMateria;
use App\Models\Horario;
use App\Models\Asistencia;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DocenteController extends Controller
{
    public function index()
    {
        $this->authorize('view', 'usuarios');
        
        $docentes = Docente::with('user', 'grupoMaterias')
            ->paginate(15);

        // Cargar grupo-materias con información de horario (incluye aula) y docentes asignados
        $gruposMaterias = GrupoMateria::with(['grupo', 'materia', 'horario.aula', 'docentes.user'])
            ->get();

        return Inertia::render('Docentes/Index', [
            'docentes' => $docentes,
            'gruposMaterias' => $gruposMaterias,
        ]);
    }

    public function asignarGrupoMateria(Request $request, Docente $docente)
    {
        $this->authorize('edit', 'usuarios');
        
        $validated = $request->validate([
            'grupo_materia_id' => 'required|exists:grupo_materias,id',
        ]);
        // Obtener el grupo-materia con horario y docentes asignados
        $grupoMateria = GrupoMateria::with('horario.aula', 'docentes.user')->find($validated['grupo_materia_id']);

        // Verificar si ya está asignado a este mismo docente
        $existe = $docente->grupoMaterias()
            ->where('grupo_materia_id', $validated['grupo_materia_id'])
            ->exists();

        if ($existe) {
            return back()->withErrors(['error' => 'Este grupo-materia ya está asignado a este docente']);
        }

        // Verificar si ya está asignado a otro docente (no permitimos más de un docente por Grupo-Materia)
        if ($grupoMateria->docentes->isNotEmpty()) {
            // tomar el primer docente asignado para mensaje
            $otro = $grupoMateria->docentes->first();
            return back()->withErrors(['error' => 'Este Grupo-Materia ya está asignado al/la docente: ' . ($otro->user->nombre ?? 'N/A') . ' ' . ($otro->user->apellido ?? '')]);
        }

        // Verificar conflicto de horarios si existe horario asignado
        if ($grupoMateria->horario_id) {
            $conflicto = $docente->verificarConflictoHorario($grupoMateria->horario_id);
            
            if ($conflicto) {
                return back()->withErrors(['error' => $conflicto['mensaje']]);
            }
        }

        $docente->grupoMaterias()->attach($validated['grupo_materia_id']);

        BitacoraService::registrar('ASIGNAR', 'docente_grupo_materias', $docente->id, 'Grupo-Materia asignado a docente');
        BitacoraService::flash('Asignación realizada exitosamente', 'success');

        return redirect()->back();
    }

    public function desasignarGrupoMateria(Docente $docente, GrupoMateria $grupoMateria)
    {
        $this->authorize('edit', 'usuarios');
        
        $docente->grupoMaterias()->detach($grupoMateria->id);

        BitacoraService::registrar('DESASIGNAR', 'docente_grupo_materias', $docente->id, 'Grupo-Materia desasignado');
        BitacoraService::flash('Desasignación realizada exitosamente', 'success');

        return redirect()->back();
    }

    public function horarios(Docente $docente)
    {
        $this->authorize('view', 'horarios');
        
        $horariosDocente = Horario::whereHas('grupoMaterias', function ($query) use ($docente) {
            $query->whereHas('docentes', function ($q) use ($docente) {
                $q->where('docente_id', $docente->id);
            });
        })->with('grupoMaterias.grupo', 'grupoMaterias.materia', 'aula')->get();

        return Inertia::render('Docentes/Horarios', [
            'docente' => $docente->load('user', 'grupoMaterias.grupo', 'grupoMaterias.materia', 'grupoMaterias.horario.aula'),
            'horarios' => $horariosDocente,
        ]);
    }

    public function generarHorarios(Request $request)
    {
        $this->authorize('create', 'horarios');
        
        // Lógica automática para generar horarios para docentes
        // Esto puede ser personalizado según las reglas de tu institución
        
        return response()->json(['message' => 'Horarios generados exitosamente']);
    }

    public function edit(Docente $docente)
    {
        $this->authorize('edit', 'usuarios');
        
        return Inertia::render('Docentes/Edit', [
            'docente' => $docente->load('user'),
        ]);
    }

    public function update(Request $request, Docente $docente)
    {
        $this->authorize('edit', 'usuarios');
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $docente->user_id,
            'especialidad' => 'required|string|max:255',
            'fecha_contrato' => 'required|date',
            'estado' => 'required|in:activo,inactivo',
        ]);

        // Actualizar usuario
        $docente->user->update([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'email' => $validated['email'],
        ]);

        // Actualizar docente
        $docente->update([
            'especialidad' => $validated['especialidad'],
            'fecha_contrato' => $validated['fecha_contrato'],
            'estado' => $validated['estado'],
        ]);

        BitacoraService::registrar('ACTUALIZAR', 'docentes', $docente->id, 'Docente actualizado');
        BitacoraService::flash('Docente actualizado exitosamente', 'success');

        return redirect()->route('docentes.index');
    }

    /**
     * Obtener página de perfil del docente autenticado con sus grupos-materias
     */
    public function perfil()
    {
        $user = auth()->user();
        
        if (!$user || !$user->docente) {
            return redirect('/dashboard');
        }

        $docente = $user->docente->load('grupoMaterias.grupo', 'grupoMaterias.materia', 'grupoMaterias.horario.aula');
        
        // Obtener asistencias paginadas (20 por página)
        $asistencias = Asistencia::where('docente_id', $docente->id)
            ->with('grupoMateria.grupo', 'grupoMateria.materia')
            ->orderBy('fecha', 'desc')
            ->paginate(20);

        return Inertia::render('Docentes/Perfil', [
            'docente' => $docente,
            'asistencias' => $asistencias,
        ]);
    }

    /**
     * Registrar entrada de docente
     */
    public function registrarEntrada(Request $request)
    {
        $user = auth()->user();
        
        if (!$user || !$user->docente) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'grupo_materia_id' => 'required|exists:grupo_materias,id',
        ]);

        $docente = $user->docente;
        
        // Verificar que el docente tiene asignado este grupo-materia
        $grupoMateria = $docente->grupoMaterias()
            ->where('grupo_materia_id', $validated['grupo_materia_id'])
            ->first();

        if (!$grupoMateria) {
            return response()->json(['error' => 'Este grupo-materia no está asignado a tu perfil'], 403);
        }

        $hoy = Carbon::today();
        
        // Verificar si ya existe un registro para hoy
        $asistencia = Asistencia::where('docente_id', $docente->id)
            ->where('grupo_materia_id', $validated['grupo_materia_id'])
            ->whereDate('fecha', $hoy)
            ->first();

        if ($asistencia && $asistencia->hora_entrada) {
            return response()->json(['error' => 'Ya registraste entrada hoy para este grupo'], 400);
        }

        if (!$asistencia) {
            // Crear nuevo registro de asistencia
            $asistencia = Asistencia::create([
                'docente_id' => $docente->id,
                'grupo_materia_id' => $validated['grupo_materia_id'],
                'fecha' => $hoy,
                'hora_entrada' => Carbon::now()->format('H:i:s'),
                'estado' => 'presente',
            ]);
        } else {
            // Actualizar entrada si no existe
            $asistencia->update([
                'hora_entrada' => Carbon::now()->format('H:i:s'),
                'estado' => 'presente',
            ]);
        }

        BitacoraService::registrar('ENTRADA', 'asistencias', $asistencia->id, 'Entrada registrada');

        return response()->json([
            'message' => 'Entrada registrada exitosamente',
            'hora' => Carbon::now()->format('H:i'),
            'asistencia' => $asistencia->load('grupoMateria.grupo', 'grupoMateria.materia'),
        ]);
    }

    /**
     * Registrar salida de docente
     */
    public function registrarSalida(Request $request)
    {
        $user = auth()->user();
        
        if (!$user || !$user->docente) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'grupo_materia_id' => 'required|exists:grupo_materias,id',
        ]);

        $docente = $user->docente;
        
        // Verificar que el docente tiene asignado este grupo-materia
        $grupoMateria = $docente->grupoMaterias()
            ->where('grupo_materia_id', $validated['grupo_materia_id'])
            ->first();

        if (!$grupoMateria) {
            return response()->json(['error' => 'Este grupo-materia no está asignado a tu perfil'], 403);
        }

        $hoy = Carbon::today();
        
        // Obtener el registro de asistencia de hoy
        $asistencia = Asistencia::where('docente_id', $docente->id)
            ->where('grupo_materia_id', $validated['grupo_materia_id'])
            ->whereDate('fecha', $hoy)
            ->first();

        if (!$asistencia) {
            return response()->json(['error' => 'No hay registro de entrada para registrar salida'], 400);
        }

        if ($asistencia->hora_salida) {
            return response()->json(['error' => 'Ya registraste salida hoy para este grupo'], 400);
        }

        // Actualizar salida
        $asistencia->update([
            'hora_salida' => Carbon::now()->format('H:i:s'),
        ]);

        BitacoraService::registrar('SALIDA', 'asistencias', $asistencia->id, 'Salida registrada');

        return response()->json([
            'message' => 'Salida registrada exitosamente',
            'hora' => Carbon::now()->format('H:i'),
            'asistencia' => $asistencia->load('grupoMateria.grupo', 'grupoMateria.materia'),
        ]);
    }
}
