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
  loading.value = true;
  try {
    const response = await axios.get(route('messages.data', { recipient: recipientId, search: searchQuery }));
    let fetchedConversations = response.data.conversations;

    // Add current user to the top if not present
    const user = authUser.value;
    if (user && !fetchedConversations.some(c => c.id === user.id)) {
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

watch(() => props.isOpen, (newVal) => {
  console.log('ChatModal: props.isOpen changed. New value:', newVal);
  if (newVal) {
    fetchMessages();
  }
});

watch(search, (value) => {
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
watch(() => selectedConversation.value, (newVal) => {
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
    fetchMessages(selectedConversation.value.id); // Re-fetch messages after sending
  } catch (error) {
    console.error('Error sending message:', error);
  }
};

const selectConversation = (convoId) => {
  console.log('Conversation selected by click. Convo ID:', convoId);
  // Temporarily set selectedConversation to null to trigger reactivity and show loading/placeholder
  selectedConversation.value = null;
  messages.value = []; // Clear messages visually
  fetchMessages(convoId);
};
</script>

<template>
  <div v-if="props.isOpen" class="fixed inset-0 flex justify-center items-end md:items-center bg-black bg-opacity-30 z-50">
    <div class="bg-white dark:bg-gray-800 rounded-t-xl md:rounded-xl shadow-2xl w-full md:w-1/2 lg:w-1/3 h-auto max-h-[90vh] flex flex-col">
      <header class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
         <h2 class="text-xl font-bold text-gray-800 dark:text-white">Messages</h2>
         <button @click="emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">X</button>
      </header>
      <div class="flex flex-col md:flex-row flex-grow">
         <aside class="w-full md:w-1/3 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
            <div class="p-3">
               <input type="text" v-model="search" placeholder="Search..." class="w-full p-2 border rounded"/>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-600">
              <div v-for="convo in conversations" :key="convo.id" @click="selectConversation(convo.id)" class="p-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                 <div class="text-sm font-semibold">{{ convo.name }}</div>
              </div>
            </div>
         </aside>
         <main class="w-full md:w-2/3 flex flex-col">
            <div class="flex-grow p-4 overflow-y-auto" ref="chatContainer">
              <div v-if="loading" class="text-center">Loading messages...</div>
              <div v-else-if="!selectedConversation">
                <div class="text-center text-gray-500 dark:text-gray-400">Select a conversation.</div>
              </div>
              <div v-else>
                <div v-for="message in messages" :key="message.id" class="mb-2" :class="{'text-right': message.sender_id === authUser.id, 'text-left': message.sender_id !== authUser.id}">
                   <div class="inline-block bg-gray-200 dark:bg-gray-700 p-2 rounded-lg">
                      {{ message.message }}
                   </div>
                   <div class="text-xs text-gray-500">{{ format(new Date(message.created_at), 'p') }}</div>
                </div>
              </div>
            </div>
            <form @submit.prevent="submit" class="flex items-center p-3 border-t border-gray-200 dark:border-gray-700">
               <input type="text" v-model="form.message" placeholder="Type your message..." class="flex-grow p-2 border rounded focus:outline-none" autocomplete="off"/>
               <button type="submit" :disabled="form.processing || !form.message.trim()" class="ml-2 p-2 bg-indigo-600 text-white rounded disabled:opacity-40">Send</button>
            </form>
         </main>
      </div>
    </div>
  </div>

  </div>
</template>

<style>
/* Updated ChatModal UI styles */
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
  background-color: #f5f5f5;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  border-radius: 10px;
  background-color: #cbd5e1;
}

.dark .custom-scrollbar::-webkit-scrollbar {
  background-color: #1a202c;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #4a5568;
}

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