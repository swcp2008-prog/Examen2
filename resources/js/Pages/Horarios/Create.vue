<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <Head title="Crear Horario" />
    
    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h1 class="text-2xl font-bold mb-6">Crear Horario</h1>

            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">Aula</label>
                <select 
                  v-model="form.aula_id"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                >
                  <option value="">Seleccione un aula</option>
                  <option v-for="aula in aulas" :key="aula.id" :value="aula.id">
                    {{ aula.nombre_aula }}
                  </option>
                </select>
                <p v-if="form.errors.aula_id" class="text-red-600 text-sm mt-1">{{ form.errors.aula_id }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Día de la Semana</label>
                <select 
                  v-model="form.dia_semana"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                >
                  <option value="">Seleccione un día</option>
                  <option value="Lunes">Lunes</option>
                  <option value="Martes">Martes</option>
                  <option value="Miércoles">Miércoles</option>
                  <option value="Jueves">Jueves</option>
                  <option value="Viernes">Viernes</option>
                  <option value="Sábado">Sábado</option>
                </select>
                <p v-if="form.errors.dia_semana" class="text-red-600 text-sm mt-1">{{ form.errors.dia_semana }}</p>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Hora Inicio</label>
                  <input 
                    v-model="form.hora_inicio" 
                    type="time"
                    required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                  />
                  <p v-if="form.errors.hora_inicio" class="text-red-600 text-sm mt-1">{{ form.errors.hora_inicio }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Hora Fin</label>
                  <input 
                    v-model="form.hora_fin" 
                    type="time"
                    required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                  />
                  <p v-if="form.errors.hora_fin" class="text-red-600 text-sm mt-1">{{ form.errors.hora_fin }}</p>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select 
                  v-model="form.estado"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                >
                  <option value="activo">Activo</option>
                  <option value="inactivo">Inactivo</option>
                </select>
              </div>

              <div class="flex gap-4">
                <button 
                  type="submit"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                  :disabled="form.processing"
                >
                  Crear Horario
                </button>
                <Link href="/horarios" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md">
                  Cancelar
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
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
  aulas: Array,
});

const form = useForm({
  aula_id: '',
  dia_semana: '',
  hora_inicio: '',
  hora_fin: '',
  estado: 'activo',
});

const submit = () => {
  form.post('/horarios');
};
</script>
