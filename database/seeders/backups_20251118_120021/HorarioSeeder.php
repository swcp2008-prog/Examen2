<?php

namespace Database\Seeders;

use App\Models\Horario;
use App\Models\Aula;
use Illuminate\Database\Seeder;

class HorarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aulas = Aula::all();
        $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $horas = [
            ['inicio' => '08:00', 'fin' => '09:30'],
            ['inicio' => '09:45', 'fin' => '11:15'],
            ['inicio' => '11:30', 'fin' => '13:00'],
            ['inicio' => '14:00', 'fin' => '15:30'],
            ['inicio' => '15:45', 'fin' => '17:15'],
        ];

        $index = 0;
        foreach ($aulas as $aula) {
            foreach ($diasSemana as $dia) {
                foreach ($horas as $hora) {
                    Horario::firstOrCreate(
                        [
                            'aula_id' => $aula->id,
                            'dia_semana' => $dia,
                            'hora_inicio' => $hora['inicio'],
                            'hora_fin' => $hora['fin'],
                        ],
                        [
                            'estado' => 'activo',
                        ]
                    );
                    $index++;
                    if ($index >= 40) break;
                }
                if ($index >= 40) break;
            }
            if ($index >= 40) break;
        }
    }
}
