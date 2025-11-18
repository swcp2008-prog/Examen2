<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Database\Seeder;

class RolPermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Rol::where('nombre', 'Administrador')->first();
        $docente = Rol::where('nombre', 'Docente')->first();
        $coordinador = Rol::where('nombre', 'Coordinador')->first();

        // Admin: todos los permisos
        $todosPermisos = Permiso::pluck('id')->toArray();
        $admin->permisos()->sync($todosPermisos);

        // Docente: ver horarios, registrar asistencia, ver asistencia
        $docentePermisos = Permiso::whereIn('nombre', [
            'horarios.ver',
            'asistencia.crear',
            'asistencia.ver',
            'reportes.generar',
            'reportes.pdf',
            'reportes.excel',
        ])->pluck('id')->toArray();
        $docente->permisos()->sync($docentePermisos);

        // Coordinador: gestión completa de horarios y grupos
        $coordinadorPermisos = Permiso::whereIn('nombre', [
            'aulas.ver',
            'aulas.crear',
            'aulas.editar',
            'horarios.ver',
            'horarios.crear',
            'horarios.editar',
            'horarios.eliminar',
            'materias.ver',
            'materias.crear',
            'materias.editar',
            'grupos.ver',
            'grupos.crear',
            'grupos.editar',
            'grupos.eliminar',
            'asistencia.ver',
            'reportes.generar',
            'reportes.pdf',
            'reportes.excel',
        ])->pluck('id')->toArray();
        $coordinador->permisos()->sync($coordinadorPermisos);
    }
}
