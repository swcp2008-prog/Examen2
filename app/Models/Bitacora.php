<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacoras';
    protected $fillable = ['user_id', 'accion', 'tabla_afectada', 'registro_id', 'detalles', 'ip_origen', 'user_agent', 'fecha_hora'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
