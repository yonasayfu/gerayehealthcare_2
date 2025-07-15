<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Send, X, MessageSquareText, Search } from 'lucide-vue-next'; // Added Search for input icon
import { format } from 'date-fns';
import axios from 'axios';

// --- DEBUG: Log when component script is loaded ---
console.log('ChatModal component script loaded. (New UI version)');

const props = defineProps<{
  isOpen: boolean;
}>();

const emit = defineEmits(['close']);

const page = usePage();
const authUser = computed(() => page.props.auth.user);

const chatContainer = ref<HTMLElement | null>(null);
const search = ref('');
const conversations = ref([]);
const selectedConversation = ref(null);
const messages = ref([]);
const loading = ref(false); // New loading state

const form = useForm({
  receiver_id: null,
  message: '',
});

const fetchMessages = async (recipientId = null, searchQuery = '') => {
  loading.value = true; // Set loading to true
  try {
    console.log('--- fetchMessages called ---');
    console.log('Recipient ID passed to fetchMessages:', recipientId);
    console.log('Search Query passed to fetchMessages:', searchQuery);

    const response = await axios.get(route('messages.data', { recipient: recipientId, search: searchQuery }));

    console.log('Backend response (response.data):', response.data);

    conversations.value = response.data.conversations;
    messages.value = response.data.messages;

    if (response.data.selectedConversation) {
      selectedConversation.value = response.data.selectedConversation;
      console.log('Selected conversation from backend:', selectedConversation.value.name);
    } else if (conversations.value.length > 0) {
      selectedConversation.value = conversations.value[0];
      console.log('Selected first conversation locally:', selectedConversation.value.name, '(ID:', selectedConversation.value.id + ')');
    } else {
      selectedConversation.value = null;
      console.log('No conversations found, selectedConversation is null.');
    }

    form.receiver_id = selectedConversation.value?.id || null;
    console.log('form.receiver_id set to:', form.receiver_id);
    console.log('Final selectedConversation.value state after fetch:', selectedConversation.value);

    // Only scroll to bottom if we have a conversation selected
    if (selectedConversation.value) {
      scrollToBottom();
    }
  } catch (error) {
    console.error('Error fetching messages:', error);
    if (error.response) {
      console.error('Error response data:', error.response.data);
      console.error('Error response status:', error.response.status);
    }
  } finally {
    loading.value = false; // Set loading to false
  }
};

watch(() => props.isOpen, (newVal) => {
  console.log('ChatModal: props.isOpen changed. New value:', newVal);
  if (newVal) {
    fetchMessages();
  }
});

watch(search, (value) => {
  console.log('Search input changed. New value:', value);
  fetchMessages(selectedConversation.value?.id, value);
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
watch(() => selectedConversation.value, (newVal) => {
  console.log('--- selectedConversation watch triggered ---');
  console.log('New selectedConversation value:', newVal);
  form.receiver_id = newVal?.id || null;
  scrollToBottom();
});

const submit = () => {
  if (!form.receiver_id || !form.message.trim()) { // Added .trim() to ensure message isn't just whitespace
    console.warn('Attempted to submit empty message or no receiver_id.');
    return;
  }
  console.log('Submitting message to receiver:', form.receiver_id);
  form.post(route('messages.store'), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('Message sent successfully. Resetting form and re-fetching messages.');
      form.reset('message');
      fetchMessages(selectedConversation.value.id);
    },
    onError: (errors) => {
      console.error('Error sending message:', errors);
    }
  });
};

const selectConversation = (convoId) => {
  console.log('Conversation selected by click. Convo ID:', convoId);
  // Temporarily set selectedConversation to null to trigger reactivity and show loading/placeholder
  selectedConversation.value = null;
  messages.value = []; // Clear messages visually
  fetchMessages(convoId);
};
  console.log('Conversation selected by click. Convo ID:', convoId);
  if (selectedConversation.value?.id !== convoId) { // Prevent re-fetching if already selected
    selectedConversation.value = null; // Clear selected to show placeholder until new data loads
    messages.value = []; // Clear messages visually
    fetchMessages(convoId);
  }
};
</script>

<template>
  <div v-if="props.isOpen" class="fixed inset-0 z-40 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="emit('close')"></div>

    <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden transform transition-all duration-300 ease-out scale-95 opacity-0"
         :class="{ 'scale-100 opacity-100': props.isOpen }">

      <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
          <MessageSquareText class="w-6 h-6 text-indigo-500" /> Messages
        </h2>
        <button @click="emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition">
          <X class="w-6 h-6" />
        </button>
      </div>

      <div class="flex-grow flex">
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

        <div class="w-2/3 flex flex-col bg-white dark:bg-gray-800">
          <template v-if="loading && !selectedConversation">
             <div class="flex-grow flex items-center justify-center">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <svg class="animate-spin h-8 w-8 text-indigo-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-2">Loading messages...</p>
                </div>
            </div>
          </template>
          <template v-else-if="!selectedConversation">
            <div class="flex-grow flex items-center justify-center p-6 text-center">
              <div class="text-gray-500 dark:text-gray-400">
                <MessageSquareText class="w-12 h-12 mx-auto mb-4 text-indigo-400" />
                <p class="text-lg font-semibold">Select a conversation</p>
                <p class="text-sm">Choose a person from the left pane to start chatting.</p>
              </div>
            </div>
          </template>
          <template v-else>
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
              <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ selectedConversation.name }}</h2>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ selectedConversation.staff?.position || 'User' }}</p>
            </div>

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

            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
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
</style>