<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedMateriasSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        DB::table('materias')->truncate();

        DB::table('materias')->insert([
            array (
  'id' => 1,
  'codigo' => 'BD-101',
  'nombre' => 'Base de Datos',
  'descripcion' => 'Diseño y gestión de bases de datos relacionales',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'estado' => 'activa',
),
        array (
  'id' => 2,
  'codigo' => 'INFO-101',
  'nombre' => 'Introduccion a la Informatica',
  'descripcion' => 'Fundamentos de informática y computación',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'estado' => 'activa',
),
        array (
  'id' => 3,
  'codigo' => 'CALC-101',
  'nombre' => 'Calculo1',
  'descripcion' => 'Cálculo diferencial e integral',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'estado' => 'activa',
),
        array (
  'id' => 4,
  'codigo' => 'MAT-200',
  'nombre' => 'Calculo 2',
  'descripcion' => 'Calculo 2',
  'created_at' => '2025-11-16 19:41:49',
  'updated_at' => '2025-11-16 19:41:49',
  'estado' => 'activa',
),
        array (
  'id' => 5,
  'codigo' => 'FIS-100',
  'nombre' => 'Fisica 1',
  'descripcion' => 'MRU MRUV, MCU',
  'created_at' => '2025-11-16 22:12:59',
  'updated_at' => '2025-11-17 17:14:36',
  'estado' => 'activa',
)
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}