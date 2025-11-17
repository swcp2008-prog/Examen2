<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\GrupoMateria;
use App\Models\Docente;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    public function index()
    {
        $this->authorize('view', 'asistencia');
        
        $user = request()->user();

        $query = Asistencia::with(['grupoMateria.grupo', 'docente.user'])->latest();

        // Si es docente y no tiene permisos amplios, mostrar sólo sus asistencias
        if ($user && $user->docente && !in_array(strtolower($user->rol?->nombre ?? ''), ['admin','coordinador'])) {
            $query->where('docente_id', $user->docente->id);
        }

        $asistencias = $query->paginate(20);

        return Inertia::render('Asistencias/Index', [
            'asistencias' => $asistencias,
        ]);
    }

    public function create()
    {
        $this->authorize('crear', 'asistencia');
        $user = request()->user();

        if ($user && $user->docente && !in_array(strtolower($user->rol?->nombre ?? ''), ['admin','coordinador'])) {
            // Docente: limitar opciones a sí mismo y a sus grupo-materias
            $docentes = Docente::with('user')->where('id', $user->docente->id)->get();
            $grupoMaterias = GrupoMateria::whereHas('docentes', function ($q) use ($user) {
                $q->where('docente_id', $user->docente->id);
            })->with('grupo', 'materia')->get();
        } else {
            $grupoMaterias = GrupoMateria::with('grupo', 'materia')->get();
            $docentes = Docente::with('user')->get();
        }

        return Inertia::render('Asistencias/Create', [
            'grupoMaterias' => $grupoMaterias,
            'docentes' => $docentes,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('crear', 'asistencia');
        
        $validated = $request->validate([
            'grupo_materia_id' => 'required|exists:grupo_materias,id',
            'docente_id' => 'required|exists:docentes,id',
            'fecha' => 'required|date',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i',
            'estado' => 'required|in:presente,ausente,retardo,justificada',
            'observaciones' => 'nullable|string',
        ]);

        $user = $request->user();

        // Si es docente sin permisos amplios, forzar docente_id al suyo
        if ($user && $user->docente && !in_array(strtolower($user->rol?->nombre ?? ''), ['admin','coordinador'])) {
            $validated['docente_id'] = $user->docente->id;
        }

        $asistencia = Asistencia::create($validated);

        BitacoraService::registrar('CREAR', 'asistencias', $asistencia->id, 'Asistencia registrada');

        BitacoraService::flash('Asistencia registrada exitosamente', 'success');

        return redirect()->route('asistencias.index');
    }

    public function registrarGrupo(Request $request)
    {
        $this->authorize('crear', 'asistencia');
        
        // NOTA: Este método registra la asistencia del docente en un grupo específico
        // No es para registrar múltiples estudiantes, sino para la asistencia del docente
        
        $validated = $request->validate([
            'grupo_materia_id' => 'required|exists:grupo_materias,id',
            'docente_id' => 'required|exists:docentes,id',
            'fecha' => 'required|date',
            'hora_entrada' => 'nullable|date_format:H:i',
            'hora_salida' => 'nullable|date_format:H:i',
            'estado' => 'required|in:presente,ausente,retardo,justificada',
            'observaciones' => 'nullable|string',
        ]);
        $user = $request->user();

        // Si es docente sin permisos amplios, forzar docente_id al suyo
        if ($user && $user->docente && !in_array(strtolower($user->rol?->nombre ?? ''), ['admin','coordinador'])) {
            $validated['docente_id'] = $user->docente->id;
        }

        // Crear un único registro de asistencia del docente
        $asistencia = Asistencia::updateOrCreate(
            [
                'grupo_materia_id' => $validated['grupo_materia_id'],
                'docente_id' => $validated['docente_id'],
                'fecha' => $validated['fecha'],
            ],
            [
                'hora_entrada' => $validated['hora_entrada'],
                'hora_salida' => $validated['hora_salida'],
                'estado' => $validated['estado'],
                'observaciones' => $validated['observaciones'],
            ]
        );

        BitacoraService::registrar('CREAR', 'asistencias', $asistencia->id, 'Asistencia del docente en grupo registrada');

        BitacoraService::flash('Asistencia del docente registrada exitosamente', 'success');

        return redirect()->route('asistencias.index');
    }

    public function porDocenteGrupo(Request $request)
    {
        $this->authorize('view', 'asistencia');
        
        $user = $request->user();
        $docente = $request->input('docente_id');

        // Si es docente sin permisos amplios, forzar consulta a su propio id
        if ($user && $user->docente && !in_array(strtolower($user->rol?->nombre ?? ''), ['admin','coordinador'])) {
            $docente = $user->docente->id;
        }
        $grupo = $request->input('grupo_id');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = Asistencia::query();

        if ($docente) {
            $query->where('docente_id', $docente);
        }

        if ($grupo) {
            $query->whereHas('grupoMateria', function ($q) use ($grupo) {
                $q->where('grupo_id', $grupo);
            });
        }

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }

        $asistencias = $query->with(['grupoMateria.grupo', 'docente.user'])
            ->get()
            ->groupBy('fecha');

        return response()->json($asistencias);
    }

    public function exportar(Request $request)
    {
        $this->authorize('exportar', 'asistencia');
        
        $docente = $request->input('docente_id');
        $grupo = $request->input('grupo_id');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = Asistencia::query();

        if ($docente) {
            $query->where('docente_id', $docente);
        }

        if ($grupo) {
            $query->whereHas('grupoMateria', function ($q) use ($grupo) {
                $q->where('grupo_id', $grupo);
            });
        }

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }

        $asistencias = $query->with(['grupoMateria.grupo', 'grupoMateria.materia', 'docente.user'])->get();

        $csv = "Fecha,Docente,Grupo,Materia,Estado,Observaciones\n";
        foreach ($asistencias as $asistencia) {
            $csv .= "{$asistencia->fecha},{$asistencia->docente->user->nombre} {$asistencia->docente->user->apellido},{$asistencia->grupoMateria->grupo->nombre},{$asistencia->grupoMateria->materia->nombre},{$asistencia->estado},\"{$asistencia->observaciones}\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="asistencias.csv"');
    }
}
