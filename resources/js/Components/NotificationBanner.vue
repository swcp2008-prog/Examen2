<script setup>
import { ref, watchEffect } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
  dismissible: {
    type: Boolean,
    default: true,
  },
});

const page = usePage();
const show = ref(false);
const displayMessage = ref('');
const messageType = ref('success'); // 'success' or 'danger'

// Watch page.props.jetstream.flash for notifications
watchEffect(() => {
  const flash = page.props.jetstream?.flash;
  console.log('[NotificationBanner] flash detected:', flash); // DEBUG
  
  if (flash?.banner) {
    console.log('[NotificationBanner] showing flash:', flash.banner); // DEBUG
    displayMessage.value = flash.banner;
    messageType.value = flash.bannerStyle === 'danger' ? 'danger' : 'success';
    show.value = true;

    // Auto-dismiss after 8 seconds (increased from 5s for better visibility)
    const timer = setTimeout(() => {
      show.value = false;
    }, 8000);
    return () => clearTimeout(timer);
  }
});

const dismiss = () => {
  show.value = false;
};

const isSuccess = () => messageType.value === 'success';
const isDanger = () => messageType.value === 'danger';
</script>

<template>
  <div v-if="show && displayMessage">
    <!-- Success Banner -->
    <div v-if="isSuccess()" class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 rounded-r">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-green-800">
            {{ displayMessage }}
          </p>
        </div>
        <div v-if="dismissible" class="ml-auto pl-3">
          <button @click="dismiss" type="button" class="inline-flex text-green-400 hover:text-green-500 focus:outline-none">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Danger Banner -->
    <div v-if="isDanger()" class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r">
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
  </div>
</template>
