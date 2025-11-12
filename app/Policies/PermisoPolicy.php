<?php

namespace App\Policies;

use App\Models\User;

class PermisoPolicy
{
    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function verificar(User $usuario, string $permiso): bool
    {
        if (!$usuario->rol) {
            return false;
        }

        return $usuario->rol->permisos()
            ->where('nombre', $permiso)
            ->exists();
    }
}
