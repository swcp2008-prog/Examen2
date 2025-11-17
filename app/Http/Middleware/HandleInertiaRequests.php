<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth.user' => fn () => $request->user() ? $request->user()->load(['rol', 'docente']) : null,
            'user' => fn () => $request->user() ? (function () use ($request) {
                $u = $request->user();
                // map common menu permissions using the defined gates (crear/view/edit/...)
                return [
                    'id' => $u->id,
                    'nombre' => $u->nombre,
                    'apellido' => $u->apellido,
                    'email' => $u->email,
                    'rol' => $u->rol?->nombre ?? 'Sin rol',
                    'docente_id' => $u->docente?->id ?? null,
                    'is_docente' => strtolower($u->rol?->nombre ?? '') === 'docente',
                    'can_view_usuarios' => $u->can('view', 'usuarios'),
                    'can_view_roles' => $u->can('view', 'roles'),
                    'can_view_aulas' => $u->can('view', 'aulas'),
                    'can_view_horarios' => $u->can('view', 'horarios'),
                    'can_create_horarios' => $u->can('create', 'horarios'),
                    'can_edit_horarios' => $u->can('edit', 'horarios'),
                    'can_delete_horarios' => $u->can('delete', 'horarios'),
                    'can_generate_horarios' => $u->can('create', 'horarios'),
                    'can_view_materias' => $u->can('view', 'materias'),
                    'can_view_grupos' => $u->can('view', 'grupos'),
                    'can_view_asistencias' => $u->can('view', 'asistencia'),
                    'can_view_bitacora' => $u->can('view', 'bitacora'),
                    'can_generate_reportes' => $u->can('generar', 'reportes'),
                ];
            })() : null,
            'jetstream' => [
                'flash' => $request->session()->get('jetstream.flash'),
            ],
        ];
    }
}
