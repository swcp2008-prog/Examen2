<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Rol::where('nombre', 'Administrador')->first();
        $docente = Rol::where('nombre', 'Docente')->first();
        $coordinador = Rol::where('nombre', 'Coordinador')->first();

        // Usuario Administrador
        User::firstOrCreate(
            ['email' => 'admin@sistema.com'],
            [
                'nombre' => 'Administrador',
                'apellido' => 'Sistema',
                'password' => Hash::make('password123'),
                'rol_id' => $admin->id,
                'estado' => 'activo',
            ]
        );

        // Usuario Coordinador
        User::firstOrCreate(
            ['email' => 'coordinador@sistema.com'],
            [
                'nombre' => 'Juan',
                'apellido' => 'Coordinador',
                'password' => Hash::make('password123'),
                'rol_id' => $coordinador->id,
                'estado' => 'activo',
            ]
        );

        // Usuarios Docentes
        $docentes = [
            ['nombre' => 'Carlos', 'apellido' => 'García', 'email' => 'carlos.garcia@sistema.com'],
            ['nombre' => 'María', 'apellido' => 'López', 'email' => 'maria.lopez@sistema.com'],
            ['nombre' => 'Pedro', 'apellido' => 'Rodríguez', 'email' => 'pedro.rodriguez@sistema.com'],
            ['nombre' => 'Ana', 'apellido' => 'Martínez', 'email' => 'ana.martinez@sistema.com'],
        ];

        foreach ($docentes as $docenteData) {
            User::firstOrCreate(
                ['email' => $docenteData['email']],
                [
                    'nombre' => $docenteData['nombre'],
                    'apellido' => $docenteData['apellido'],
                    'password' => Hash::make('password123'),
                    'rol_id' => $docente->id,
                    'estado' => 'activo',
                ]
            );
        }
    }
}
