<?php

namespace Database\Seeders;

use App\Models\Grupo;
use Illuminate\Database\Seeder;

class GrupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grupos = [
            ['codigo' => 'NW', 'nombre' => 'NW', 'descripcion' => 'Grupo NW', 'cantidad_estudiantes' => 30, 'estado' => 'activo'],
            ['codigo' => 'SF', 'nombre' => 'SF', 'descripcion' => 'Grupo SF', 'cantidad_estudiantes' => 28, 'estado' => 'activo'],
            ['codigo' => 'SA', 'nombre' => 'SA', 'descripcion' => 'Grupo SA', 'cantidad_estudiantes' => 32, 'estado' => 'activo'],
            ['codigo' => 'SC', 'nombre' => 'SC', 'descripcion' => 'Grupo SC', 'cantidad_estudiantes' => 29, 'estado' => 'activo'],
            ['codigo' => 'NA', 'nombre' => 'NA', 'descripcion' => 'Grupo NA', 'cantidad_estudiantes' => 31, 'estado' => 'activo'],
        ];

        foreach ($grupos as $grupo) {
            Grupo::firstOrCreate(
                ['codigo' => $grupo['codigo']],
                $grupo
            );
        }
    }
}
