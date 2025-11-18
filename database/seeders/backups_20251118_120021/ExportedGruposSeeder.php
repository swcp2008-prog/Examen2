<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedGruposSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        DB::table('grupos')->truncate();

        DB::table('grupos')->insert([
            array (
  'id' => 1,
  'codigo' => 'NW',
  'nombre' => 'NW',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'descripcion' => 'Grupo NW',
  'cantidad_estudiantes' => 30,
  'estado' => 'activo',
),
        array (
  'id' => 2,
  'codigo' => 'SF',
  'nombre' => 'SF',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'descripcion' => 'Grupo SF',
  'cantidad_estudiantes' => 28,
  'estado' => 'activo',
),
        array (
  'id' => 3,
  'codigo' => 'SA',
  'nombre' => 'SA',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'descripcion' => 'Grupo SA',
  'cantidad_estudiantes' => 32,
  'estado' => 'activo',
),
        array (
  'id' => 4,
  'codigo' => 'SC',
  'nombre' => 'SC',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'descripcion' => 'Grupo SC',
  'cantidad_estudiantes' => 29,
  'estado' => 'activo',
),
        array (
  'id' => 5,
  'codigo' => 'NA',
  'nombre' => 'NA',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'descripcion' => 'Grupo NA',
  'cantidad_estudiantes' => 31,
  'estado' => 'activo',
)
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}