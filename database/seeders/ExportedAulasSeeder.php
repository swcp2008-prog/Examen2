<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedAulasSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        // Do not truncate: use insertOrIgnore to avoid removing existing data
        DB::table('aulas')->insertOrIgnore([
            array (
  'id' => 1,
  'nombre_aula' => 'A-101',
  'tipo' => 'Salón',
  'capacidad' => 40,
  'estado' => 'activa',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
),
        array (
  'id' => 2,
  'nombre_aula' => 'A-102',
  'tipo' => 'Salón',
  'capacidad' => 35,
  'estado' => 'activa',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
),
        array (
  'id' => 3,
  'nombre_aula' => 'A-103',
  'tipo' => 'Laboratorio',
  'capacidad' => 25,
  'estado' => 'activa',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
),
        array (
  'id' => 4,
  'nombre_aula' => 'A-104',
  'tipo' => 'Salón',
  'capacidad' => 40,
  'estado' => 'activa',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
),
        array (
  'id' => 5,
  'nombre_aula' => 'A-105',
  'tipo' => 'Laboratorio',
  'capacidad' => 20,
  'estado' => 'activa',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
),
        array (
  'id' => 6,
  'nombre_aula' => 'B-201',
  'tipo' => 'Salón',
  'capacidad' => 50,
  'estado' => 'activa',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
),
        array (
  'id' => 7,
  'nombre_aula' => 'B-202',
  'tipo' => 'Salón',
  'capacidad' => 45,
  'estado' => 'activa',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
),
        array (
  'id' => 8,
  'nombre_aula' => 'B-203',
  'tipo' => 'Auditorio',
  'capacidad' => 100,
  'estado' => 'activa',
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
)
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}