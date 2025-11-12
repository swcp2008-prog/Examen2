<template>
  <AuthenticatedLayout :user="$page.props.auth.user">
    <Head title="Editar Aula" />
    
    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h1 class="text-2xl font-bold mb-6">Editar Aula</h1>

            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input 
                  v-model="form.nombre_aula" 
                  type="text"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                />
                <p v-if="form.errors.nombre_aula" class="text-red-600 text-sm mt-1">{{ form.errors.nombre_aula }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                <select 
                  v-model="form.tipo"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                >
                  <option value="Salón">Salón</option>
                  <option value="Laboratorio">Laboratorio</option>
                  <option value="Auditorio">Auditorio</option>
                </select>
                <p v-if="form.errors.tipo" class="text-red-600 text-sm mt-1">{{ form.errors.tipo }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Capacidad</label>
                <input 
                  v-model="form.capacidad" 
                  type="number"
                  required
                  min="1"
                  max="500"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                />
                <p v-if="form.errors.capacidad" class="text-red-600 text-sm mt-1">{{ form.errors.capacidad }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select 
                  v-model="form.estado"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                >
                  <option value="activa">Activa</option>
                  <option value="inactiva">Inactiva</option>
                  <option value="mantenimiento">Mantenimiento</option>
                </select>
              </div>

              <div class="flex gap-4">
                <button 
                  type="submit"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                  :disabled="form.processing"
                >
                  Actualizar Aula
                </button>
                <Link href="/aulas" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md">
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
  aula: Object,
});

const form = useForm({
  nombre_aula: '',
  tipo: '',
  capacidad: '',
  estado: 'activa',
});

// Llenar el formulario con datos del aula
form.defaults({
  nombre_aula: aula.nombre_aula,
  tipo: aula.tipo,
  capacidad: aula.capacidad,
  estado: aula.estado,
});

const submit = () => {
  form.put(`/aulas/${aula.id}`);
};
</script>
