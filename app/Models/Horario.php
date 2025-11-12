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

    public function grupoMaterias()
    {
        return $this->hasMany(GrupoMateria::class);
    }
}
