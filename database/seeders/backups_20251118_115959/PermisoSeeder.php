<?php

namespace Database\Seeders;

use App\Models\Permiso;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            // Usuarios
            ['nombre' => 'usuarios.crear', 'descripcion' => 'Crear usuarios'],
            ['nombre' => 'usuarios.editar', 'descripcion' => 'Editar usuarios'],
            ['nombre' => 'usuarios.eliminar', 'descripcion' => 'Eliminar usuarios'],
            ['nombre' => 'usuarios.ver', 'descripcion' => 'Ver usuarios'],
            
            // Roles
            ['nombre' => 'roles.crear', 'descripcion' => 'Crear roles'],
            ['nombre' => 'roles.editar', 'descripcion' => 'Editar roles'],
            ['nombre' => 'roles.eliminar', 'descripcion' => 'Eliminar roles'],
            ['nombre' => 'roles.ver', 'descripcion' => 'Ver roles'],
            
            // Bitácora
            ['nombre' => 'bitacora.ver', 'descripcion' => 'Ver bitácora'],
            ['nombre' => 'bitacora.exportar', 'descripcion' => 'Exportar bitácora'],
            
            // Aulas
            ['nombre' => 'aulas.crear', 'descripcion' => 'Crear aulas'],
            ['nombre' => 'aulas.editar', 'descripcion' => 'Editar aulas'],
            ['nombre' => 'aulas.eliminar', 'descripcion' => 'Eliminar aulas'],
            ['nombre' => 'aulas.ver', 'descripcion' => 'Ver aulas'],
            
            // Horarios
            ['nombre' => 'horarios.crear', 'descripcion' => 'Crear horarios'],
            ['nombre' => 'horarios.editar', 'descripcion' => 'Editar horarios'],
            ['nombre' => 'horarios.eliminar', 'descripcion' => 'Eliminar horarios'],
            ['nombre' => 'horarios.ver', 'descripcion' => 'Ver horarios'],
            
            // Materias
            ['nombre' => 'materias.crear', 'descripcion' => 'Crear materias'],
            ['nombre' => 'materias.editar', 'descripcion' => 'Editar materias'],
            ['nombre' => 'materias.eliminar', 'descripcion' => 'Eliminar materias'],
            ['nombre' => 'materias.ver', 'descripcion' => 'Ver materias'],
            
            // Grupos
            ['nombre' => 'grupos.crear', 'descripcion' => 'Crear grupos'],
            ['nombre' => 'grupos.editar', 'descripcion' => 'Editar grupos'],
            ['nombre' => 'grupos.eliminar', 'descripcion' => 'Eliminar grupos'],
            ['nombre' => 'grupos.ver', 'descripcion' => 'Ver grupos'],
            
            // Asistencia
            ['nombre' => 'asistencia.crear', 'descripcion' => 'Registrar asistencia'],
            ['nombre' => 'asistencia.editar', 'descripcion' => 'Editar asistencia'],
            ['nombre' => 'asistencia.ver', 'descripcion' => 'Ver asistencia'],
            ['nombre' => 'asistencia.exportar', 'descripcion' => 'Exportar asistencia'],
            
            // Reportes
            ['nombre' => 'reportes.generar', 'descripcion' => 'Generar reportes'],
            ['nombre' => 'reportes.pdf', 'descripcion' => 'Exportar a PDF'],
            ['nombre' => 'reportes.excel', 'descripcion' => 'Exportar a Excel'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::firstOrCreate(
                ['nombre' => $permiso['nombre']],
                $permiso
            );
        }
    }
}
