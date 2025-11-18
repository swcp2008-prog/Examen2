<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedDocentesSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        // Do not truncate: use insertOrIgnore to avoid removing existing data
        DB::table('docentes')->insertOrIgnore([
            array (
  'id' => 1,
  'user_id' => 3,
  'especialidad' => 'Física',
  'fecha_contrato' => '2023-11-13',
  'estado' => 'activo',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
),
        array (
  'id' => 2,
  'user_id' => 4,
  'especialidad' => 'Física',
  'fecha_contrato' => '2021-11-13',
  'estado' => 'activo',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
),
        array (
  'id' => 3,
  'user_id' => 5,
  'especialidad' => 'Física',
  'fecha_contrato' => '2021-11-13',
  'estado' => 'activo',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
),
        array (
  'id' => 4,
  'user_id' => 6,
  'especialidad' => 'Matemática',
  'fecha_contrato' => '2022-11-13',
  'estado' => 'activo',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
),
        array (
  'id' => 5,
  'user_id' => 8,
  'especialidad' => 'Base de Datos',
  'fecha_contrato' => '2024-02-01',
  'estado' => 'activo',
  'created_at' => '2025-11-16 18:51:32',
  'updated_at' => '2025-11-16 18:51:32',
)
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}