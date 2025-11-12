<?php

namespace App\Services;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BitacoraService
{
    public static function registrar(string $accion, string $tabla, ?int $registro_id = null, ?string $detalles = null): void
    {
        if (!Auth::check()) {
            return;
        }

        Bitacora::create([
            'user_id' => Auth::id(),
            'accion' => $accion,
            'tabla_afectada' => $tabla,
            'registro_id' => $registro_id,
            'detalles' => $detalles,
            'ip_origen' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'fecha_hora' => now(),
        ]);
    }
}
