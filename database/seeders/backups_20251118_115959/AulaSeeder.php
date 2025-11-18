<?php

namespace Database\Seeders;

use App\Models\Aula;
use Illuminate\Database\Seeder;

class AulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aulas = [
            ['nombre_aula' => 'A-101', 'tipo' => 'Salón', 'capacidad' => 40, 'estado' => 'activa'],
            ['nombre_aula' => 'A-102', 'tipo' => 'Salón', 'capacidad' => 35, 'estado' => 'activa'],
            ['nombre_aula' => 'A-103', 'tipo' => 'Laboratorio', 'capacidad' => 25, 'estado' => 'activa'],
            ['nombre_aula' => 'A-104', 'tipo' => 'Salón', 'capacidad' => 40, 'estado' => 'activa'],
            ['nombre_aula' => 'A-105', 'tipo' => 'Laboratorio', 'capacidad' => 20, 'estado' => 'activa'],
            ['nombre_aula' => 'B-201', 'tipo' => 'Salón', 'capacidad' => 50, 'estado' => 'activa'],
            ['nombre_aula' => 'B-202', 'tipo' => 'Salón', 'capacidad' => 45, 'estado' => 'activa'],
            ['nombre_aula' => 'B-203', 'tipo' => 'Auditorio', 'capacidad' => 100, 'estado' => 'activa'],
        ];

        foreach ($aulas as $aula) {
            Aula::firstOrCreate(
                ['nombre_aula' => $aula['nombre_aula']],
                $aula
            );
        }
    }
}
