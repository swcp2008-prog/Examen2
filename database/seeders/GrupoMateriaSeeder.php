<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Horario;
use Illuminate\Database\Seeder;

class GrupoMateriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grupos = Grupo::all();
        $materias = Materia::all();
        $horarios = Horario::all();

        foreach ($grupos as $grupo) {
            // Asignar todas las materias a cada grupo
            foreach ($materias as $materia) {
                $horario = $horarios->random();
                $grupo->materias()->attach($materia->id, ['horario_id' => $horario->id]);
            }
        }
    }
}
