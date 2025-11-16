<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoMateria extends Model
{
    protected $table = 'grupo_materias';
    protected $fillable = ['grupo_id', 'materia_id', 'horario_id'];

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    // Ahora una asignación puede tener varios horarios (teoría/práctica)
    public function horarios()
    {
        return $this->belongsToMany(Horario::class, 'grupo_materia_horario', 'grupo_materia_id', 'horario_id')->withTimestamps();
    }

    public function docentes()
    {
        return $this->belongsToMany(Docente::class, 'docente_grupo_materias', 'grupo_materia_id', 'docente_id');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}
