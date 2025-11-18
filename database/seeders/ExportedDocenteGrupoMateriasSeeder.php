<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedDocenteGrupoMateriasSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        // Do not truncate: use insertOrIgnore to avoid removing existing data
        DB::table('docente_grupo_materia')->insertOrIgnore([
            array (
  'id' => 2,
  'docente_id' => 1,
  'grupo_materia_id' => 8,
  'created_at' => NULL,
  'updated_at' => NULL,
),
        array (
  'id' => 3,
  'docente_id' => 1,
  'grupo_materia_id' => 14,
  'created_at' => NULL,
  'updated_at' => NULL,
),
        array (
  'id' => 4,
  'docente_id' => 2,
  'grupo_materia_id' => 4,
  'created_at' => NULL,
  'updated_at' => NULL,
),
        array (
  'id' => 5,
  'docente_id' => 2,
  'grupo_materia_id' => 13,
  'created_at' => NULL,
  'updated_at' => NULL,
),
        array (
  'id' => 6,
  'docente_id' => 3,
  'grupo_materia_id' => 3,
  'created_at' => NULL,
  'updated_at' => NULL,
),
        array (
  'id' => 7,
  'docente_id' => 3,
  'grupo_materia_id' => 4,
  'created_at' => NULL,
  'updated_at' => NULL,
),
        array (
  'id' => 8,
  'docente_id' => 3,
  'grupo_materia_id' => 11,
  'created_at' => NULL,
  'updated_at' => NULL,
),
        array (
  'id' => 9,
  'docente_id' => 4,
  'grupo_materia_id' => 1,
  'created_at' => NULL,
  'updated_at' => NULL,
),
        array (
  'id' => 10,
  'docente_id' => 4,
  'grupo_materia_id' => 9,
  'created_at' => NULL,
  'updated_at' => NULL,
),
        array (
  'id' => 11,
  'docente_id' => 4,
  'grupo_materia_id' => 14,
  'created_at' => NULL,
  'updated_at' => NULL,
),
        array (
  'id' => 12,
  'docente_id' => 1,
  'grupo_materia_id' => 3,
  'created_at' => NULL,
  'updated_at' => NULL,
),
        array (
  'id' => 13,
  'docente_id' => 5,
  'grupo_materia_id' => 10,
  'created_at' => NULL,
  'updated_at' => NULL,
)
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}