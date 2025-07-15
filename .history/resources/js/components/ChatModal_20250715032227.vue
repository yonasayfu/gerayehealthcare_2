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

const modalWidth = ref(400);
const modalHeight = ref(600);
const isResizing = ref(false);
let startX = 0, startY = 0, startWidth = 0, startHeight = 0;
const startResize = (e: MouseEvent) => {
  isResizing.value = true;
  startX = e.clientX;
  startY = e.clientY;
  startWidth = modalWidth.value;
  startHeight = modalHeight.value;
  document.addEventListener('mousemove', onMouseMove);
  document.addEventListener('mouseup', onMouseUp);
};
const onMouseMove = (e: MouseEvent) => {
  if (!isResizing.value) return;
  const dx = e.clientX - startX;
  const dy = e.clientY - startY;
  modalWidth.value = Math.max(300, startWidth + dx);
  modalHeight.value = Math.max(200, startHeight + dy);
};
const onMouseUp = () => {
  isResizing.value = false;
  document.removeEventListener('mousemove', onMouseMove);
  document.removeEventListener('mouseup', onMouseUp);
};

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
    <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl flex flex-col overflow-hidden resize" :style="{ width: modalWidth + 'px', height: modalHeight + 'px', minWidth: '300px', minHeight: '400px' }">
      <!-- Card Header -->
      <div class="card-header d-flex justify-content-between align-items-center p-3" style="border-top: 4px solid #ffa900;">
        <h5 class="mb-0">Chat messages</h5>
        <div>
          <button @click="emit('close')" class="btn btn-danger btn-sm">Close</button>
        </div>
      </div>
      <!-- Card Body -->
      <div class="card-body overflow-y-auto" style="position: relative; height: calc(100% - 130px);">
        <div class="row">
          <!-- Sidebar: Conversation List -->
          <div class="col-4" style="border-right: 1px solid #eee;">
            <div class="mb-3">
              <input type="text" v-model="search" placeholder="Search..." class="form-control">
            </div>
            <div v-if="loading && conversations.length === 0" class="text-center text-muted">Loading conversations...</div>
            <div v-else-if="conversations.length === 0" class="text-center text-muted">No conversations found.</div>
            <div v-for="convo in conversations" :key="convo.id" @click="selectConversation(convo.id)" class="p-2 mb-1" :class="{'bg-light font-weight-bold': selectedConversation && selectedConversation.id === convo.id, 'cursor-pointer': true}">
              <div class="d-flex align-items-center">
                <div class="bg-secondary text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                  {{ convo.name.charAt(0).toUpperCase() }}
                </div>
                <div class="ms-2">
                  <p class="mb-0">{{ convo.name }}</p>
                  <small class="text-muted">{{ convo.staff?.position || 'User' }}</small>
                </div>
              </div>
            </div>
          </div>
          <!-- Main Chat Area -->
          <div class="col-8">
            <div v-if="!selectedConversation" class="text-center text-muted mt-4">
              <p>Select a conversation to start messaging.</p>
            </div>
            <div v-else class="d-flex flex-column h-100">
              <!-- Chat Header -->
              <div class="mb-2 p-2 border-bottom">
                <h5 class="mb-0">{{ selectedConversation.name }}</h5>
                <small class="text-muted">{{ selectedConversation.staff?.position || 'User' }}</small>
              </div>
              <!-- Messages -->
              <div ref="chatContainer" class="flex-grow overflow-auto mb-2">
                <div v-for="message in messages" :key="message.id" class="mb-2">
                  <div class="d-flex" :class="{'justify-content-end': message.sender_id === authUser.id, 'justify-content-start': message.sender_id !== authUser.id}">
                    <div v-if="message.sender_id !== authUser.id" class="d-flex">
                      <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3-bg.webp" alt="avatar" style="width: 45px; height: 45px;" class="rounded-circle">
                      <div class="ms-2">
                        <div class="p-2 rounded-3 bg-light">
                          {{ message.message }}
                        </div>
                        <small class="text-muted">{{ format(new Date(message.created_at), 'p') }}</small>
                      </div>
                    </div>
                    <div v-else class="d-flex flex-column align-items-end">
                      <div class="p-2 rounded-3 bg-primary text-white">
                        {{ message.message }}
                      </div>
                      <small class="text-muted">{{ format(new Date(message.created_at), 'p') }}</small>
                    </div>
                  </div>
                </div>
                <div v-if="messages.length === 0 && !loading" class="text-center text-muted py-3">
                  <p>No messages yet with {{ selectedConversation.name }}.</p>
                  <p>Start the conversation!</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Card Footer with new attachment feature -->
      <div class="card-footer d-flex align-items-center p-3">
        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3-bg.webp" alt="avatar" style="width: 40px; height: 40px;" class="rounded-circle me-2">
        <input type="text" v-model="form.message" class="form-control" placeholder="Type message">
        <a class="ms-2 text-muted" href="#" @click.prevent><i class="fas fa-paperclip"></i></a>
        <a class="ms-2 text-muted" href="#" @click.prevent><i class="fas fa-smile"></i></a>
        <a class="ms-2" href="#" @click.prevent="submit"><i class="fas fa-paper-plane"></i></a>
      </div>
      <!-- Resize Handle -->
      <div class="resize-handle absolute bottom-2 right-2 w-6 h-6 cursor-se-resize" @mousedown="startResize"></div>
    </div>
  </div>
