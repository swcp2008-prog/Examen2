<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Crear Asignación Grupo-Materia
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <form @submit.prevent="guardar">
              <!-- Grupo -->
              <div class="mb-6">
                <label for="grupo_id" class="block text-sm font-medium text-gray-700 mb-2">
                  Grupo *
                </label>
                <select
                  id="grupo_id"
                  v-model="formulario.grupo_id"
                  class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-500': errores.grupo_id }"
                >
                  <option value="">-- Selecciona un Grupo --</option>
                  <option v-for="grupo in grupos" :key="grupo.id" :value="grupo.id">
                    {{ grupo.nombre }}
                  </option>
                </select>
                <p v-if="errores.grupo_id" class="mt-1 text-sm text-red-600">
                  {{ errores.grupo_id[0] }}
                </p>
              </div>

              <!-- Materia -->
              <div class="mb-6">
                <label for="materia_id" class="block text-sm font-medium text-gray-700 mb-2">
                  Materia *
                </label>
                <select
                  id="materia_id"
                  v-model="formulario.materia_id"
                  class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-500': errores.materia_id }"
                >
                  <option value="">-- Selecciona una Materia --</option>
                  <option v-for="materia in materias" :key="materia.id" :value="materia.id">
                    {{ materia.nombre }}
                  </option>
                </select>
                <p v-if="errores.materia_id" class="mt-1 text-sm text-red-600">
                  {{ errores.materia_id[0] }}
                </p>
              </div>

              <!-- Horario -->
              <div class="mb-6">
                <label for="horario_id" class="block text-sm font-medium text-gray-700 mb-2">
                  Horario-Aula *
                </label>
                <select
                  id="horario_id"
                  v-model="formulario.horario_id"
                  class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-500': errores.horario_id }"
                >
                  <option value="">-- Selecciona un Horario --</option>
                  <option v-for="horario in horarios" :key="horario.id" :value="horario.id">
                    {{ horario.dia_semana }} ({{ horario.hora_inicio }} - {{ horario.hora_fin }}) - {{ horario.aula?.nombre_aula || 'N/A' }}
                  </option>
                </select>
                <p v-if="errores.horario_id" class="mt-1 text-sm text-red-600">
                  {{ errores.horario_id[0] }}
                </p>
              </div>

              <!-- Error general -->
              <div v-if="errores.general" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ errores.general }}
              </div>

              <!-- Botones -->
              <div class="flex gap-3">
                <button
                  type="submit"
                  :disabled="cargando"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:bg-gray-400"
                >
                  {{ cargando ? "Guardando..." : "✅ Guardar" }}
                </button>
                <Link
                  href="/grupo-materias"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  ❌ Cancelar
                </Link>
              </div>
            </form>
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
  grupos: Array,
  materias: Array,
  horarios: Array,
});

const formulario = reactive({
  grupo_id: '',
  materia_id: '',
  horario_id: '',
});

const errores = reactive({});
const cargando = ref(false);

const guardar = async () => {
  cargando.value = true;
  Object.keys(errores).forEach(key => delete errores[key]);

  try {
    await router.post('/grupo-materias', formulario, {
      onError: (e) => {
        Object.assign(errores, e);
        cargando.value = false;
      },
      onSuccess: () => {
        cargando.value = false;
        // La redirección se maneja automáticamente
      },
    });
  } catch (error) {
    cargando.value = false;
  }
};
</script>
