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
}
