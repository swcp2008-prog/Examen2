<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $table = 'aulas';
    protected $fillable = ['nombre_aula', 'tipo', 'capacidad', 'estado'];

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
