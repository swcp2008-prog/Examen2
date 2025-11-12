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
            // Asignar 3-4 materias por grupo
            $materiasAsignadas = $materias->random(rand(3, 4));
            foreach ($materiasAsignadas as $materia) {
                $horario = $horarios->random();
                $grupo->materias()->attach($materia->id, ['horario_id' => $horario->id]);
            }
        }
    }
}
