<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->text('descripcion')->nullable();
            $table->integer('cantidad_estudiantes')->default(30);
            $table->string('estado')->default('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropColumn('descripcion');
            $table->dropColumn('cantidad_estudiantes');
            $table->dropColumn('estado');
        });
    }
};
