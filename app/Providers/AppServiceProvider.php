<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Definir gates para permisos
        Gate::define('crear', function (User $user, string $recurso) {
            return $user->rol?->permisos()
                ->where('nombre', "{$recurso}.crear")
                ->exists() ?? false;
        });

        Gate::define('create', function (User $user, string $recurso) {
            return $user->rol?->permisos()
                ->where('nombre', "{$recurso}.crear")
                ->exists() ?? false;
        });

        Gate::define('view', function (User $user, string $recurso) {
            return $user->rol?->permisos()
                ->where('nombre', "{$recurso}.ver")
                ->exists() ?? false;
        });

        Gate::define('edit', function (User $user, string $recurso) {
            return $user->rol?->permisos()
                ->where('nombre', "{$recurso}.editar")
                ->exists() ?? false;
        });

        Gate::define('delete', function (User $user, string $recurso) {
            return $user->rol?->permisos()
                ->where('nombre', "{$recurso}.eliminar")
                ->exists() ?? false;
        });

        Gate::define('exportar', function (User $user, string $recurso) {
            return $user->rol?->permisos()
                ->where('nombre', "{$recurso}.exportar")
                ->exists() ?? false;
        });

        Gate::define('generar', function (User $user, string $recurso) {
            return $user->rol?->permisos()
                ->where('nombre', "{$recurso}.generar")
                ->exists() ?? false;
        });
    }
}
