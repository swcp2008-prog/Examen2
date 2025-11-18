<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Aulas Disponibles
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="mb-6 p-4 bg-blue-50 rounded">
              <p class="text-sm text-gray-600">
                Filtrar aulas disponibles por día, hora y capacidad
              </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
              <div>
                <label class="block text-sm font-medium mb-1">Día:</label>
                <select v-model="filtros.dia_semana" class="w-full px-3 py-2 border rounded">
                  <option value="">Seleccione un día</option>
                  <option value="Lunes">Lunes</option>
                  <option value="Martes">Martes</option>
                  <option value="Miércoles">Miércoles</option>
                  <option value="Jueves">Jueves</option>
                  <option value="Viernes">Viernes</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Hora Inicio (Opcional):</label>
                <input v-model="filtros.hora_inicio" type="time" class="w-full px-3 py-2 border rounded" placeholder="Ej: 10:00">
              </div>
              <div>
                <label class="block text-sm font-medium mb-1">Capacidad Mínima (Opcional):</label>
                <input v-model.number="filtros.capacidad_minima" type="number" class="w-full px-3 py-2 border rounded" placeholder="Ej: 30">
              </div>
            </div>

            <button 
              @click="consultar" 
              :disabled="cargando || !filtros.dia_semana"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-400"
            >
              {{ cargando ? "Buscando..." : "🔍 Buscar Disponibles" }}
            </button>

            <div v-if="aulasDisponibles && aulasDisponibles.length > 0" class="mt-6 overflow-x-auto">
              <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded">
                <p class="text-sm text-green-800">
                  ✅ Se encontraron <strong>{{ aulasDisponibles.length }}</strong> aula(s) disponible(s)
                  <span v-if="filtros.hora_inicio">
                    el {{ filtros.dia_semana }} a las {{ filtros.hora_inicio }}
                  </span>
                </p>
              </div>
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aula</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intervalos Disponibles</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="aula in aulasDisponibles" :key="aula.id">
                    <td class="px-6 py-4 text-sm font-medium">{{ aula.nombre_aula }}</td>
                    <td class="px-6 py-4 text-sm">{{ aula.tipo }}</td>
                    <td class="px-6 py-4 text-sm">{{ aula.capacidad }} estudiantes</td>
                    <td class="px-6 py-4 text-sm">
                      <div v-if="aula.intervalos_disponibles && aula.intervalos_disponibles.length">
                        <ul class="list-disc ml-5 text-sm">
                          <li v-for="(it, idx) in aula.intervalos_disponibles" :key="idx">{{ it.start }} - {{ it.end }}</li>
                        </ul>
                      </div>
                      <div v-else class="text-sm text-gray-500">No especificado</div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                      <span class="px-2 py-1 bg-green-100 text-green-700 rounded">Disponible</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-else-if="consultado && aulasDisponibles.length === 0" class="mt-6 p-4 bg-yellow-50 text-yellow-700 rounded">
              ℹ️ No hay aulas disponibles con esos criterios. Intenta con otro día, hora o capacidad.
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const cargando = ref(false);
const consultado = ref(false);
const aulasDisponibles = ref([]);
const filtros = reactive({
  dia_semana: '',
  hora_inicio: '',
  capacidad_minima: 0,
});

const consultar = async () => {
  if (!filtros.dia_semana) {
    alert('Por favor, selecciona un día');
    return;
  }
  
  cargando.value = true;
  consultado.value = true;
  try {
    const params = new URLSearchParams();
    params.append('dia_semana', filtros.dia_semana);
    if (filtros.hora_inicio) params.append('hora_inicio', filtros.hora_inicio);
    if (filtros.capacidad_minima) params.append('capacidad_minima', filtros.capacidad_minima);
    
    const response = await fetch(`/aulas/disponibles?${params}`);
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }
    const data = await response.json();
    aulasDisponibles.value = data;
  } catch (error) {
    console.error('Error fetching aulas:', error);
    aulasDisponibles.value = [];
  } finally {
    cargando.value = false;
  }
};
</script>

