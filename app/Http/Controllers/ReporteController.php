<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Bitacora;
use App\Models\Docente;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function asistenciaPdf(Request $request)
    {
        $this->authorize('generar', 'reportes');
        
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

        $asistencias = $query->with(['grupoMateria', 'docente'])->get();

        // Preparar datos para el PDF
        $data = $asistencias->map(function ($asistencia) {
            $fecha = is_string($asistencia->fecha) ? Carbon::parse($asistencia->fecha) : $asistencia->fecha;
            return [
                'fecha' => $fecha->format('d/m/Y'),
                'docente' => $asistencia->docente->user->nombre . ' ' . $asistencia->docente->user->apellido,
                'grupo' => $asistencia->grupoMateria->grupo->nombre,
                'materia' => $asistencia->grupoMateria->materia->nombre,
                'estado' => ucfirst($asistencia->estado),
                'observaciones' => $asistencia->observaciones,
            ];
        })->toArray();

        $pdf = Pdf::loadView('reportes.asistencia_pdf', [
            'titulo' => 'Reporte de Asistencia',
            'fecha_generacion' => now()->format('d/m/Y H:i'),
            'data' => $data,
        ]);

        return $pdf->download('reporte_asistencia_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function asistenciaExcel(Request $request)
    {
        $this->authorize('generar', 'reportes');
        
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

        $asistencias = $query->with(['grupoMateria', 'docente'])->get();

        $csv = "Fecha,Docente,Grupo,Materia,Estado,Observaciones\n";
        foreach ($asistencias as $asistencia) {
            $docente_nombre = $asistencia->docente->user->nombre . ' ' . $asistencia->docente->user->apellido;
            $fecha = is_string($asistencia->fecha) ? Carbon::parse($asistencia->fecha) : $asistencia->fecha;
            $csv .= "{$fecha->format('d/m/Y')},\"{$docente_nombre}\",\"{$asistencia->grupoMateria->grupo->nombre}\",\"{$asistencia->grupoMateria->materia->nombre}\",{$asistencia->estado},\"{$asistencia->observaciones}\"\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="reporte_asistencia_' . now()->format('Y-m-d') . '.csv"');
    }

    public function bitacoraPdf(Request $request)
    {
        $this->authorize('generar', 'reportes');
        
        $tabla = $request->input('tabla');
        $accion = $request->input('accion');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = Bitacora::with('user');

        if ($tabla) {
            $query->where('tabla_afectada', $tabla);
        }

        if ($accion) {
            $query->where('accion', 'like', '%' . $accion . '%');
        }

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha_hora', [
                $fechaInicio . ' 00:00:00',
                $fechaFin . ' 23:59:59',
            ]);
        }

        $bitacoras = $query->latest()->get();

        // Preparar datos para el PDF
        $data = $bitacoras->map(function ($bitacora) {
            return [
                'fecha' => $bitacora->fecha_hora->format('d/m/Y H:i:s'),
                'usuario' => $bitacora->user->nombre . ' ' . $bitacora->user->apellido,
                'accion' => $bitacora->accion,
                'tabla' => $bitacora->tabla_afectada,
                'ip' => $bitacora->ip_origen,
            ];
        })->toArray();

        $pdf = Pdf::loadView('reportes.bitacora_pdf', [
            'titulo' => 'Reporte de Bitácora',
            'fecha_generacion' => now()->format('d/m/Y H:i'),
            'data' => $data,
        ]);

        return $pdf->download('reporte_bitacora_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    public function bitacoraExcel(Request $request)
    {
        $this->authorize('generar', 'reportes');
        
        $tabla = $request->input('tabla');
        $accion = $request->input('accion');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = Bitacora::with('user');

        if ($tabla) {
            $query->where('tabla_afectada', $tabla);
        }

        if ($accion) {
            $query->where('accion', 'like', '%' . $accion . '%');
        }

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha_hora', [
                $fechaInicio . ' 00:00:00',
                $fechaFin . ' 23:59:59',
            ]);
        }

        $bitacoras = $query->latest()->get();

        $csv = "Fecha,Usuario,Acción,Tabla,IP Origen\n";
        foreach ($bitacoras as $bitacora) {
            $usuario_nombre = $bitacora->user->nombre . ' ' . $bitacora->user->apellido;
            $csv .= "{$bitacora->fecha_hora->format('d/m/Y H:i:s')},\"{$usuario_nombre}\",{$bitacora->accion},{$bitacora->tabla_afectada},{$bitacora->ip_origen}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="reporte_bitacora_' . now()->format('Y-m-d') . '.csv"');
    }

    public function horariosSemanales(Request $request)
    {
        $this->authorize('view', 'horarios');
        
        $docenteId = $request->input('docente_id');
        $grupoId = $request->input('grupo_id');

        if ($docenteId) {
            $horarios = Docente::find($docenteId)
                ->grupoMaterias()
                ->with('horario.aula')
                ->get()
                ->pluck('horario')
                ->groupBy('dia_semana');
        } elseif ($grupoId) {
            $horarios = \App\Models\Grupo::find($grupoId)
                ->grupoMaterias()
                ->with('horario.aula')
                ->get()
                ->pluck('horario')
                ->groupBy('dia_semana');
        } else {
            $horarios = [];
        }

        return response()->json($horarios);
    }
}
