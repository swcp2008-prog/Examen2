<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <Head title="Asistencia" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-bold">Gestión de Asistencia</h1>
              <Link href="/asistencias/create" class="bg-indigo-600 text-white px-4 py-2 rounded-md">
                Registrar Asistencia
              </Link>
            </div>

            <div class="mb-6 space-x-4">
              <a href="/reportes/asistencia-excel" class="bg-green-600 text-white px-4 py-2 rounded-md">
                Exportar Excel
              </a>
              <a href="/reportes/asistencia-pdf" class="bg-red-600 text-white px-4 py-2 rounded-md">
                Exportar PDF
              </a>
            </div>

            <table class="min-w-full bg-white border border-gray-300">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docente</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="asistencia in asistencias.data" :key="asistencia.id" class="border-b">
                  <td class="px-6 py-4">{{ asistencia.fecha }}</td>
                  <td class="px-6 py-4">{{ asistencia.docente?.user?.nombre }} {{ asistencia.docente?.user?.apellido }}</td>
                  <td class="px-6 py-4">{{ asistencia.grupo_materia?.grupo?.nombre }}</td>
                  <td class="px-6 py-4">
                    <span :class="getEstadoClass(asistencia.estado)">
                      {{ asistencia.estado }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <button @click="deleteAsistencia(asistencia.id)" class="text-red-600">Eliminar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
  asistencias: Object,
});

const getEstadoClass = (estado) => {
  const clases = {
    'presente': 'text-green-600',
    'ausente': 'text-red-600',
    'retardo': 'text-yellow-600',
    'justificada': 'text-blue-600',
  };
  return clases[estado] || '';
};

const deleteAsistencia = (id) => {
  if (confirm('¿Desea eliminar este registro?')) {
    router.delete(`/asistencias/${id}`);
  }
};
</script>
