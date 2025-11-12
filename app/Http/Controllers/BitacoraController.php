<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view', 'bitacora');
        
        $query = Bitacora::with('user')->latest();

        // Filtros
        if ($request->filled('tabla')) {
            $query->where('tabla_afectada', $request->input('tabla'));
        }

        if ($request->filled('accion')) {
            $query->where('accion', 'like', '%' . $request->input('accion') . '%');
        }

        if ($request->filled('usuario_id')) {
            $query->where('user_id', $request->input('usuario_id'));
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_hora', [
                $request->input('fecha_inicio') . ' 00:00:00',
                $request->input('fecha_fin') . ' 23:59:59',
            ]);
        }

        $bitacoras = $query->paginate(20);

        return Inertia::render('Bitacora/Index', [
            'bitacoras' => $bitacoras,
            'filters' => $request->only(['tabla', 'accion', 'usuario_id', 'fecha_inicio', 'fecha_fin']),
        ]);
    }

    public function show(Bitacora $bitacora)
    {
        return Inertia::render('Bitacora/Show', [
            'bitacora' => $bitacora->load('user'),
        ]);
    }

    public function exportar(Request $request)
    {
        $this->authorize('exportar', 'bitacora');
        
        $query = Bitacora::with('user')->latest();

        if ($request->filled('tabla')) {
            $query->where('tabla_afectada', $request->input('tabla'));
        }

        if ($request->filled('accion')) {
            $query->where('accion', 'like', '%' . $request->input('accion') . '%');
        }

        $bitacoras = $query->get();

        $csv = "ID,Usuario,Acción,Tabla,Registro ID,IP,Fecha\n";
        foreach ($bitacoras as $bitacora) {
            $csv .= "{$bitacora->id},\"{$bitacora->user->nombre} {$bitacora->user->apellido}\",{$bitacora->accion},{$bitacora->tabla_afectada},{$bitacora->registro_id},{$bitacora->ip_origen},{$bitacora->fecha_hora}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="bitacora.csv"');
    }
}
