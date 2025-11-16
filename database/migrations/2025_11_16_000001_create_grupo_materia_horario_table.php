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

        // Migrar datos existentes desde grupo_materias.horario_id al pivot
        if (Schema::hasColumn('grupo_materias', 'horario_id')) {
            $rows = \DB::table('grupo_materias')->whereNotNull('horario_id')->get(['id', 'horario_id']);
            foreach ($rows as $r) {
                \DB::table('grupo_materia_horario')->insert([
                    'grupo_materia_id' => $r->id,
                    'horario_id' => $r->horario_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Eliminar la columna antigua
            Schema::table('grupo_materias', function (Blueprint $table) {
                if (Schema::hasColumn('grupo_materias', 'horario_id')) {
                    $table->dropForeign(['horario_id']);
                    $table->dropColumn('horario_id');
                }
            });
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