</template>
    <div
      class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl flex flex-col overflow-hidden"
      :style="{ width: modalWidth + 'px', height: modalHeight + 'px', minWidth: '300px', minHeight: '400px' }"
    >
      <!-- Header -->
      <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700">
        <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2">
          <MessageSquareText class="w-5 h-5 text-indigo-500" /> Messages
        </h2>
        <button @click="emit('close')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition">
          <X class="w-5 h-5" />
        </button>
      </div>
      <div class="flex-grow flex min-h-0">
        <!-- Sidebar -->
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
        <div class="w-2/3 flex flex-col bg-white dark:bg-gray-800 min-h-0">
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
            <!-- Chat Header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ selectedConversation.name }}</h2>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ selectedConversation.staff?.position || 'User' }}</p>
            </div>
            <!-- Messages -->
            <div ref="chatContainer" class="flex-grow p-6 overflow-y-auto space-y-4 custom-scrollbar min-h-0">
              <div
                v-for="message in messages"
                :key="message.id"
                class="flex"
                :class="[message.sender_id === authUser.id ? 'justify-end' : 'justify-start']"
              >
                <div
                  class="max-w-md rounded-xl px-4 py-2 text-sm shadow-md relative message-bubble"
                  :class="{
                    'bg-indigo-600 text-white self-end message-sent': message.sender_id === authUser.id,
                    'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 self-start message-received': message.sender_id !== authUser.id,
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
                  :disabled="form.processing || !form.message.trim() || !selectedConversation?.id"
                  class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-40 disabled:cursor-not-allowed transition duration-150 ease-in-out"
                >
                  <Send class="w-5 h-5" />
                </button>
              </form>
            </div>
          </template>
        </div>
      </div>
      <!-- Resize handle -->
      <div class="resize-handle absolute bottom-2 right-2 w-6 h-6 cursor-se-resize" @mousedown="startResize"></div>
    </div>
  </div>
</template>

<style>
/* Additional styles for the new chat UI */
.card-header { background-color: #fff; }
.card-body { background-color: #f8f9fa; }
.card-footer { background-color: #fff; }
.resize-handle { background: rgba(0, 0, 0, 0.2); border-radius: 3px; }
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

.message-bubble {
  position: relative;
  max-width: 75%; /* Adjust as needed */
}

.message-sent {
  border-bottom-right-radius: 4px !important; /* Smaller radius for the "tail" corner */
}

.message-received {
  border-bottom-left-radius: 4px !important; /* Smaller radius for the "tail" corner */
}

.message-sent::after {
  content: '';
  position: absolute;
  bottom: 0;
  right: -7px; /* Adjust to position the tail */
  width: 0;
  height: 0;
  border: 8px solid transparent;
  border-bottom-color: #4f46e5; /* Indigo-600 */
  border-right-color: #4f46e5; /* Indigo-600 */
  border-bottom-left-radius: 4px; /* Match bubble radius */
  transform: rotate(45deg);
  z-index: -1; /* Ensure it's behind the bubble content */
}

.message-received::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: -7px; /* Adjust to position the tail */
  width: 0;
  height: 0;
  border: 8px solid transparent;
  border-bottom-color: #e5e7eb; /* Gray-200 */
  border-left-color: #e5e7eb; /* Gray-200 */
  border-bottom-right-radius: 4px; /* Match bubble radius */
  transform: rotate(-45deg);
  z-index: -1;
}

/* Dark mode adjustments for tails */
.dark .message-sent::after {
  border-bottom-color: #4f46e5; /* Indigo-600 */
  border-right-color: #4f46e5; /* Indigo-600 */
}

.dark .message-received::after {
  border-bottom-color: #374151; /* Gray-700 */
  border-left-color: #374151; /* Gray-700 */
}
</style>
