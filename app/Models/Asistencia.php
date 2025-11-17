<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencias';
    protected $fillable = ['grupo_materia_id', 'docente_id', 'fecha', 'hora_entrada', 'hora_salida', 'estado', 'observaciones'];
    
    protected $casts = [
        'fecha' => 'datetime',
        'hora_entrada' => 'string',
        'hora_salida' => 'string',
    ];

    public function grupoMateria()
    {
        return $this->belongsTo(GrupoMateria::class);
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }
}
