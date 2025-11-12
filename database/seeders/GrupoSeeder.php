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
            ['codigo' => 'TI-2024-A', 'nombre' => 'Tecnología Informatica 2024-A'],
            ['codigo' => 'TI-2024-B', 'nombre' => 'Tecnología Informatica 2024-B'],
            ['codigo' => 'TI-2024-C', 'nombre' => 'Tecnología Informatica 2024-C'],
            ['codigo' => 'IS-2024-A', 'nombre' => 'Ingeniería de Software 2024-A'],
            ['codigo' => 'IS-2024-B', 'nombre' => 'Ingeniería de Software 2024-B'],
            ['codigo' => 'BD-2024-A', 'nombre' => 'Bases de Datos 2024-A'],
        ];

        foreach ($grupos as $grupo) {
            Grupo::firstOrCreate(
                ['codigo' => $grupo['codigo']],
                $grupo
            );
        }
    }
}
