<script setup lang="ts">
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const show = ref(false);
const message = ref('');
const type = ref(''); // 'success', 'error', etc.

const page = usePage();

watch(() => page.props.flash, (flash) => {
  if (flash && (flash.success || flash.error)) {
    message.value = (flash.success || flash.error) as string;
    type.value = flash.success ? 'success' : 'error';
    show.value = true;

    // Automatically hide after 3 seconds
    setTimeout(() => {
      show.value = false;
      message.value = '';
      type.value = '';
    }, 3000);
  }
}, { deep: true });

const alertClass = (type: string) => {
  switch (type) {
    case 'success':
      return 'bg-green-100 border-green-400 text-green-700';
    case 'error':
      return 'bg-red-100 border-red-400 text-red-700';
    default:
      return 'bg-blue-100 border-blue-400 text-blue-700';
  }
};
</script>

<template>
  <div v-if="show" :class="alertClass(type)" class="fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-md border" role="alert">
