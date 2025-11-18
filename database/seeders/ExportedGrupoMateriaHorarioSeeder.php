<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedGrupoMateriaHorarioSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        // Do not truncate: use insertOrIgnore to avoid removing existing data
        DB::table('grupo_materia_horario')->insertOrIgnore([
            array (
  'id' => 1,
  'grupo_materia_id' => 1,
  'horario_id' => 9,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 2,
  'grupo_materia_id' => 2,
  'horario_id' => 18,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 3,
  'grupo_materia_id' => 3,
  'horario_id' => 16,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 4,
  'grupo_materia_id' => 4,
  'horario_id' => 28,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 5,
  'grupo_materia_id' => 6,
  'horario_id' => 33,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 6,
  'grupo_materia_id' => 7,
  'horario_id' => 4,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 7,
  'grupo_materia_id' => 8,
  'horario_id' => 40,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 8,
  'grupo_materia_id' => 9,
  'horario_id' => 12,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 9,
  'grupo_materia_id' => 10,
  'horario_id' => 35,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 10,
  'grupo_materia_id' => 11,
  'horario_id' => 19,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 11,
  'grupo_materia_id' => 12,
  'horario_id' => 11,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 12,
  'grupo_materia_id' => 13,
  'horario_id' => 14,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 13,
  'grupo_materia_id' => 14,
  'horario_id' => 37,
  'created_at' => '2025-11-16 20:33:55',
  'updated_at' => '2025-11-16 20:33:55',
),
        array (
  'id' => 15,
  'grupo_materia_id' => 11,
  'horario_id' => 1,
  'created_at' => '2025-11-16 20:46:04',
  'updated_at' => '2025-11-16 20:46:04',
),
        array (
  'id' => 17,
  'grupo_materia_id' => 18,
  'horario_id' => 38,
  'created_at' => '2025-11-16 21:57:21',
  'updated_at' => '2025-11-16 21:57:21',
),
        array (
  'id' => 18,
  'grupo_materia_id' => 18,
  'horario_id' => 41,
  'created_at' => '2025-11-16 21:57:21',
  'updated_at' => '2025-11-16 21:57:21',
),
        array (
  'id' => 20,
  'grupo_materia_id' => 21,
  'horario_id' => 2,
  'created_at' => '2025-11-16 22:13:14',
  'updated_at' => '2025-11-16 22:13:14',
),
        array (
  'id' => 21,
  'grupo_materia_id' => 21,
  'horario_id' => 13,
  'created_at' => '2025-11-17 15:44:48',
  'updated_at' => '2025-11-17 15:44:48',
),
        array (
  'id' => 22,
  'grupo_materia_id' => 21,
  'horario_id' => 22,
  'created_at' => '2025-11-17 15:46:50',
  'updated_at' => '2025-11-17 15:46:50',
),
        array (
  'id' => 23,
  'grupo_materia_id' => 5,
  'horario_id' => 3,
  'created_at' => '2025-11-17 15:52:46',
  'updated_at' => '2025-11-17 15:52:46',
),
        array (
  'id' => 24,
  'grupo_materia_id' => 15,
  'horario_id' => 30,
  'created_at' => '2025-11-17 15:57:37',
  'updated_at' => '2025-11-17 15:57:37',
),
        array (
  'id' => 26,
  'grupo_materia_id' => 1,
  'horario_id' => 24,
  'created_at' => '2025-11-17 15:58:05',
  'updated_at' => '2025-11-17 15:58:05',
),
        array (
  'id' => 27,
  'grupo_materia_id' => 15,
  'horario_id' => 23,
  'created_at' => '2025-11-17 16:03:33',
  'updated_at' => '2025-11-17 16:03:33',
),
        array (
  'id' => 30,
  'grupo_materia_id' => 1,
  'horario_id' => 20,
  'created_at' => '2025-11-17 16:11:59',
  'updated_at' => '2025-11-17 16:11:59',
),
        array (
  'id' => 31,
  'grupo_materia_id' => 2,
  'horario_id' => 21,
  'created_at' => '2025-11-17 16:28:26',
  'updated_at' => '2025-11-17 16:28:26',
),
        array (
  'id' => 37,
  'grupo_materia_id' => 5,
  'horario_id' => 25,
  'created_at' => '2025-11-17 17:09:57',
  'updated_at' => '2025-11-17 17:09:57',
),
        array (
  'id' => 39,
  'grupo_materia_id' => 10,
  'horario_id' => 17,
  'created_at' => '2025-11-17 17:23:24',
  'updated_at' => '2025-11-17 17:23:24',
),
        array (
  'id' => 40,
  'grupo_materia_id' => 10,
  'horario_id' => 39,
  'created_at' => '2025-11-17 17:49:06',
  'updated_at' => '2025-11-17 17:49:06',
)
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}