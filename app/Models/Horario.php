<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horarios';
    protected $fillable = ['aula_id', 'dia_semana', 'hora_inicio', 'hora_fin', 'estado'];

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    // Un horario puede pertenecer a varias asignaciones (pivot)
    public function grupoMaterias()
    {
        return $this->belongsToMany(GrupoMateria::class, 'grupo_materia_horario', 'horario_id', 'grupo_materia_id')->withTimestamps();
    }
}
