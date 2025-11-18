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
        try {
            $this->authorize('view', 'aulas');

            $dia_semana = $request->input('dia_semana');
            $hora_inicio = $request->input('hora_inicio');
            $capacidad_minima = $request->input('capacidad_minima', 0);

            if (!$dia_semana) {
                return response()->json(['error' => 'Día es requerido'], 400);
            }

            $aulasActivas = Aula::where('estado', 'activa');
            if ($capacidad_minima > 0) {
                $aulasActivas->where('capacidad', '>=', $capacidad_minima);
            }

            $aulas = $aulasActivas->get();
            $aulasDisponibles = [];

            $apertura = '07:00';
            $cierre = '22:00';
            $slotStart = $hora_inicio;
            $slotEnd = $hora_inicio ? date('H:i', strtotime($hora_inicio . ' +1 hour')) : null;

            foreach ($aulas as $aula) {
                $horariosOcupados = $aula->horarios()
                    ->where('dia_semana', $dia_semana)
                    ->where('estado', 'activo')
                    ->get()
                    ->map(function ($h) {
                        return ['start' => date('H:i', strtotime($h->hora_inicio)), 'end' => date('H:i', strtotime($h->hora_fin))];
                    })->toArray();

                usort($horariosOcupados, function ($a, $b) {
                    return strcmp($a['start'], $b['start']);
                });

                $merged = [];
                foreach ($horariosOcupados as $int) {
                    if (empty($merged)) { $merged[] = $int; continue; }
                    $last =& $merged[count($merged) - 1];
                    if ($int['start'] <= $last['end']) {
                        if ($int['end'] > $last['end']) { $last['end'] = $int['end']; }
                    } else { $merged[] = $int; }
                }

                $free = [];
                $cursor = $apertura;
                foreach ($merged as $m) {
                    if (strcmp($m['start'], $cursor) > 0) { $free[] = ['start' => $cursor, 'end' => $m['start']]; }
                    $cursor = (strcmp($m['end'], $cursor) > 0) ? $m['end'] : $cursor;
                }
                if (strcmp($cursor, $cierre) < 0) { $free[] = ['start' => $cursor, 'end' => $cierre]; }

                $isFreeForSlot = true;
                if ($slotStart && $slotEnd) {
                    $isFreeForSlot = false;
                    foreach ($free as $f) {
                        if (strcmp($slotStart, $f['start']) >= 0 && strcmp($slotEnd, $f['end']) <= 0) { $isFreeForSlot = true; break; }
                    }
                }

                if ($isFreeForSlot) {
                    $aulasDisponibles[] = array_merge($aula->toArray(), ['intervalos_disponibles' => $free]);
                }
            }

            return response()->json($aulasDisponibles);
        } catch (\Throwable $e) {
            \Log::error('Error en AulaController::disponibles - ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }
}
