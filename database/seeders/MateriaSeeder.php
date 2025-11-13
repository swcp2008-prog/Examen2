<?php

namespace Database\Seeders;

use App\Models\Materia;
use Illuminate\Database\Seeder;

class MateriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materias = [
            ['codigo' => 'BD-101', 'nombre' => 'Base de Datos', 'descripcion' => 'Diseño y gestión de bases de datos relacionales', 'horas_semanales' => 4, 'estado' => 'activa'],
            ['codigo' => 'INFO-101', 'nombre' => 'Introduccion a la Informatica', 'descripcion' => 'Fundamentos de informática y computación', 'horas_semanales' => 3, 'estado' => 'activa'],
            ['codigo' => 'CALC-101', 'nombre' => 'Calculo1', 'descripcion' => 'Cálculo diferencial e integral', 'horas_semanales' => 5, 'estado' => 'activa'],
        ];

        foreach ($materias as $materia) {
            Materia::firstOrCreate(
                ['codigo' => $materia['codigo']],
                $materia
            );
        }
    }
}
