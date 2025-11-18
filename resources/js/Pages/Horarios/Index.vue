<template>
  <AuthenticatedLayout>
    <Head title="Horarios" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-bold">Gestión de Horarios</h1>
              <template v-if="user?.can_create_horarios">
                <Link href="/horarios/create" class="bg-indigo-600 text-white px-4 py-2 rounded-md">
                  Crear Horario
                </Link>
              </template>
            </div>

            <table class="min-w-full bg-white border border-gray-300">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aula</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Día</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora Inicio</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora Fin</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="horario in horarios.data" :key="horario.id" class="border-b">
                  <td class="px-6 py-4">{{ horario.aula?.nombre_aula }}</td>
                  <td class="px-6 py-4">{{ horario.dia_semana }}</td>
                  <td class="px-6 py-4">{{ horario.hora_inicio }}</td>
                  <td class="px-6 py-4">{{ horario.hora_fin }}</td>
                  <td class="px-6 py-4">{{ horario.estado }}</td>
                  <td class="px-6 py-4">
                    <Link v-if="user?.can_edit_horarios" :href="`/horarios/${horario.id}/edit`" class="text-indigo-600">Editar</Link>
                    <button v-if="user?.can_delete_horarios" @click="deleteHorario(horario.id)" class="ml-4 text-red-600">Eliminar</button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Paginación -->
            <div class="mt-6 flex justify-between items-center">
              <div class="text-sm text-gray-600">
                Mostrando {{ horarios.from }} a {{ horarios.to }} de {{ horarios.total }} registros
              </div>
              <div class="flex gap-2">
                <Link 
                  v-if="horarios.prev_page_url"
                  :href="horarios.prev_page_url"
                  class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                >
                  Anterior
                </Link>
                <span class="px-4 py-2 text-gray-700">
                  Página {{ horarios.current_page }} de {{ horarios.last_page }}
                </span>
                <Link 
                  v-if="horarios.next_page_url"
                  :href="horarios.next_page_url"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                  Siguiente
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.user || null;

defineProps({
  horarios: Object,
});

const deleteHorario = (id) => {
  if (confirm('¿Desea eliminar este horario?')) {
    router.delete(`/horarios/${id}`);
  }
};
</script>

