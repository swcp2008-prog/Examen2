<template>
  <AuthenticatedLayout>
    <Head title="Roles" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-bold">Gestión de Roles</h1>
              <Link href="/roles/create" class="bg-indigo-600 text-white px-4 py-2 rounded-md">
                Crear Rol
              </Link>
            </div>

            <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
              {{ $page.props.flash?.success }}
            </div>

            <table class="min-w-full bg-white border border-gray-300">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permisos</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="!roles?.data || roles.data.length === 0" class="border-b">
                  <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                    No hay roles disponibles
                  </td>
                </tr>
                <tr v-for="rol in roles?.data" :key="rol.id" class="border-b">
                  <td class="px-6 py-4">{{ rol.nombre }}</td>
                  <td class="px-6 py-4">{{ rol.descripcion || '-' }}</td>
                  <td class="px-6 py-4">{{ rol.permisos?.length || 0 }}</td>
                  <td class="px-6 py-4">
                    <Link :href="`/roles/${rol.id}/edit`" class="text-indigo-600 hover:text-indigo-900">
                      Editar
                    </Link>
                    <button 
                      @click="deleteRol(rol.id)"
                      class="ml-4 text-red-600 hover:text-red-900"
                    >
                      Eliminar
                    </button>
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
  roles: Object,
});

const deleteRol = (id) => {
  if (confirm('¿Desea eliminar este rol?')) {
    router.delete(`/roles/${id}`);
  }
};
</script>

