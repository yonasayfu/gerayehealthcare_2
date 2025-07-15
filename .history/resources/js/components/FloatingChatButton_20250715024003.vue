<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { MessageSquare } from 'lucide-vue-next';
import ChatModal from './ChatModal.vue';
import { eventBus } from '@/lib/eventBus'; // Import eventBus

const props = defineProps<{
  initialConversationId?: number | null;
}>();

const isChatOpen = ref(false);
const activeConversationId = ref<number | null>(null);

const toggleChat = (convoId: number | null = null) => {
  if (convoId) {
    activeConversationId.value = convoId;
    isChatOpen.value = true;
  } else {
    isChatOpen.value = !isChatOpen.value;
    if (!isChatOpen.value) {
      activeConversationId.value = null; // Reset when closing
    }
  }
};

watch(() => props.initialConversationId, (newVal) => {
  if (newVal) {
    toggleChat(newVal);
  }
}, { immediate: true });

onMounted(() => {
  eventBus.on('open-chat', (convoId) => {
    toggleChat(convoId);
  });
});

onUnmounted(() => {
  eventBus.off('open-chat');
});
</script>

<template>
  <div class="fixed bottom-4 right-4 z-50">
    <button
      @click="toggleChat()"
      class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-4 shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
    >
      <MessageSquare class="w-6 h-6" />
    </button>

    <ChatModal :is-open="isChatOpen" @close="isChatOpen = false" :initial-conversation-id="activeConversationId" />
  </div>
</template>

<style scoped>
/* Add any specific styles for the floating button here if needed */
</style>
