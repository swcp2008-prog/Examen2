<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <Head title="Registrar Asistencia" />
    
    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h1 class="text-2xl font-bold mb-6">Registrar Asistencia</h1>

            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">Grupo-Materia</label>
                <select 
                  v-model="form.grupo_materia_id"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                >
                  <option value="">Seleccione un grupo-materia</option>
                  <option v-for="gm in grupoMaterias" :key="gm.id" :value="gm.id">
                    {{ gm.grupo?.nombre }} - {{ gm.materia?.nombre }}
                  </option>
                </select>
                <p v-if="form.errors.grupo_materia_id" class="text-red-600 text-sm mt-1">{{ form.errors.grupo_materia_id }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Docente</label>
                <select 
                  v-model="form.docente_id"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                >
                  <option value="">Seleccione un docente</option>
                  <option v-for="docente in docentes" :key="docente.id" :value="docente.id">
                    {{ docente.user?.nombre }} {{ docente.user?.apellido }}
                  </option>
                </select>
                <p v-if="form.errors.docente_id" class="text-red-600 text-sm mt-1">{{ form.errors.docente_id }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Fecha</label>
                <input 
                  v-model="form.fecha" 
                  type="date"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                />
                <p v-if="form.errors.fecha" class="text-red-600 text-sm mt-1">{{ form.errors.fecha }}</p>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Hora Entrada</label>
                  <input 
                    v-model="form.hora_entrada" 
                    type="time"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Hora Salida</label>
                  <input 
                    v-model="form.hora_salida" 
                    type="time"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select 
                  v-model="form.estado"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                >
                  <option value="presente">Presente</option>
                  <option value="ausente">Ausente</option>
                  <option value="retardo">Retardo</option>
                  <option value="justificada">Justificada</option>
                </select>
                <p v-if="form.errors.estado" class="text-red-600 text-sm mt-1">{{ form.errors.estado }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                <textarea 
                  v-model="form.observaciones" 
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                  rows="3"
                ></textarea>
              </div>

              <div class="flex gap-4">
                <button 
                  type="submit"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                  :disabled="form.processing"
                >
                  Registrar Asistencia
                </button>
                <Link href="/asistencias" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md">
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
  grupoMaterias: Array,
  docentes: Array,
});

const form = useForm({
  grupo_materia_id: '',
  docente_id: '',
  fecha: new Date().toISOString().split('T')[0],
  hora_entrada: '',
  hora_salida: '',
  estado: 'presente',
  observaciones: '',
});

const submit = () => {
  form.post('/asistencias');
};
</script>
