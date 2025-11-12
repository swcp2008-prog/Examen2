<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <Head title="Aulas" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-bold">Gestión de Aulas</h1>
              <Link href="/aulas/create" class="bg-indigo-600 text-white px-4 py-2 rounded-md">
                Crear Aula
              </Link>
            </div>

            <table class="min-w-full bg-white border border-gray-300">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacidad</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="aula in aulas.data" :key="aula.id" class="border-b">
                  <td class="px-6 py-4">{{ aula.nombre_aula }}</td>
                  <td class="px-6 py-4">{{ aula.tipo }}</td>
                  <td class="px-6 py-4">{{ aula.capacidad }}</td>
                  <td class="px-6 py-4">
                    <span :class="aula.estado === 'activa' ? 'text-green-600' : 'text-red-600'">
                      {{ aula.estado }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <Link :href="`/aulas/${aula.id}/edit`" class="text-indigo-600">Editar</Link>
                    <button @click="deleteAula(aula.id)" class="ml-4 text-red-600">Eliminar</button>
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
  aulas: Object,
});

const deleteAula = (id) => {
  if (confirm('¿Desea eliminar esta aula?')) {
    router.delete(`/aulas/${id}`);
  }
};
</script>
