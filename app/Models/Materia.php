<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materias';
    protected $fillable = ['codigo', 'nombre', 'descripcion'];

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_materias', 'materia_id', 'grupo_id')
            ->withPivot('horario_id')
            ->withTimestamps();
    }

    public function grupoMaterias()
    {
        return $this->hasMany(GrupoMateria::class);
    }
}
