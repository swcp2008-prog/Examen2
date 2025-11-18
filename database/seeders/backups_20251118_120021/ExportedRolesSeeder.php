<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedRolesSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        DB::table('roles')->truncate();

        DB::table('roles')->insert([
            array (
  'id' => 1,
  'nombre' => 'Administrador',
  'descripcion' => 'Acceso total al sistema',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 2,
  'nombre' => 'Docente',
  'descripcion' => 'Gestión de asistencia y calificaciones',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
),
        array (
  'id' => 3,
  'nombre' => 'Coordinador',
  'descripcion' => 'Gestión de horarios y grupos',
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
)
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}