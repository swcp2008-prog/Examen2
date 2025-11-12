<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <Head title="Bitácora" />
    
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
              <h1 class="text-2xl font-bold">Bitácora del Sistema</h1>
              <button @click="exportarBitacora" class="bg-indigo-600 text-white px-4 py-2 rounded-md">
                Exportar CSV
              </button>
            </div>

            <div class="mb-6 grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Tabla</label>
                <input 
                  v-model="filtros.tabla"
                  type="text"
                  placeholder="Filtrar por tabla"
                  class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Acción</label>
                <input 
                  v-model="filtros.accion"
                  type="text"
                  placeholder="Filtrar por acción"
                  class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3"
                />
              </div>
            </div>

            <table class="min-w-full bg-white border border-gray-300">
              <thead class="bg-gray-100">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tabla</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="bitacora in bitacoras.data" :key="bitacora.id" class="border-b">
                  <td class="px-6 py-4 text-sm">{{ formatFecha(bitacora.fecha_hora) }}</td>
                  <td class="px-6 py-4 text-sm">{{ bitacora.user?.nombre }} {{ bitacora.user?.apellido }}</td>
                  <td class="px-6 py-4 text-sm">{{ bitacora.accion }}</td>
                  <td class="px-6 py-4 text-sm">{{ bitacora.tabla_afectada }}</td>
                  <td class="px-6 py-4 text-sm">{{ bitacora.ip_origen }}</td>
                </tr>
              </tbody>
            </table>

            <!-- Paginación -->
            <div class="mt-6 flex justify-between">
              <Link 
                v-if="bitacoras.prev_page_url"
                :href="bitacoras.prev_page_url" 
                class="text-indigo-600"
              >
                ← Anterior
              </Link>
              <Link 
                v-if="bitacoras.next_page_url"
                :href="bitacoras.next_page_url" 
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
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
  bitacoras: Object,
});

const filtros = ref({
  tabla: '',
  accion: '',
});

const formatFecha = (fecha) => {
  return new Date(fecha).toLocaleString('es-ES');
};

const exportarBitacora = () => {
  window.location.href = `/bitacora/exportar?tabla=${filtros.value.tabla}&accion=${filtros.value.accion}`;
};
</script>
