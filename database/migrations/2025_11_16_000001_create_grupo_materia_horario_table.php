<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrupoMateriaHorarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crear tabla pivot solo si no existe (idempotente)
        if (!Schema::hasTable('grupo_materia_horario')) {
            Schema::create('grupo_materia_horario', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('grupo_materia_id');
                $table->unsignedBigInteger('horario_id');
                $table->timestamps();

                $table->foreign('grupo_materia_id')->references('id')->on('grupo_materias')->onDelete('cascade');
                $table->foreign('horario_id')->references('id')->on('horarios')->onDelete('cascade');

                // Evitar que un mismo horario sea asignado a más de una GrupoMateria
                $table->unique('horario_id');
            });
        }

        // Migrar datos existentes desde grupo_materias.horario_id al pivot
        if (Schema::hasColumn('grupo_materias', 'horario_id')) {
            $rows = \DB::table('grupo_materias')->whereNotNull('horario_id')->get(['id', 'horario_id']);
            foreach ($rows as $r) {
                // Evitar insertar duplicados (si ya existe un registro para ese horario)
                $exists = \DB::table('grupo_materia_horario')->where('horario_id', $r->horario_id)->exists();
                if ($exists) {
                    // si ya existe, saltar (no insertar) para evitar violar la constraint única
                    continue;
                }

                \DB::table('grupo_materia_horario')->insert([
                    'grupo_materia_id' => $r->id,
                    'horario_id' => $r->horario_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // NOTA: no eliminamos la columna antigua `horario_id` automáticamente
            // para mantener compatibilidad y permitir verificaciones manuales.
            // Si deseas eliminarla, hazlo con una migración separada después de
            // validar que todos los datos y la aplicación funcionan correctamente.
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // En down, recrear columna horario_id (nullable) y popular desde pivot
        Schema::table('grupo_materias', function (Blueprint $table) {
            if (!Schema::hasColumn('grupo_materias', 'horario_id')) {
                $table->unsignedBigInteger('horario_id')->nullable()->after('materia_id');
            }
        });

        // Copiar datos desde pivot a columna
        if (Schema::hasTable('grupo_materia_horario')) {
            $pivot = \DB::table('grupo_materia_horario')->get();
            foreach ($pivot as $p) {
                \DB::table('grupo_materias')->where('id', $p->grupo_materia_id)->update(['horario_id' => $p->horario_id]);
            }

            Schema::dropIfExists('grupo_materia_horario');
        }
    }
}
