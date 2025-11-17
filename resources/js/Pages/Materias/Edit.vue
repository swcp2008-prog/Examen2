<template>
  <AuthenticatedLayout>
    <Head title="Editar Materia" />
    
    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h1 class="text-2xl font-bold mb-6">Editar Materia</h1>

            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input 
                  v-model="form.nombre" 
                  type="text"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                />
                <p v-if="form.errors.nombre" class="text-red-600 text-sm mt-1">{{ form.errors.nombre }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Código</label>
                <input 
                  v-model="form.codigo" 
                  type="text"
                  required
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                />
                <p v-if="form.errors.codigo" class="text-red-600 text-sm mt-1">{{ form.errors.codigo }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea 
                  v-model="form.descripcion"
                  class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                  rows="4"
                ></textarea>
                <p v-if="form.errors.descripcion" class="text-red-600 text-sm mt-1">{{ form.errors.descripcion }}</p>
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
                </select>
              </div>

              <div class="flex gap-4">
                <button 
                  type="submit"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                  :disabled="form.processing"
                >
                  Actualizar Materia
                </button>
                <Link href="/materias" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md">
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

const props = defineProps({
  materia: Object,
});

const form = useForm({
  nombre: '',
  codigo: '',
  descripcion: '',
  estado: 'activa',
});

form.defaults({
  nombre: props.materia.nombre,
  codigo: props.materia.codigo,
  descripcion: props.materia.descripcion,
  estado: props.materia.estado,
});

form.reset();

const submit = () => {
  form.put(`/materias/${props.materia.id}`);
};
</script>
