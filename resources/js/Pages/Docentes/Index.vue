<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestionar Docentes
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Tarjeta principal -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900">
            <!-- Botón Crear -->
            <div class="mb-6">
              <Link href="/usuarios/create?rol=docente" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ➕ Nuevo Docente
              </Link>
            </div>

            <!-- Tabla de Docentes -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupos Asignados</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="docente in docentes.data" :key="docente.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ docente.user.nombre }} {{ docente.user.apellido }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                      {{ docente.user.email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                      {{ docente.especialidad || "Sin especialidad" }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ docente.grupo_materias ? docente.grupo_materias.length : 0 }} grupos
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                      <Link :href="`/docentes/${docente.id}/edit`" class="text-indigo-600 hover:text-indigo-900">✏️ Editar</Link>
                      <button @click="abrirModalAsignar(docente)" class="text-green-600 hover:text-green-900">📚 Asignar Grupo</button>
                      <Link :href="`/docentes/${docente.id}/horarios`" class="text-purple-600 hover:text-purple-900">📅 Horarios</Link>
                      <button @click="confirmarEliminar(docente)" class="text-red-600 hover:text-red-900">🗑️ Eliminar</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Paginación -->
            <div class="mt-6 flex justify-center">
              <nav class="flex space-x-2">
                <Link v-if="docentes.prev_page_url" :href="docentes.prev_page_url" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                  ← Anterior
                </Link>
                <span class="px-3 py-2 text-gray-600">
                  Página {{ docentes.current_page }} de {{ docentes.last_page }}
                </span>
                <Link v-if="docentes.next_page_url" :href="docentes.next_page_url" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                  Siguiente →
                </Link>
              </nav>
            </div>
          </div>
        </div>

        <!-- Modal para asignar grupo-materia (CU12) -->
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
                      📚 Asignar Grupo-Materia a {{ docenteSeleccionado?.user?.nombre }}
                    </h3>
                    <div class="mt-4">
                      <!-- Tabla de Grupo-Materia con disponibilidad -->
                      <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                          <thead class="bg-gray-50">
                            <tr>
                              <th class="px-3 py-2 text-left font-medium text-gray-600">Grupo</th>
                              <th class="px-3 py-2 text-left font-medium text-gray-600">Materia</th>
                              <th class="px-3 py-2 text-left font-medium text-gray-600">Día</th>
                              <th class="px-3 py-2 text-left font-medium text-gray-600">Hora</th>
                              <th class="px-3 py-2 text-left font-medium text-gray-600">Aula</th>
                              <th class="px-3 py-2 text-left font-medium text-gray-600">Estado</th>
                              <th class="px-3 py-2 text-center font-medium text-gray-600">Acción</th>
                            </tr>
                          </thead>
                          <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="gm in gruposMaterias" :key="gm.id" class="hover:bg-gray-50">
                              <td class="px-3 py-2">{{ gm.grupo?.nombre || '—' }}</td>
                              <td class="px-3 py-2">{{ gm.materia?.nombre || '—' }}</td>
                              <td class="px-3 py-2">{{ gm.horario?.dia_semana || '—' }}</td>
                              <td class="px-3 py-2">{{ gm.horario ? gm.horario.hora_inicio + ' - ' + gm.horario.hora_fin : '—' }}</td>
                              <td class="px-3 py-2">{{ gm.horario?.aula?.nombre || '—' }}</td>
                              <td class="px-3 py-2">
                                <span v-if="gm.docentes && gm.docentes.length" class="text-red-600">Asignado a {{ gm.docentes[0].user?.nombre }} {{ gm.docentes[0].user?.apellido }}</span>
                                <span v-else class="text-green-600">Disponible</span>
                              </td>
                              <td class="px-3 py-2 text-center">
                                <button
                                  :disabled="gm.docentes && gm.docentes.length"
                                  @click.prevent="asignarDesdeFila(gm)"
                                  class="px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700 disabled:opacity-50"
                                >Asignar</button>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div v-if="errores.grupo_materia_id" class="text-red-600 text-sm mt-1">
                        {{ errores.grupo_materia_id[0] }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Botones -->
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button 
                  @click="asignarGrupoMateria" 
                  :disabled="cargando"
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:bg-gray-400"
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
import { ref, reactive, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
  docentes: Object,
  gruposMaterias: Array,
});

const mostrarModal = ref(false);
const docenteSeleccionado = ref(null);
const cargando = ref(false);
const errores = reactive({});

const formulario = reactive({
  grupo_materia_id: '',
});

const abrirModalAsignar = (docente) => {
  docenteSeleccionado.value = docente;
  formulario.grupo_materia_id = '';
  Object.keys(errores).forEach(key => delete errores[key]);
  mostrarModal.value = true;
};

const cerrarModal = () => {
  mostrarModal.value = false;
  docenteSeleccionado.value = null;
};

const asignarGrupoMateria = async () => {
  cargando.value = true;
  Object.keys(errores).forEach(key => delete errores[key]);

  try {
    await router.post(`/docentes/${docenteSeleccionado.value.id}/asignar-grupo-materia`, {
      grupo_materia_id: formulario.grupo_materia_id,
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

const asignarDesdeFila = async (gm) => {
  if (!docenteSeleccionado.value) return;
  cargando.value = true;
  Object.keys(errores).forEach(key => delete errores[key]);

  try {
    await router.post(`/docentes/${docenteSeleccionado.value.id}/asignar-grupo-materia`, {
      grupo_materia_id: gm.id,
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

const confirmarEliminar = (docente) => {
  if (confirm(`¿Estás seguro de que deseas eliminar a ${docente.user.nombre}?`)) {
    router.delete(`/docentes/${docente.id}`);
  }
};

onMounted(() => {
  // El componente se monta correctamente
});
</script>

