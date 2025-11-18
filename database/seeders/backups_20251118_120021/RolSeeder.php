<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Administrador', 'descripcion' => 'Acceso total al sistema'],
            ['nombre' => 'Docente', 'descripcion' => 'Gestión de asistencia y calificaciones'],
            ['nombre' => 'Coordinador', 'descripcion' => 'Gestión de horarios y grupos'],
        ];

        foreach ($roles as $rol) {
            Rol::firstOrCreate(
                ['nombre' => $rol['nombre']],
                $rol
            );
        }
    }
}
