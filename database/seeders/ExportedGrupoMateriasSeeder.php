<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedGrupoMateriasSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        // Do not truncate: use insertOrIgnore to avoid removing existing data
        DB::table('grupo_materias')->insertOrIgnore([
            array (
  'id' => 5,
  'grupo_id' => 2,
  'materia_id' => 2,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => NULL,
),
        array (
  'id' => 15,
  'grupo_id' => 5,
  'materia_id' => 3,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => NULL,
),
        array (
  'id' => 1,
  'grupo_id' => 1,
  'materia_id' => 1,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 9,
),
        array (
  'id' => 2,
  'grupo_id' => 1,
  'materia_id' => 2,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 18,
),
        array (
  'id' => 3,
  'grupo_id' => 1,
  'materia_id' => 3,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 16,
),
        array (
  'id' => 4,
  'grupo_id' => 2,
  'materia_id' => 1,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 28,
),
        array (
  'id' => 6,
  'grupo_id' => 2,
  'materia_id' => 3,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 33,
),
        array (
  'id' => 7,
  'grupo_id' => 3,
  'materia_id' => 1,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 4,
),
        array (
  'id' => 8,
  'grupo_id' => 3,
  'materia_id' => 2,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 40,
),
        array (
  'id' => 9,
  'grupo_id' => 3,
  'materia_id' => 3,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 12,
),
        array (
  'id' => 10,
  'grupo_id' => 4,
  'materia_id' => 1,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 35,
),
        array (
  'id' => 11,
  'grupo_id' => 4,
  'materia_id' => 2,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 19,
),
        array (
  'id' => 12,
  'grupo_id' => 4,
  'materia_id' => 3,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 11,
),
        array (
  'id' => 13,
  'grupo_id' => 5,
  'materia_id' => 1,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 14,
),
        array (
  'id' => 14,
  'grupo_id' => 5,
  'materia_id' => 2,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'horario_id' => 37,
),
        array (
  'id' => 18,
  'grupo_id' => 3,
  'materia_id' => 4,
  'created_at' => '2025-11-16 20:11:40',
  'updated_at' => '2025-11-16 20:13:35',
  'horario_id' => 38,
),
        array (
  'id' => 21,
  'grupo_id' => 1,
  'materia_id' => 5,
  'created_at' => '2025-11-16 22:13:14',
  'updated_at' => '2025-11-16 22:13:14',
  'horario_id' => NULL,
)
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}