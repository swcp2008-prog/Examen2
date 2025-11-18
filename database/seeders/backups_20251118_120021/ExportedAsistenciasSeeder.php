<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedAsistenciasSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        DB::table('asistencias')->truncate();

        DB::table('asistencias')->insert([
            array (
  'id' => 1,
  'grupo_materia_id' => 3,
  'docente_id' => 1,
  'fecha' => '2025-11-12',
  'hora_entrada' => '09:00:00',
  'hora_salida' => '11:00:00',
  'estado' => 'presente',
  'observaciones' => NULL,
  'created_at' => '2025-11-17 19:34:39',
  'updated_at' => '2025-11-17 19:34:39',
),
        array (
  'id' => 2,
  'grupo_materia_id' => 14,
  'docente_id' => 1,
  'fecha' => '2025-11-12',
  'hora_entrada' => '09:40:00',
  'hora_salida' => '11:15:00',
  'estado' => 'presente',
  'observaciones' => NULL,
  'created_at' => '2025-11-18 04:44:46',
  'updated_at' => '2025-11-18 04:44:46',
),
        array (
  'id' => 3,
  'grupo_materia_id' => 8,
  'docente_id' => 1,
  'fecha' => '2025-11-12',
  'hora_entrada' => '15:40:00',
  'hora_salida' => '17:15:00',
  'estado' => 'presente',
  'observaciones' => NULL,
  'created_at' => '2025-11-18 04:45:23',
  'updated_at' => '2025-11-18 04:45:23',
),
        array (
  'id' => 4,
  'grupo_materia_id' => 3,
  'docente_id' => 1,
  'fecha' => '2025-11-13',
  'hora_entrada' => '08:00:00',
  'hora_salida' => '09:30:00',
  'estado' => 'presente',
  'observaciones' => NULL,
  'created_at' => '2025-11-18 04:46:41',
  'updated_at' => '2025-11-18 04:46:41',
)
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}