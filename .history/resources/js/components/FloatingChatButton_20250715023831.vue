<script setup lang="ts">
import { ref, watch } from 'vue';
import { MessageSquare } from 'lucide-vue-next';
import ChatModal from './ChatModal.vue';

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
