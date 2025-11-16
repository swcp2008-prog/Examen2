<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🏫 Sistema de Gestión de Docentes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Bienvenida -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 overflow-hidden shadow-xl sm:rounded-lg mb-6 text-white p-8">
                    <h1 class="text-3xl font-bold mb-2">Bienvenido al Sistema</h1>
                    <p class="text-lg opacity-90">Gestión integral de docentes, horarios y asistencias</p>
                </div>

                <!-- Menú Principal -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Gestión de Docentes -->
                    <Link href="/docentes" class="block group">
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-shadow p-6 cursor-pointer h-full">
                            <div class="text-4xl mb-4">👨‍🏫</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600">Docentes</h3>
                            <p class="text-gray-600 text-sm mb-4">Gestionar docentes, perfiles y asignaciones</p>
                            <div class="text-blue-600 text-sm font-semibold">CU01-CU10 →</div>
                        </div>
                    </Link>

                    <!-- Gestión de Aulas -->
                    <Link href="/aulas" class="block group">
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-shadow p-6 cursor-pointer h-full">
                            <div class="text-4xl mb-4">🏢</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-green-600">Aulas</h3>
                            <p class="text-gray-600 text-sm mb-4">Gestionar aulas, capacidades y disponibilidad</p>
                            <div class="text-green-600 text-sm font-semibold">CU11 →</div>
                        </div>
                    </Link>

                    <!-- Horarios -->
                    <Link href="/docentes" class="block group">
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-shadow p-6 cursor-pointer h-full">
                            <div class="text-4xl mb-4">📅</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-purple-600">Horarios</h3>
                            <p class="text-gray-600 text-sm mb-4">Crear y gestionar horarios de docentes</p>
                            <div class="text-purple-600 text-sm font-semibold">CU12-CU15 →</div>
                        </div>
                    </Link>

                    <!-- Grupo-Materias -->
                    <Link href="/grupo-materias" class="block group">
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-shadow p-6 cursor-pointer h-full">
                            <div class="text-4xl mb-4">📚</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-teal-600">Grupo-Materias</h3>
                            <p class="text-gray-600 text-sm mb-4">Asignar materias a grupos con horarios</p>
                            <div class="text-teal-600 text-sm font-semibold">CU12 →</div>
                        </div>
                    </Link>

                    <!-- Asistencias -->
                    <Link href="/asistencias" class="block group">
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-shadow p-6 cursor-pointer h-full">
                            <div class="text-4xl mb-4">✅</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-600">Asistencias</h3>
                            <p class="text-gray-600 text-sm mb-4">Registrar y consultar asistencia de docentes</p>
                            <div class="text-orange-600 text-sm font-semibold">CU14, CU17 →</div>
                        </div>
                    </Link>

                    <!-- Aulas Disponibles -->
                    <Link href="/aulas-disponibles" class="block group">
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-shadow p-6 cursor-pointer h-full">
                            <div class="text-4xl mb-4">🔍</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600">Aulas Disponibles</h3>
                            <p class="text-gray-600 text-sm mb-4">Consultar disponibilidad de aulas</p>
                            <div class="text-indigo-600 text-sm font-semibold">CU16 →</div>
                        </div>
                    </Link>

                    <!-- Reportes -->
                    <Link href="/reportes" class="block group">
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-shadow p-6 cursor-pointer h-full">
                            <div class="text-4xl mb-4">📊</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-red-600">Reportes</h3>
                            <p class="text-gray-600 text-sm mb-4">Generar reportes de asistencias y horarios</p>
                            <div class="text-red-600 text-sm font-semibold">CU18, CU19 →</div>
                        </div>
                    </Link>
                </div>

                <!-- Información del Sistema -->
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 p-6 sm:rounded-lg border border-blue-200">
                        <div class="text-3xl font-bold text-blue-600 mb-2">19</div>
                        <p class="text-gray-700">Casos de Uso Implementados</p>
                    </div>
                    <div class="bg-green-50 p-6 sm:rounded-lg border border-green-200">
                        <div class="text-3xl font-bold text-green-600 mb-2">13</div>
                        <p class="text-gray-700">Tablas en Base de Datos</p>
                    </div>
                    <div class="bg-purple-50 p-6 sm:rounded-lg border border-purple-200">
                        <div class="text-3xl font-bold text-purple-600 mb-2">100%</div>
                        <p class="text-gray-700">Sistema Funcional</p>
                    </div>
                </div>

                <!-- Acceso rápido -->
                <div class="mt-12 bg-white overflow-hidden shadow-lg sm:rounded-lg p-8">
                    <h3 class="text-lg font-bold mb-6">📚 Documentación Rápida</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Primeros pasos:</h4>
                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                <li>Ver docentes registrados</li>
                                <li>Consultar horarios</li>
                                <li>Registrar asistencias</li>
                                <li>Generar reportes</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Funcionalidades principales:</h4>
                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                <li>Gestión de docentes</li>
                                <li>Generación automática de horarios</li>
                                <li>Control de asistencia</li>
                                <li>Reportes PDF y CSV</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

