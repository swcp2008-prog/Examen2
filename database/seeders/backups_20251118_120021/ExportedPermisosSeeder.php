<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedPermisosSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        DB::table('permisos')->truncate();

        DB::table('permisos')->insert([
            array (
  'id' => 1,
  'nombre' => 'usuarios.crear',
  'descripcion' => 'Crear usuarios',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 2,
  'nombre' => 'usuarios.editar',
  'descripcion' => 'Editar usuarios',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 3,
  'nombre' => 'usuarios.eliminar',
  'descripcion' => 'Eliminar usuarios',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 4,
  'nombre' => 'usuarios.ver',
  'descripcion' => 'Ver usuarios',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 5,
  'nombre' => 'roles.crear',
  'descripcion' => 'Crear roles',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 6,
  'nombre' => 'roles.editar',
  'descripcion' => 'Editar roles',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 7,
  'nombre' => 'roles.eliminar',
  'descripcion' => 'Eliminar roles',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 8,
  'nombre' => 'roles.ver',
  'descripcion' => 'Ver roles',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 9,
  'nombre' => 'bitacora.ver',
  'descripcion' => 'Ver bitácora',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 10,
  'nombre' => 'bitacora.exportar',
  'descripcion' => 'Exportar bitácora',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 11,
  'nombre' => 'aulas.crear',
  'descripcion' => 'Crear aulas',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 12,
  'nombre' => 'aulas.editar',
  'descripcion' => 'Editar aulas',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 13,
  'nombre' => 'aulas.eliminar',
  'descripcion' => 'Eliminar aulas',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 14,
  'nombre' => 'aulas.ver',
  'descripcion' => 'Ver aulas',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 15,
  'nombre' => 'horarios.crear',
  'descripcion' => 'Crear horarios',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 16,
  'nombre' => 'horarios.editar',
  'descripcion' => 'Editar horarios',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 17,
  'nombre' => 'horarios.eliminar',
  'descripcion' => 'Eliminar horarios',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 18,
  'nombre' => 'horarios.ver',
  'descripcion' => 'Ver horarios',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 19,
  'nombre' => 'materias.crear',
  'descripcion' => 'Crear materias',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 20,
  'nombre' => 'materias.editar',
  'descripcion' => 'Editar materias',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 21,
  'nombre' => 'materias.eliminar',
  'descripcion' => 'Eliminar materias',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 22,
  'nombre' => 'materias.ver',
  'descripcion' => 'Ver materias',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 23,
  'nombre' => 'grupos.crear',
  'descripcion' => 'Crear grupos',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 24,
  'nombre' => 'grupos.editar',
  'descripcion' => 'Editar grupos',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 25,
  'nombre' => 'grupos.eliminar',
  'descripcion' => 'Eliminar grupos',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 26,
  'nombre' => 'grupos.ver',
  'descripcion' => 'Ver grupos',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 27,
  'nombre' => 'asistencia.crear',
  'descripcion' => 'Registrar asistencia',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 28,
  'nombre' => 'asistencia.editar',
  'descripcion' => 'Editar asistencia',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 29,
  'nombre' => 'asistencia.ver',
  'descripcion' => 'Ver asistencia',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 30,
  'nombre' => 'asistencia.exportar',
  'descripcion' => 'Exportar asistencia',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 31,
  'nombre' => 'reportes.generar',
  'descripcion' => 'Generar reportes',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 32,
  'nombre' => 'reportes.pdf',
  'descripcion' => 'Exportar a PDF',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 33,
  'nombre' => 'reportes.excel',
  'descripcion' => 'Exportar a Excel',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
)
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}