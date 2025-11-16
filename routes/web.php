<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\GrupoMateriaController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\ReporteController;

Route::get('/', function () {
    // Redirigir a login si no está autenticado, o a dashboard si está autenticado
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Rutas de Usuarios
    Route::resource('usuarios', UsuarioController::class);
    Route::post('/usuarios/cambiar-contrasena', [UsuarioController::class, 'cambiarContrasena'])->name('usuarios.cambiar-contrasena');

    // Rutas de Roles
    Route::resource('roles', RolController::class);

    // Rutas de Bitácora
    Route::resource('bitacora', BitacoraController::class)->only(['index', 'show']);
    Route::post('/bitacora/exportar', [BitacoraController::class, 'exportar'])->name('bitacora.exportar');

    // Rutas de Aulas (CU11)
    Route::resource('aulas', AulaController::class);
    Route::get('/aulas-disponibles', function () {
        return Inertia::render('Aulas/Disponibles', [
            'aulas' => \App\Models\Aula::all(),
        ]);
    })->name('aulas.disponibles-page');
    Route::get('/aulas/disponibles', [AulaController::class, 'disponibles'])->name('aulas.disponibles');

    // Rutas de Horarios
    Route::resource('horarios', HorarioController::class);
    Route::get('/horarios/semanales', [HorarioController::class, 'semanales'])->name('horarios.semanales');
    Route::post('/horarios/verificar-disponibilidad', [HorarioController::class, 'verificarDisponibilidad'])->name('horarios.verificar-disponibilidad');

    // Rutas de Materias
    Route::resource('materias', MateriaController::class);

    // Rutas de Grupos
    Route::resource('grupos', GrupoController::class);

    // Rutas de Grupo-Materias
    Route::resource('grupo-materias', GrupoMateriaController::class);
        Route::get('/grupo-materias/{grupoMateria}/horarios-disponibles', [GrupoMateriaController::class, 'getHorariosDisponibles'])->name('grupo-materias.horarios-disponibles');

    // Rutas de Docentes (CU12, CU13, CU15)
    Route::get('/docentes', [DocenteController::class, 'index'])->name('docentes.index');
    Route::get('/docentes/{docente}/edit', [DocenteController::class, 'edit'])->name('docentes.edit');
    Route::put('/docentes/{docente}', [DocenteController::class, 'update'])->name('docentes.update');
    Route::get('/docentes/generar', function () {
        return Inertia::render('Docentes/GenerarHorarios');
    })->name('docentes.generar-page');
    Route::post('/docentes/{docente}/asignar-grupo-materia', [DocenteController::class, 'asignarGrupoMateria'])->name('docentes.asignar-grupo-materia');
    Route::delete('/docentes/{docente}/desasignar-grupo-materia/{grupoMateria}', [DocenteController::class, 'desasignarGrupoMateria'])->name('docentes.desasignar-grupo-materia');
    Route::get('/docentes/{docente}/horarios', [DocenteController::class, 'horarios'])->name('docentes.horarios');
    Route::post('/docentes/generar-horarios', [DocenteController::class, 'generarHorarios'])->name('docentes.generar-horarios');

    // Rutas de Asistencia (CU14, CU17)
    Route::resource('asistencias', AsistenciaController::class)->except('show');
    Route::get('/asistencias-consultar', function () {
        return Inertia::render('Asistencias/Consultar', [
            'docentes' => \App\Models\Docente::with('user')->get(),
            'grupos' => \App\Models\Grupo::all(),
        ]);
    })->name('asistencias.consultar-page');
    Route::post('/asistencias/registrar-grupo', [AsistenciaController::class, 'registrarGrupo'])->name('asistencias.registrar-grupo');
    Route::get('/asistencias/por-docente-grupo', [AsistenciaController::class, 'porDocenteGrupo'])->name('asistencias.por-docente-grupo');
    Route::post('/asistencias/exportar', [AsistenciaController::class, 'exportar'])->name('asistencias.exportar');

    // Rutas de Reportes (CU18, CU19)
    Route::get('/reportes', function () {
        return Inertia::render('Reportes/Index');
    })->name('reportes.index');
    Route::get('/reportes/asistencia-pdf', [ReporteController::class, 'asistenciaPdf'])->name('reportes.asistencia-pdf');
    Route::get('/reportes/asistencia-excel', [ReporteController::class, 'asistenciaExcel'])->name('reportes.asistencia-excel');
    Route::get('/reportes/bitacora-pdf', [ReporteController::class, 'bitacoraPdf'])->name('reportes.bitacora-pdf');
    Route::get('/reportes/bitacora-excel', [ReporteController::class, 'bitacoraExcel'])->name('reportes.bitacora-excel');
    Route::get('/reportes/horarios-semanales', [ReporteController::class, 'horariosSemanales'])->name('reportes.horarios-semanales');
});
