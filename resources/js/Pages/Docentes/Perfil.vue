<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        👤 Mi Perfil - Registro de Asistencia
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <!-- Información del Docente -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
              <div>
                <p class="text-sm font-medium text-gray-600">Nombre:</p>
                <p class="text-lg font-semibold">{{ docente.user.nombre }} {{ docente.user.apellido }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Email:</p>
                <p class="text-lg">{{ docente.user.email }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Especialidad:</p>
                <p class="text-lg">{{ docente.especialidad || 'N/A' }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-600">Grupos Asignados:</p>
                <p class="text-lg font-semibold text-blue-600">{{ docente.grupo_materias?.length || 0 }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Registro de Asistencia -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 text-gray-900">
            <h3 class="text-2xl font-bold mb-6 flex items-center gap-2">
              <span class="text-2xl">📝</span>
              Registrar Asistencia
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div v-for="grupoMateria in docente.grupo_materias" :key="grupoMateria.id" class="border-2 border-blue-300 rounded-lg p-6 hover:shadow-lg transition-shadow bg-gradient-to-br from-white to-blue-50">
                <!-- Encabezado: Grupo - Materia (prominente) -->
                <div class="mb-4 pb-4 border-b-2 border-blue-200">
                  <h4 class="text-xl font-bold text-blue-900 mb-1">
                    {{ grupoMateria.grupo?.nombre }} - {{ grupoMateria.materia?.nombre }}
                  </h4>
                  <p class="text-xs text-blue-600 font-semibold">Grupo-Materia Asignado</p>
                </div>

                <!-- Detalles del horario -->
                <div v-if="grupoMateria.horario" class="mb-4 p-3 bg-blue-50 rounded-md border border-blue-200">
                  <p class="text-sm font-semibold text-gray-800 mb-2">📋 Horario:</p>
                  <p class="text-sm text-gray-700">📅 {{ grupoMateria.horario.dia_semana }}</p>
                  <p class="text-sm text-gray-700">🕐 {{ grupoMateria.horario.hora_inicio }} - {{ grupoMateria.horario.hora_fin }}</p>
                  <p class="text-sm text-gray-700">🏢 Aula: {{ grupoMateria.horario.aula?.nombre_aula }}</p>
                </div>

                <!-- Estado de asistencia hoy -->
                <div class="mb-4 p-3 bg-gray-50 rounded border border-gray-300">
                  <p class="text-sm font-semibold text-gray-800 mb-2">📍 Estado Hoy:</p>
                  <div v-if="getAsistenciaHoy(grupoMateria.id)" class="space-y-2">
                    <p v-if="getAsistenciaHoy(grupoMateria.id).hora_entrada" class="text-sm text-green-700">
                      ✅ <strong>Entrada:</strong> {{ formatHora(getAsistenciaHoy(grupoMateria.id).hora_entrada) }}
                    </p>
                    <p v-if="getAsistenciaHoy(grupoMateria.id).hora_salida" class="text-sm text-orange-700">
                      🚪 <strong>Salida:</strong> {{ formatHora(getAsistenciaHoy(grupoMateria.id).hora_salida) }}
                    </p>
                  </div>
                  <p v-else class="text-sm text-gray-500 italic">Sin registros hoy</p>
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-3">
                  <button
                    @click="registrarEntrada(grupoMateria.id)"
                    :disabled="cargando[grupoMateria.id] || (getAsistenciaHoy(grupoMateria.id)?.hora_entrada && !getAsistenciaHoy(grupoMateria.id)?.hora_salida)"
                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2 font-semibold"
                  >
                    <span v-if="cargando[grupoMateria.id]">⏳</span>
                    <span v-else>✅</span>
                    {{ cargando[grupoMateria.id] ? 'Procesando...' : 'Entrada' }}
                  </button>
                  <button
                    @click="registrarSalida(grupoMateria.id)"
                    :disabled="cargando[grupoMateria.id] || !getAsistenciaHoy(grupoMateria.id)?.hora_entrada || getAsistenciaHoy(grupoMateria.id)?.hora_salida"
                    class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors flex items-center justify-center gap-2 font-semibold"
                  >
                    <span v-if="cargando[grupoMateria.id]">⏳</span>
                    <span v-else>🚪</span>
                    {{ cargando[grupoMateria.id] ? 'Procesando...' : 'Salida' }}
                  </button>
                </div>

                <!-- Mensajes -->
                <div v-if="mensajes[grupoMateria.id]" class="mt-3 p-2 rounded" :class="mensajes[grupoMateria.id].tipo === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                  <p class="text-sm">{{ mensajes[grupoMateria.id].texto }}</p>
                </div>
              </div>

              <!-- Mensaje si no hay grupos -->
              <div v-if="!docente.grupo_materias || docente.grupo_materias.length === 0" class="col-span-full p-8 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-center text-yellow-800">
                  ℹ️ No tienes grupos-materias asignados. Contacta a un administrador.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Historial reciente -->
        <div v-if="asistencias.data && asistencias.data.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h3 class="text-2xl font-bold mb-6 flex items-center gap-2">
              <span class="text-2xl">📋</span>
              Historial de Asistencia
            </h3>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrada</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salida</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="asistencia in asistencias.data" :key="asistencia.id">
                    <td class="px-6 py-4 text-sm">{{ formatFecha(asistencia.fecha) }}</td>
                    <td class="px-6 py-4 text-sm font-medium">{{ asistencia.grupo_materia?.grupo?.nombre }}</td>
                    <td class="px-6 py-4 text-sm">{{ asistencia.grupo_materia?.materia?.nombre }}</td>
                    <td class="px-6 py-4 text-sm">{{ asistencia.hora_entrada ? formatHora(asistencia.hora_entrada) : '—' }}</td>
                    <td class="px-6 py-4 text-sm">{{ asistencia.hora_salida ? formatHora(asistencia.hora_salida) : '—' }}</td>
                    <td class="px-6 py-4 text-sm">
                      <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">
                        {{ asistencia.estado }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Información de paginación -->
            <div class="mt-6 flex justify-between items-center">
              <div class="text-sm text-gray-600">
                Mostrando {{ asistencias.from }} a {{ asistencias.to }} de {{ asistencias.total }} registros
              </div>
              <div class="flex gap-2">
                <Link 
                  v-if="asistencias.prev_page_url"
                  :href="asistencias.prev_page_url"
                  class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors"
                >
                  ← Anterior
                </Link>
                <span class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md">
                  Página {{ asistencias.current_page }} de {{ asistencias.last_page }}
                </span>
                <Link 
                  v-if="asistencias.next_page_url"
                  :href="asistencias.next_page_url"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                >
                  Siguiente →
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
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  docente: Object,
  asistencias: Object,
});

const cargando = ref({});
const mensajes = ref({});

/**
 * Obtener asistencia de hoy para un grupo-materia
 */
const getAsistenciaHoy = (grupoMateriaId) => {
  const hoy = new Date();
  const fechaHoy = hoy.toISOString().split('T')[0];
  
  return props.asistencias?.data?.find(a => {
    const fechaRegistro = new Date(a.fecha).toISOString().split('T')[0];
    return a.grupo_materia_id === grupoMateriaId && fechaRegistro === fechaHoy;
  });
};

/**
 * Formatear hora (HH:MM:SS a HH:MM)
 */
const formatHora = (hora) => {
  if (!hora) return '';
  return hora.slice(0, 5); // HH:MM
};

/**
 * Formatear fecha
 */
const formatFecha = (fecha) => {
  if (!fecha) return '';
  const date = new Date(fecha);
  return date.toLocaleDateString('es-ES');
};

/**
 * Registrar entrada
 */
const registrarEntrada = async (grupoMateriaId) => {
  cargando.value[grupoMateriaId] = true;
  mensajes.value[grupoMateriaId] = null;

  try {
    const response = await fetch('/docentes/registrar-entrada', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        grupo_materia_id: grupoMateriaId,
      }),
    });

    const data = await response.json();

    if (response.ok) {
      mensajes.value[grupoMateriaId] = {
        tipo: 'success',
        texto: `✅ Entrada registrada a las ${data.hora}`,
      };
      
      // Recargar página después de 2 segundos
      setTimeout(() => {
        window.location.reload();
      }, 2000);
    } else {
      mensajes.value[grupoMateriaId] = {
        tipo: 'error',
        texto: data.error || 'Error al registrar entrada',
      };
    }
  } catch (error) {
    console.error('Error:', error);
    mensajes.value[grupoMateriaId] = {
      tipo: 'error',
      texto: 'Error de conexión',
    };
  } finally {
    cargando.value[grupoMateriaId] = false;
  }
};

/**
 * Registrar salida
 */
const registrarSalida = async (grupoMateriaId) => {
  cargando.value[grupoMateriaId] = true;
  mensajes.value[grupoMateriaId] = null;

  try {
    const response = await fetch('/docentes/registrar-salida', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        grupo_materia_id: grupoMateriaId,
      }),
    });

    const data = await response.json();

    if (response.ok) {
      mensajes.value[grupoMateriaId] = {
        tipo: 'success',
        texto: `🚪 Salida registrada a las ${data.hora}`,
      };
      
      // Recargar página después de 2 segundos
      setTimeout(() => {
        window.location.reload();
      }, 2000);
    } else {
      mensajes.value[grupoMateriaId] = {
        tipo: 'error',
        texto: data.error || 'Error al registrar salida',
      };
    }
  } catch (error) {
    console.error('Error:', error);
    mensajes.value[grupoMateriaId] = {
      tipo: 'error',
      texto: 'Error de conexión',
    };
  } finally {
    cargando.value[grupoMateriaId] = false;
  }
};
</script>
