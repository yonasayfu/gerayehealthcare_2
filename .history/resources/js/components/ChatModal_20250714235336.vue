<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Send, X } from 'lucide-vue-next';
import { format } from 'date-fns';
import axios from 'axios';

// --- DEBUG: Log when component script is loaded ---
console.log('MessageModal component script loaded.');

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

const form = useForm({
  receiver_id: null,
  message: '',
});

const fetchMessages = async (recipientId = null, searchQuery = '') => {
  try {
    // --- DEBUG: Log when fetchMessages is called ---
    console.log('--- fetchMessages called ---');
    console.log('Recipient ID passed to fetchMessages:', recipientId);
    console.log('Search Query passed to fetchMessages:', searchQuery);

    const response = await axios.get(route('messages.data', { recipient: recipientId, search: searchQuery }));

    // --- DEBUG: Log the full backend response ---
    console.log('Backend response (response.data):', response.data);

    conversations.value = response.data.conversations;
    messages.value = response.data.messages;

    // Prioritize: If backend sent a selectedConversation, use it.
    if (response.data.selectedConversation) {
      selectedConversation.value = response.data.selectedConversation;
      console.log('Selected conversation from backend:', selectedConversation.value.name);
    }
    // Fallback: If no selectedConversation from backend, and there are conversations, select the first one locally.
    else if (conversations.value.length > 0) {
      selectedConversation.value = conversations.value[0];
      console.log('Selected first conversation locally:', selectedConversation.value.name, '(ID:', selectedConversation.value.id + ')');
    }
    // Handle case where there are no conversations at all
    else {
      selectedConversation.value = null;
      console.log('No conversations found, selectedConversation is null.');
    }

    // Ensure form.receiver_id is always in sync with selectedConversation's ID
    form.receiver_id = selectedConversation.value?.id || null;
    console.log('form.receiver_id set to:', form.receiver_id);
    console.log('Final selectedConversation.value state after fetch:', selectedConversation.value); // Check its value here

    scrollToBottom();
  } catch (error) {
    console.error('Error fetching messages:', error);
    if (error.response) {
      console.error('Error response data:', error.response.data);
      console.error('Error response status:', error.response.status);
    }
  }
};

// --- DEBUG: Watch for changes in props.isOpen ---
watch(() => props.isOpen, (newVal) => {
  console.log('MessageModal: props.isOpen changed. New value:', newVal);
  if (newVal) {
    fetchMessages();
  }
});

// --- DEBUG: Watch for changes in search input ---
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

// --- DEBUG: Watch for changes in messages and selected conversation ---
watch(() => messages.value, scrollToBottom, { deep: true, immediate: true });
watch(() => selectedConversation.value, (newVal) => {
    console.log('--- selectedConversation watch triggered ---');
    console.log('New selectedConversation value:', newVal);
    form.receiver_id = newVal?.id || null;
    scrollToBottom(); // Scroll to bottom when conversation changes
});

const submit = () => {
  if (!form.receiver_id) {
    console.warn('Attempted to submit message with no receiver_id.');
    return;
  }
  console.log('Submitting message to receiver:', form.receiver_id);
  form.post(route('messages.store'), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('Message sent successfully. Resetting form and re-fetching messages.');
      form.reset('message');
      fetchMessages(selectedConversation.value.id); // Re-fetch messages after sending
    },
    onError: (errors) => {
      console.error('Error sending message:', errors);
    }
  });
};

const selectConversation = (convoId) => {
  console.log('Conversation selected by click. Convo ID:', convoId);
  fetchMessages(convoId);
};
</script>

<template>
  <div v-if="props.isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl h-[80vh] flex flex-col">
      <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Messages</h2>
        <button @click="emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
          <X class="w-5 h-5" />
        </button>
      </div>

      <div class="flex-grow flex">
        <div class="w-1/3 border-r border-gray-200 dark:border-gray-700 flex flex-col">
          <div class="p-4 border-b dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Conversations</h2>
            <input
              type="text"
              v-model="search"
              placeholder="Search conversations..."
              class="form-input w-full rounded-md border border-gray-300 px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100 mt-2"
            />
          </div>
          <div class="flex-grow overflow-y-auto">
            <div
              v-for="convo in conversations"
              :key="convo.id"
              @click="selectConversation(convo.id)"
              class="block p-4 border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition cursor-pointer"
              :class="{ 'bg-indigo-100 dark:bg-indigo-900/50': selectedConversation?.id === convo.id }"
            >
              <p class="font-semibold text-gray-900 dark:text-gray-100">{{ convo.name }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ convo.staff?.position || 'User' }}</p>
            </div>
          </div>
        </div>

        <div class="w-2/3 flex flex-col">
          <template v-if="selectedConversation">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ selectedConversation.name }}</h2>
            </div>

            <div ref="chatContainer" class="flex-grow p-6 overflow-y-auto space-y-4">
              <div
                v-for="message in messages"
                :key="message.id"
                class="flex"
                :class="[message.sender_id === authUser.id ? 'justify-end' : 'justify-start']"
              >
                <div
                  class="max-w-md rounded-lg px-4 py-2"
                  :class="{
                    'bg-indigo-600 text-white': message.sender_id === authUser.id,
                    'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200': message.sender_id !== authUser.id,
                  }"
                >
                  <p>{{ message.message }}</p>
                  <p class="text-xs mt-1 opacity-75">{{ format(new Date(message.created_at), 'p') }}</p>
                </div>
              </div>
              <div v-if="messages.length === 0" class="text-center text-gray-500">
                  No messages yet. Start the conversation!
              </div>
            </div>

            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
              <form @submit.prevent="submit" class="flex items-center gap-4">
                <input
                  v-model="form.message"
                  type="text"
                  placeholder="Type a message..."
                  class="flex-grow form-input w-full rounded-md border border-gray-300 px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100"
                  autocomplete="off"
                />
                <button
                  type="submit"
                  :disabled="form.processing || !form.message"
                  class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-50 transition"
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