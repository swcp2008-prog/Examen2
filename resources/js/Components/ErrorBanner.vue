<script setup>
import { ref, watchEffect, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
  message: String,
  dismissible: {
    type: Boolean,
    default: true,
  },
});

const page = usePage();
const show = ref(false);
const displayMessage = ref('');

// Watch both prop message and page.props.jetstream.flash for danger style
watchEffect(() => {
  // Priority: if message prop is set, use it
  if (props.message) {
    displayMessage.value = props.message;
    show.value = true;
  }
  // Secondary: check if Jetstream flash is danger
  else if (page.props.jetstream?.flash?.bannerStyle === 'danger') {
    displayMessage.value = page.props.jetstream.flash.banner;
    show.value = true;
  }

  // Auto-dismiss after 6 seconds
  if (show.value) {
    const timer = setTimeout(() => {
      show.value = false;
    }, 6000);
    return () => clearTimeout(timer);
  }
});

const dismiss = () => {
  show.value = false;
};
</script>

<template>
  <div v-if="show && displayMessage" class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
    <div class="flex">
      <div class="flex-shrink-0">
        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
      </div>
      <div class="ml-3">
        <p class="text-sm font-medium text-red-800">
          {{ displayMessage }}
        </p>
      </div>
      <div v-if="dismissible" class="ml-auto pl-3">
        <button @click="dismiss" type="button" class="inline-flex text-red-400 hover:text-red-500 focus:outline-none">
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>
