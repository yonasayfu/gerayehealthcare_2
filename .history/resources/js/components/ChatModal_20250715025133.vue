<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Send, X, MessageSquareText, Search } from 'lucide-vue-next';
import { format } from 'date-fns';
import axios from 'axios';
import type { User, Message, Conversation, AppPageProps } from '@/types'; // Import types

// --- DEBUG: Log when component script is loaded ---
console.log('ChatModal component script loaded. (New UI version)');
// TODO: Clarify "fix chstmode inputbox and notification"

const props = defineProps<{
  isOpen: boolean;
  initialConversationId?: number | null; // New prop
}>();

const emit = defineEmits(['close']);

const page = usePage<AppPageProps>();
const authUser = computed<User>(() => page.props.auth.user);

const chatContainer = ref<HTMLElement | null>(null);
const search = ref<string>('');
const conversations = ref<Conversation[]>([]);
const selectedConversation = ref<Conversation | null>(null);
const messages = ref<Message[]>([]);
const loading = ref<boolean>(false);

const form = useForm({
  receiver_id: null as number | null,
  message: '',
});

const fetchMessages = async (recipientId: number | null = null, searchQuery: string = '') => {
  loading.value = true;
  try {
    const response = await axios.get(route('messages.data', { recipient: recipientId, search: searchQuery }));
    let fetchedConversations: Conversation[] = response.data.conversations;

    // Add current user to the top if not present
    const user = authUser.value;
    if (user && !fetchedConversations.some((c: Conversation) => c.id === user.id)) {
      fetchedConversations = [{ ...user, name: user.name, staff: user.staff }, ...fetchedConversations];
    }
    conversations.value = fetchedConversations;

    selectedConversation.value = response.data.selectedConversation;

    // If no conversation is selected, default to current user
    if (!selectedConversation.value && conversations.value.length > 0) {
      selectedConversation.value = { ...conversations.value[0] };
      // If the selected is not the current user, fetch messages for that user
      if (selectedConversation.value.id !== user.id) {
        const resp = await axios.get(route('messages.data', { recipient: selectedConversation.value.id, search: searchQuery }));
        messages.value = resp.data.messages;
        selectedConversation.value = { ...resp.data.selectedConversation };
      } else {
        messages.value = [];
      }
    } else {
      messages.value = response.data.messages;
    }

    form.receiver_id = selectedConversation.value?.id || null;
    if (selectedConversation.value) scrollToBottom();
  } catch (error) {
    console.error('Error fetching messages:', error);
  } finally {
    loading.value = false;
  }
};

watch(() => props.isOpen, (newVal: boolean) => {
  console.log('ChatModal: props.isOpen changed. New value:', newVal);
  if (newVal) {
    // If initialConversationId is provided, use it to fetch messages
    fetchMessages(props.initialConversationId || null);
  }
});

watch(search, (value: string) => {
  fetchMessages(null, value);
});

const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
      console.log('Scrolled to bottom.');
    }
  });
};

watch(() => messages.value, scrollToBottom, { deep: true, immediate: true });
watch(() => selectedConversation.value, (newVal: Conversation | null) => {
    console.log('--- selectedConversation watch triggered ---');
    console.log('New selectedConversation value:', newVal);
    form.receiver_id = newVal?.id || null;
    scrollToBottom(); // Scroll to bottom when conversation changes
});

const submit = async () => {
  if (!form.receiver_id || !form.message.trim()) {
    console.warn('Attempted to submit empty message or no receiver_id.');
    return;
  }
  try {
    await axios.post(route('messages.store'), {
      receiver_id: form.receiver_id,
      message: form.message,
    });
    form.reset('message');
    if (selectedConversation.value) { // Ensure selectedConversation is not null before accessing id
      fetchMessages(selectedConversation.value.id); // Re-fetch messages after sending
    }
  } catch (error) {
    console.error('Error sending message:', error);
  }
};

const selectConversation = (convoId: number) => {
  console.log('Conversation selected by click. Convo ID:', convoId);
  messages.value = []; // Clear messages visually
  fetchMessages(convoId);
};
</script>

