<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Docente;
use Illuminate\Database\Seeder;

class DocenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $docentes = User::whereHas('rol', function ($query) {
            $query->where('nombre', 'Docente');
        })->get();

        $especialidades = ['Matemática', 'Física', 'Química', 'Programación', 'Bases de Datos'];

        foreach ($docentes as $usuario) {
            Docente::firstOrCreate(
                ['user_id' => $usuario->id],
                [
                    'especialidad' => $especialidades[array_rand($especialidades)],
                    'fecha_contrato' => now()->subYears(rand(1, 5)),
                    'estado' => 'activo',
                ]
            );
        }
    }
}
