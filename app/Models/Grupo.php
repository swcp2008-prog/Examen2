<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupos';
    protected $fillable = ['codigo', 'nombre'];

    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'grupo_materias', 'grupo_id', 'materia_id')
            ->withPivot('horario_id')
            ->withTimestamps();
    }

    public function grupoMaterias()
    {
        return $this->hasMany(GrupoMateria::class);
    }
}
