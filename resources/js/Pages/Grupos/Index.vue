<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <Head title="Grupos" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-bold">Gestión de Grupos</h1>
              <Link href="/grupos/create" class="bg-indigo-600 text-white px-4 py-2 rounded-md">
                Crear Grupo
              </Link>
            </div>

            <table class="min-w-full bg-white border border-gray-300">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="grupo in grupos.data" :key="grupo.id" class="border-b">
                  <td class="px-6 py-4">{{ grupo.codigo }}</td>
                  <td class="px-6 py-4">{{ grupo.nombre }}</td>
                  <td class="px-6 py-4">
                    <Link :href="`/grupos/${grupo.id}/edit`" class="text-indigo-600">Editar</Link>
                    <button @click="deleteGrupo(grupo.id)" class="ml-4 text-red-600">Eliminar</button>
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
  grupos: Object,
});

const deleteGrupo = (id) => {
  if (confirm('¿Desea eliminar este grupo?')) {
    router.delete(`/grupos/${id}`);
  }
};
</script>
