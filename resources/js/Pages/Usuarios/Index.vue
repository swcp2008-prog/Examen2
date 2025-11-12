<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <Head title="Usuarios" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-bold">Gestión de Usuarios</h1>
              <Link href="/usuarios/create" class="bg-indigo-600 text-white px-4 py-2 rounded-md">
                Crear Usuario
              </Link>
            </div>

            <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
              {{ $page.props.flash.success }}
            </div>

            <table class="min-w-full bg-white border border-gray-300">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="usuario in usuarios.data" :key="usuario.id" class="border-b">
                  <td class="px-6 py-4">{{ usuario.nombre }} {{ usuario.apellido }}</td>
                  <td class="px-6 py-4">{{ usuario.email }}</td>
                  <td class="px-6 py-4">{{ usuario.rol?.nombre || '-' }}</td>
                  <td class="px-6 py-4">
                    <span :class="usuario.estado === 'activo' ? 'text-green-600' : 'text-red-600'">
                      {{ usuario.estado }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <Link :href="`/usuarios/${usuario.id}/edit`" class="text-indigo-600 hover:text-indigo-900">
                      Editar
                    </Link>
                    <button 
                      @click="deleteUsuario(usuario.id)"
                      class="ml-4 text-red-600 hover:text-red-900"
                    >
                      Eliminar
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Paginación -->
            <div class="mt-6 flex justify-between">
              <Link 
                v-if="usuarios.prev_page_url"
                :href="usuarios.prev_page_url" 
                class="text-indigo-600"
              >
                ← Anterior
              </Link>
              <Link 
                v-if="usuarios.next_page_url"
                :href="usuarios.next_page_url" 
                class="text-indigo-600"
              >
                Siguiente →
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';

defineProps({
  usuarios: Object,
});

const deleteUsuario = (id) => {
  if (confirm('¿Desea eliminar este usuario?')) {
    router.delete(`/usuarios/${id}`);
  }
};
</script>
