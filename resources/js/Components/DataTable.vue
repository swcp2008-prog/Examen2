<template>
  <table class="min-w-full bg-white border border-gray-300">
    <thead class="bg-gray-100">
      <tr>
        <th 
          v-for="column in columns" 
          :key="column"
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b"
        >
          {{ column }}
        </th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
          Acciones
        </th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
      <tr v-for="row in data" :key="row.id" class="hover:bg-gray-50">
        <td v-for="column in columns" :key="column" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
          {{ row[column.toLowerCase()] || '-' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
          <Link :href="`${resourceRoute}/${row.id}/edit`" class="text-indigo-600 hover:text-indigo-900">
            Editar
          </Link>
          <button 
            @click="deleteItem(row.id)"
            class="ml-4 text-red-600 hover:text-red-900"
          >
            Eliminar
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  columns: Array,
  data: Array,
  resourceRoute: String,
});

const emit = defineEmits(['delete']);

const deleteItem = (id) => {
  if (confirm('¿Desea eliminar este registro?')) {
    emit('delete', id);
  }
};
</script>
