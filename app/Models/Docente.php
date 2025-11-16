<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docentes';
    protected $fillable = ['user_id', 'especialidad', 'fecha_contrato', 'estado'];
    protected $dates = ['fecha_contrato'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grupoMaterias()
    {
        return $this->belongsToMany(GrupoMateria::class, 'docente_grupo_materias', 'docente_id', 'grupo_materia_id');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    /**
     * Valida si hay conflicto de horarios para este docente
     * @param int $horarioId - ID del horario a asignar
     * @param int $grupoMateriaId - ID del grupo materia (para excluir si es edición)
     * @return array|null - Retorna información del conflicto o null si no hay conflicto
     */
    public function verificarConflictoHorario($horarioId, $grupoMateriaIdExcluir = null)
    {
        $horarioNuevo = Horario::find($horarioId);
        if (!$horarioNuevo) {
            return ['error' => 'Horario no encontrado'];
        }

        // Obtener todos los horarios asignados a este docente (ahora mediante pivot)
        $horariosDocente = $this->grupoMaterias()
            ->with('horarios')
            ->get()
            ->flatMap(fn($gm) => $gm->horarios)
            ->filter(fn($h) => !$grupoMateriaIdExcluir || !$h->pivot || $h->pivot->grupo_materia_id !== $grupoMateriaIdExcluir);

        foreach ($horariosDocente as $horarioExistente) {
            // Comparar si están en el mismo día
            if ($horarioNuevo->dia_semana === $horarioExistente->dia_semana) {
                $nuevoInicio = strtotime($horarioNuevo->hora_inicio);
                $nuevoFin = strtotime($horarioNuevo->hora_fin);
                $existenteInicio = strtotime($horarioExistente->hora_inicio);
                $existenteFin = strtotime($horarioExistente->hora_fin);

                if (!($nuevoFin <= $existenteInicio || $nuevoInicio >= $existenteFin)) {
                    return [
                        'conflicto' => true,
                        'mensaje' => "Conflicto de horario: el docente ya tiene asignado un horario el {$horarioExistente->dia_semana} de {$horarioExistente->hora_inicio} a {$horarioExistente->hora_fin}",
                        'horarioExistente' => $horarioExistente,
                    ];
                }
            }
        }

        return null;
    }
}
