<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExportedUsersSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();
        DB::statement('SET session_replication_role = replica;'); // for postgres to disable FK checks

        DB::table('users')->truncate();

        DB::table('users')->insert([
            array (
  'id' => 1,
  'nombre' => 'Administrador',
  'apellido' => 'Sistema',
  'email' => 'admin@sistema.com',
  'email_verified_at' => NULL,
  'password' => '$2y$12$RN5kUNG21j0CyCSoP0PxheMsFvcihII/qNO/jywCYMkADZUOQksDa',
  'remember_token' => NULL,
  'rol_id' => 1,
  'estado' => 'activo',
  'profile_photo_path' => NULL,
  'created_at' => '2025-11-13 09:49:28',
  'updated_at' => '2025-11-13 09:49:28',
  'two_factor_secret' => NULL,
  'two_factor_recovery_codes' => NULL,
  'two_factor_confirmed_at' => NULL,
),
        array (
  'id' => 2,
  'nombre' => 'Juan',
  'apellido' => 'Coordinador',
  'email' => 'coordinador@sistema.com',
  'email_verified_at' => NULL,
  'password' => '$2y$12$HGgF58WOT8L98XmWQtHlDeDM7b3zN50.tX2DpAgDRk/eAYoq48Pa2',
  'remember_token' => NULL,
  'rol_id' => 3,
  'estado' => 'activo',
  'profile_photo_path' => NULL,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'two_factor_secret' => NULL,
  'two_factor_recovery_codes' => NULL,
  'two_factor_confirmed_at' => NULL,
),
        array (
  'id' => 3,
  'nombre' => 'Carlos',
  'apellido' => 'García',
  'email' => 'carlos.garcia@sistema.com',
  'email_verified_at' => NULL,
  'password' => '$2y$12$C/AfocZ9NYrt1pXtxvap8.ZdndFhu.sCfUUEmKAGkQn.heZbhr3Ry',
  'remember_token' => NULL,
  'rol_id' => 2,
  'estado' => 'activo',
  'profile_photo_path' => NULL,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'two_factor_secret' => NULL,
  'two_factor_recovery_codes' => NULL,
  'two_factor_confirmed_at' => NULL,
),
        array (
  'id' => 4,
  'nombre' => 'María',
  'apellido' => 'López',
  'email' => 'maria.lopez@sistema.com',
  'email_verified_at' => NULL,
  'password' => '$2y$12$HdnYop4BBAIb8XurrKjp4.rqXlR4QnodYtN2EenXdP.BX4t2GrIvy',
  'remember_token' => NULL,
  'rol_id' => 2,
  'estado' => 'activo',
  'profile_photo_path' => NULL,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'two_factor_secret' => NULL,
  'two_factor_recovery_codes' => NULL,
  'two_factor_confirmed_at' => NULL,
),
        array (
  'id' => 5,
  'nombre' => 'Pedro',
  'apellido' => 'Rodríguez',
  'email' => 'pedro.rodriguez@sistema.com',
  'email_verified_at' => NULL,
  'password' => '$2y$12$omBDW2K7t4MqRymlz7Xlf.ODzKZWw/Jy8CqO7qN5Q2s74H5sZB8qi',
  'remember_token' => NULL,
  'rol_id' => 2,
  'estado' => 'activo',
  'profile_photo_path' => NULL,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'two_factor_secret' => NULL,
  'two_factor_recovery_codes' => NULL,
  'two_factor_confirmed_at' => NULL,
),
        array (
  'id' => 6,
  'nombre' => 'Ana',
  'apellido' => 'Martínez',
  'email' => 'ana.martinez@sistema.com',
  'email_verified_at' => NULL,
  'password' => '$2y$12$tjci5At2pCsBOcKCd5.nPus8bfkEOR83ibGnFzBLB3/Dmj1M0WAmi',
  'remember_token' => NULL,
  'rol_id' => 2,
  'estado' => 'activo',
  'profile_photo_path' => NULL,
  'created_at' => '2025-11-13 09:49:29',
  'updated_at' => '2025-11-13 09:49:29',
  'two_factor_secret' => NULL,
  'two_factor_recovery_codes' => NULL,
  'two_factor_confirmed_at' => NULL,
),
        array (
  'id' => 8,
  'nombre' => 'Efrain',
  'apellido' => 'Padilla',
  'email' => 'efrainpadilla@gmail.com',
  'email_verified_at' => NULL,
  'password' => '$2y$12$jf6PUuKvBqMrWscnWJgJP.r6GADHEVns0r2AZeCNhURl0kE4ziYeG',
  'remember_token' => NULL,
  'rol_id' => 2,
  'estado' => 'activo',
  'profile_photo_path' => NULL,
  'created_at' => '2025-11-16 18:51:32',
  'updated_at' => '2025-11-16 18:51:32',
  'two_factor_secret' => NULL,
  'two_factor_recovery_codes' => NULL,
  'two_factor_confirmed_at' => NULL,
)
        ]);

        DB::statement('SET session_replication_role = default;');
        DB::commit();
    }
}