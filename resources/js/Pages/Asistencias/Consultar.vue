<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Consultar Asistencia
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                      <div>
                        <label class="block text-sm font-medium mb-1">Docente:</label>
                        <div v-if="!isDocente">
                          <select v-model="filtros.docente_id" class="w-full px-3 py-2 border rounded">
                            <option value="">Selecciona docente</option>
                            <option v-for="docente in docentes" :key="docente.id" :value="docente.id">
                              {{ docente.user.nombre }}
                            </option>
                          </select>
                        </div>
                        <div v-else>
                          <p class="py-2">{{ docenteNombre }}</p>
                        </div>
                      </div>
              <div>
                <label class="block text-sm font-medium mb-1">Grupo:</label>
                <select v-model="filtros.grupo_id" class="w-full px-3 py-2 border rounded">
                  <option value="">Selecciona grupo</option>
                  <option v-for="grupo in grupos" :key="grupo.id" :value="grupo.id">
                    {{ grupo.nombre }}
                  </option>
                </select>
              </div>
            </div>

            <button 
              @click="consultar" 
              :disabled="cargando || (!isDocente && !filtros.docente_id) || !filtros.grupo_id"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-400"
            >
              {{ cargando ? "Buscando..." : "🔍 Buscar" }}
            </button>

            <div v-if="asistencias && asistencias.length > 0" class="mt-6 overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Presente</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="asistencia in asistencias" :key="asistencia.id">
                    <td class="px-6 py-4 text-sm">{{ asistencia.estudiante_nombre }}</td>
                    <td class="px-6 py-4 text-sm">{{ new Date(asistencia.fecha).toLocaleDateString() }}</td>
                    <td class="px-6 py-4 text-sm">
                      <span :class="asistencia.presente ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded">
                        {{ asistencia.presente ? '✅ Sí' : '❌ No' }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-else-if="consultado" class="mt-6 p-4 bg-yellow-50 text-yellow-700 rounded">
              ℹ️ No hay registros de asistencia
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
  docentes: Array,
  grupos: Array,
});

const page = usePage();
const currentUser = page.props.user || null;
const isDocente = !!(currentUser && currentUser.docente_id);
const docenteNombre = isDocente ? (page.props.user?.nombre + ' ' + page.props.user?.apellido) : '';

const cargando = ref(false);
const consultado = ref(false);
const asistencias = ref([]);
  const filtros = reactive({
  docente_id: isDocente ? currentUser.docente_id : '',
  grupo_id: '',
});

const consultar = async () => {
  cargando.value = true;
  consultado.value = true;
  try {
    const docenteParam = filtros.docente_id ? `docente_id=${filtros.docente_id}&` : '';
    const response = await fetch(`/asistencias/por-docente-grupo?${docenteParam}grupo_id=${filtros.grupo_id}`);
    const data = await response.json();
    asistencias.value = data;
  } catch (error) {
    console.error(error);
  } finally {
    cargando.value = false;
  }
};
</script>

