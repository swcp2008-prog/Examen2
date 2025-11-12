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
            ['codigo' => 'MAT-101', 'nombre' => 'Matemática I', 'descripcion' => 'Cálculo diferencial'],
            ['codigo' => 'MAT-102', 'nombre' => 'Matemática II', 'descripcion' => 'Cálculo integral'],
            ['codigo' => 'FIS-101', 'nombre' => 'Física I', 'descripcion' => 'Mecánica clásica'],
            ['codigo' => 'FIS-102', 'nombre' => 'Física II', 'descripcion' => 'Electromagnetismo'],
            ['codigo' => 'QUI-101', 'nombre' => 'Química General', 'descripcion' => 'Química general y inorgánica'],
            ['codigo' => 'PRO-101', 'nombre' => 'Programación I', 'descripcion' => 'Fundamentos de programación'],
            ['codigo' => 'PRO-102', 'nombre' => 'Programación II', 'descripcion' => 'Programación orientada a objetos'],
            ['codigo' => 'BD-101', 'nombre' => 'Bases de Datos', 'descripcion' => 'Diseño y gestión de bases de datos'],
            ['codigo' => 'WEB-101', 'nombre' => 'Desarrollo Web', 'descripcion' => 'HTML, CSS, JavaScript'],
            ['codigo' => 'BD-102', 'nombre' => 'SQL Avanzado', 'descripcion' => 'SQL avanzado y optimización'],
        ];

        foreach ($materias as $materia) {
            Materia::firstOrCreate(
                ['codigo' => $materia['codigo']],
                $materia
            );
        }
    }
}
