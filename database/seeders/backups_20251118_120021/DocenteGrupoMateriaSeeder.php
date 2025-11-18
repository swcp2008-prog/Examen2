<?php

namespace Database\Seeders;

use App\Models\Docente;
use App\Models\GrupoMateria;
use Illuminate\Database\Seeder;

class DocenteGrupoMateriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $docentes = Docente::all();
        $grupoMaterias = GrupoMateria::all();

        foreach ($docentes as $docente) {
            // Asignar 2-3 grupos-materias por docente
            $asignaciones = $grupoMaterias->random(rand(2, 3));
            foreach ($asignaciones as $grupoMateria) {
                $docente->grupoMaterias()->attach($grupoMateria->id);
            }
        }
    }
}
