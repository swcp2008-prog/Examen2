<template>
  <form @submit.prevent="submit" class="space-y-6">
    <div>
      <label class="block text-sm font-medium text-gray-700">
        {{ label }}
      </label>
      <input
        v-model="modelValue"
        :type="type"
        :placeholder="placeholder"
        :required="required"
        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
      />
      <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
    </div>
  </form>
</template>

<script setup>
defineProps({
  modelValue: String,
  label: String,
  type: { type: String, default: 'text' },
  placeholder: String,
  required: Boolean,
  error: String,
});

const emit = defineEmits(['update:modelValue']);

const updateValue = (value) => {
  emit('update:modelValue', value);
};
</script>