<template>
  <div v-if="props.isOpen" class="fixed bottom-6 right-6 z-50 chat-modal-widget">
    <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[400px] h-[600px] flex flex-col overflow-hidden">
      <!-- Header -->
      <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700">
        <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
          <MessageSquareText class="w-5 h-5 text-indigo-500" /> Messages
        </h2>
        <button @click="emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition">
          <X class="w-5 h-5" />
        </button>
      </div>
      <div class="flex-grow flex">
        <!-- Sidebar (unchanged) -->
        <div class="w-1/3 flex flex-col bg-gray-100 dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="p-4 border-b border-gray-200 dark:border-gray-700 relative">
            <input
              type="text"
              v-model="search"
              placeholder="Search conversations..."
              class="pr-10 form-input w-full rounded-full border border-gray-300 px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-700 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
            />
            <Search class="absolute right-7 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 w-5 h-5" />
          </div>
          <div class="flex-grow overflow-y-auto custom-scrollbar">
            <div v-if="loading && conversations.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400">Loading conversations...</div>
            <div v-else-if="conversations.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400">No conversations found.</div>
            <div
              v-for="convo in conversations"
              :key="convo.id"
              @click="selectConversation(convo.id)"
              class="flex items-center gap-3 p-4 border-b border-gray-200 dark:border-gray-800 hover:bg-indigo-50 dark:hover:bg-gray-800 transition cursor-pointer"
              :class="{ 'bg-indigo-100 dark:bg-indigo-900/40 font-semibold': selectedConversation?.id === convo.id }"
            >
              <div class="w-10 h-10 rounded-full bg-indigo-200 dark:bg-indigo-700 flex items-center justify-center text-indigo-800 dark:text-indigo-200 text-sm font-medium">
                {{ convo.name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <p class="font-medium text-gray-900 dark:text-gray-100">{{ convo.name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ convo.staff?.position || 'User' }}</p>
              </div>
            </div>
          </div>
        </div>
        <!-- Main chat area -->
        <div class="w-2/3 flex flex-col bg-white dark:bg-gray-800">
          <template v-if="selectedConversation">
            <!-- Chat Header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ selectedConversation.name }}</h2>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ selectedConversation.staff?.position || 'User' }}</p>
            </div>
            <!-- Messages -->
            <div ref="chatContainer" class="flex-grow p-6 overflow-y-auto space-y-4 custom-scrollbar">
              <div
                v-for="message in messages"
                :key="message.id"
                class="flex"
                :class="[message.sender_id === authUser.id ? 'justify-end' : 'justify-start']"
              >
                <div
                  class="max-w-md rounded-2xl px-4 py-2 text-sm shadow-md"
                  :class="{
                    'bg-indigo-600 text-white rounded-br-none': message.sender_id === authUser.id,
                    'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-bl-none': message.sender_id !== authUser.id,
                  }"
                >
                  <p class="break-words">{{ message.message }}</p>
                  <p class="text-xs mt-1 opacity-80 text-right">{{ format(new Date(message.created_at), 'p') }}</p>
                </div>
              </div>
              <div v-if="messages.length === 0 && !loading" class="text-center text-gray-500 dark:text-gray-400 py-10">
                <p>No messages yet with {{ selectedConversation.name }}.</p>
                <p>Start the conversation!</p>
              </div>
            </div>
            <!-- Message Input Form -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
              {{ console.log('Rendering message input form. Selected conversation ID:', selectedConversation?.id) }}
              <form @submit.prevent="submit" class="flex items-center gap-3">
                <input
                  v-model="form.message"
                  type="text"
                  placeholder="Type your message here..."
                  class="flex-grow form-input w-full rounded-full border border-gray-300 px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                  autocomplete="off"
                />
                <button
                  type="submit"
                  :disabled="form.processing || !form.message.trim()"
                  class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-40 disabled:cursor-not-allowed transition duration-150 ease-in-out"
                >
                  <Send class="w-5 h-5" />
                </button>
              </form>
            </div>
          </template>
          <template v-else>
            <div class="flex-grow flex items-center justify-center">
              <p class="text-gray-500">Select a conversation to start messaging.</p>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
/* Custom Scrollbar for nicer appearance */
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
  background-color: #f5f5f5; /* Light gray for background */
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  border-radius: 10px;
  background-color: #cbd5e1; /* Gray-300 */
}

.dark .custom-scrollbar::-webkit-scrollbar {
  background-color: #1a202c; /* Dark gray for background */
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #4a5568; /* Gray-700 */
}
.chat-modal-widget {
  /* For mobile responsiveness, see above */
}
</style>
