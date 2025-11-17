<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Generar Horarios Docentes
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="mb-6">
              <p class="text-gray-600 mb-4">
                Generar automáticamente los horarios para todos los docentes según las materias y grupos asignados.
              </p>
            </div>

            <button v-if="user?.can_generate_horarios"
              @click="generarHorarios" 
              :disabled="cargando"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-400"
            >
              {{ cargando ? "Generando..." : "🔄 Generar Horarios" }}
            </button>

            <div v-if="mensaje.tipo" class="mt-4 p-4 rounded" :class="mensaje.tipo === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
              {{ mensaje.texto }}
            </div>

            <div v-if="resultado" class="mt-6 p-4 bg-gray-50 rounded">
              <p class="font-semibold mb-2">Resultado:</p>
              <pre class="text-sm overflow-auto">{{ JSON.stringify(resultado, null, 2) }}</pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();
const user = page.props.user || null;

const cargando = ref(false);
const mensaje = reactive({ tipo: '', texto: '' });
const resultado = ref(null);

const generarHorarios = async () => {
  cargando.value = true;
  try {
    const response = await fetch('/docentes/generar-horarios', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    });
    const data = await response.json();
    resultado.value = data;
    mensaje.tipo = 'success';
    mensaje.texto = data.message || 'Horarios generados exitosamente';
  } catch (error) {
    mensaje.tipo = 'error';
    mensaje.texto = 'Error al generar horarios: ' + error.message;
  } finally {
    cargando.value = false;
  }
};
</script>

