<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestionar Grupos-Materias
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Tarjeta principal -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900">
            <!-- Botón Crear -->
            <div class="mb-6">
              <Link href="/grupo-materias/create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ➕ Nueva Asignación
              </Link>
            </div>

            <!-- Tabla de Grupos-Materias -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horario Asignado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="grupoMateria in gruposMaterias.data" :key="grupoMateria.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ grupoMateria.grupo?.nombre || '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                      {{ grupoMateria.materia?.nombre || '—' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                      <span v-if="grupoMateria.horario" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ grupoMateria.horario.dia_semana }} ({{ grupoMateria.horario.hora_inicio }} - {{ grupoMateria.horario.hora_fin }}) - {{ grupoMateria.horario.aula?.nombre_aula || 'N/A' }}
                      </span>
                      <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Sin horario
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                      <button @click="abrirModalAsignarHorario(grupoMateria)" class="text-blue-600 hover:text-blue-900">🕐 Asignar Horario</button>
                      <Link :href="`/grupo-materias/${grupoMateria.id}/edit`" class="text-indigo-600 hover:text-indigo-900">✏️ Editar</Link>
                      <button @click="confirmarEliminar(grupoMateria)" class="text-red-600 hover:text-red-900">🗑️ Eliminar</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Paginación -->
            <div class="mt-6 flex justify-center">
              <nav class="flex space-x-2">
                <Link v-if="gruposMaterias.prev_page_url" :href="gruposMaterias.prev_page_url" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                  ← Anterior
                </Link>
                <span class="px-3 py-2 text-gray-600">
                  Página {{ gruposMaterias.current_page }} de {{ gruposMaterias.last_page }}
                </span>
                <Link v-if="gruposMaterias.next_page_url" :href="gruposMaterias.next_page_url" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                  Siguiente →
                </Link>
              </nav>
            </div>
          </div>
        </div>

        <!-- Modal para asignar horario rápidamente -->
        <div v-if="mostrarModal" class="fixed z-50 inset-0 overflow-y-auto">
          <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Fondo oscuro -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <!-- Modal -->
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                      🕐 Asignar Horario a {{ grupoMateriaSeleccionado?.grupo?.nombre }} - {{ grupoMateriaSeleccionado?.materia?.nombre }}
                    </h3>
                    <div class="mt-4">
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Selecciona un horario:
                      </label>
                      <select v-model="formulario.horario_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Selecciona --</option>
                        <option v-for="horario in horarios" :key="horario.id" :value="horario.id"
                          :disabled="horario.disponible === false && horario.id !== grupoMateriaSeleccionado?.horario_id">
                          {{ horario.dia_semana }} {{ horario.hora_inicio }} - {{ horario.hora_fin }} (Aula: {{ horario.aula?.nombre_aula || 'N/A' }})
                          <span v-if="horario.disponible === false"> - (Ocupado)</span>
                          <span v-else> - (Disponible)</span>
                        </option>
                      </select>
                      <div v-if="errores.horario_id" class="text-red-600 text-sm mt-1">
                        {{ errores.horario_id[0] }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Botones -->
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button 
                  @click="asignarHorario" 
                  :disabled="cargando"
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:bg-gray-400"
                >
                  {{ cargando ? "Asignando..." : "✅ Asignar" }}
                </button>
                <button 
                  @click="cerrarModal" 
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                >
                  ❌ Cancelar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
  gruposMaterias: Object,
  horarios: Array,
});

const mostrarModal = ref(false);
const grupoMateriaSeleccionado = ref(null);
const cargando = ref(false);
const errores = reactive({});

const formulario = reactive({
  horario_id: '',
});

const abrirModalAsignarHorario = (grupoMateria) => {
  grupoMateriaSeleccionado.value = grupoMateria;
  formulario.horario_id = grupoMateria.horario_id || '';
  Object.keys(errores).forEach(key => delete errores[key]);
  mostrarModal.value = true;
};

const cerrarModal = () => {
  mostrarModal.value = false;
  grupoMateriaSeleccionado.value = null;
};

const asignarHorario = async () => {
  cargando.value = true;
  Object.keys(errores).forEach(key => delete errores[key]);

  try {
    await router.put(`/grupo-materias/${grupoMateriaSeleccionado.value.id}`, {
      horario_id: formulario.horario_id,
    }, {
      onError: (e) => {
        Object.assign(errores, e);
        cargando.value = false;
      },
      onSuccess: () => {
        cerrarModal();
        cargando.value = false;
      },
    });
  } catch (error) {
    cargando.value = false;
  }
};

const confirmarEliminar = (grupoMateria) => {
  if (confirm(`¿Estás seguro de que deseas eliminar la asignación ${grupoMateria.grupo?.nombre} - ${grupoMateria.materia?.nombre}?`)) {
    router.delete(`/grupo-materias/${grupoMateria.id}`);
  }
};
</script>
