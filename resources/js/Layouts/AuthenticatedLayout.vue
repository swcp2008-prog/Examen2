<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center gap-8">
            <h1 class="text-xl font-bold text-gray-900">Sistema de Gestión Académica</h1>
            <div class="hidden md:flex gap-4">
              <Link href="/dashboard" class="text-gray-700 hover:text-gray-900">Dashboard</Link>
              <Link href="/usuarios" class="text-gray-700 hover:text-gray-900">Usuarios</Link>
              <Link href="/roles" class="text-gray-700 hover:text-gray-900">Roles</Link>
              <Link href="/aulas" class="text-gray-700 hover:text-gray-900">Aulas</Link>
              <Link href="/horarios" class="text-gray-700 hover:text-gray-900">Horarios</Link>
              <Link href="/materias" class="text-gray-700 hover:text-gray-900">Materias</Link>
              <Link href="/grupos" class="text-gray-700 hover:text-gray-900">Grupos</Link>
              <Link href="/asistencias" class="text-gray-700 hover:text-gray-900">Asistencia</Link>
              <Link href="/bitacora" class="text-gray-700 hover:text-gray-900">Bitácora</Link>
            </div>
          </div>
          <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">{{ user.nombre }} {{ user.apellido }}</span>
            <Link href="/logout" method="post" class="text-gray-700 hover:text-gray-900">Salir</Link>
          </div>
        </div>
      </div>
    </nav>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
  user: Object,
});
</script>
