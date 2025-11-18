<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            PermisoSeeder::class,
            RolPermisoSeeder::class,
            UsuarioSeeder::class,
            AulaSeeder::class,
            HorarioSeeder::class,
            MateriaSeeder::class,
            GrupoSeeder::class,
            GrupoMateriaSeeder::class,
            DocenteSeeder::class,
            DocenteGrupoMateriaSeeder::class,
        ]);
    }
}
