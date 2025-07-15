<script setup lang="ts">
import { ref } from 'vue';
import { MessageSquare } from 'lucide-vue-next';
import ChatModal from './ChatModal.vue'; // Will create this in the next step

const isChatOpen = ref(false);

const toggleChat = () => {
  isChatOpen.value = !isChatOpen.value;
};
</script>

<template>
  <div class="fixed bottom-4 right-4 z-50">
    <button
      @click="toggleChat"
      class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-4 shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
    >
      <MessageSquare class="w-6 h-6" />
    </button>

    <ChatModal :is-open="isChatOpen" @close="isChatOpen = false" />
  </div>
</template>

<style scoped>
/* Add any specific styles for the floating button here if needed */
</style>
