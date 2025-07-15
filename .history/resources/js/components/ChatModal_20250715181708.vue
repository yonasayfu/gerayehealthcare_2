<script setup lang="ts">
import { ref, watch, nextTick, computed, onMounted, onUnmounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Send, X, MessageSquareText, Search, Minus, GripVertical, Paperclip, Smile, File, Image } from 'lucide-vue-next';
import { format } from 'date-fns';
import axios from 'axios';
import type { User, Message, Conversation, AppPageProps } from '@/types';

// --- DEBUG: 0. Component Init ---
console.log('0. ChatModal component script loaded. (Full Debug Version - Final attempt)');

const props = defineProps<{
  isOpen: boolean;
  initialConversationId?: number | null;
}>();

const emit = defineEmits(['close', 'minimize']);

const page = usePage<AppPageProps>();
const authUser = computed<User>(() => {
  const user = page.props.auth.user;
  console.log('0.5 Auth User Computed:', user ? user.name : 'Not logged in');
  return user;
});

const chatContainer = ref<HTMLElement | null>(null);
const search = ref<string>('');
const conversations = ref<Conversation[]>([]);
const selectedConversation = ref<Conversation | null>(null);
const messages = ref<Message[]>([]);
const loading = ref<boolean>(false);
const showConversationList = ref(true);

// --- Window Width for Responsiveness ---
const currentWindowWidth = ref(0);

const updateWindowWidth = () => {
  currentWindowWidth.value = window.innerWidth;
  // Adjust showConversationList based on width
  if (currentWindowWidth.value >= 768) {
    showConversationList.value = true; // Always show list on desktop
  } else {
    // On mobile, show list initially if no conversation selected or if explicitly showing list
    if (!selectedConversation.value) {
      showConversationList.value = true;
    }
  }
};

// --- DEBUG: 1. onMounted Hook (initial mount check) ---
onMounted(() => {
  console.log('1. ChatModal component mounted.');
  updateWindowWidth(); // Set initial width and responsiveness
  window.addEventListener('resize', updateWindowWidth);

  // Set initial modal size based on current window size
  modalWidth.value = currentWindowWidth.value > 1024 ? 800 : Math.min(currentWindowWidth.value - 40, 600);
  modalHeight.value = currentWindowWidth.value > 768 ? 600 : Math.min(currentWindowWidth.value - 80, 500);

  console.log('1.1 Initial props.isOpen on mount:', props.isOpen);
  if (props.isOpen) {
    console.log('1.2 Modal is already open on mount or immediate watch, calling fetchMessages.');
    fetchMessages(props.initialConversationId || null);
  }
});

onUnmounted(() => {
  window.removeEventListener('resize', updateWindowWidth);
});

// --- Resizing Logic ---
const modalRef = ref<HTMLElement | null>(null);
const modalWidth = ref(800);
const modalHeight = ref(600);
const isResizing = ref(false);
let startX = 0, startY = 0, startWidth = 0, startHeight = 0;

const startResize = (e: MouseEvent) => {
  e.preventDefault();
  isResizing.value = true;
  startX = e.clientX;
  startY = e.clientY;
  startWidth = modalRef.value?.offsetWidth || modalWidth.value;
  startHeight = modalRef.value?.offsetHeight || modalHeight.value;

  document.addEventListener('mousemove', onMouseMove);
  document.addEventListener('mouseup', onMouseUp);
};

const onMouseMove = (e: MouseEvent) => {
  if (!isResizing.value) return;
  const dx = e.clientX - startX;
  const dy = e.clientY - startY;

  // Use min/max based on window size
  const minModalWidth = currentWindowWidth.value < 768 ? currentWindowWidth.value * 0.9 : 500;
  const maxModalWidth = currentWindowWidth.value * 0.95;
  const minModalHeight = currentWindowWidth.value < 768 ? currentWindowWidth.value * 0.8 : 400;
  const maxModalHeight = currentWindowWidth.value * 0.95;

  modalWidth.value = Math.min(maxModalWidth, Math.max(minModalWidth, startWidth + dx));
  modalHeight.value = Math.min(maxModalHeight, Math.max(minModalHeight, startHeight + dy));
};

const onMouseUp = () => {
  isResizing.value = false;
  document.removeEventListener('mousemove', onMouseMove);
  document.removeEventListener('mouseup', onMouseUp);
};

// --- Attachment Handling ---
const form = useForm({
  receiver_id: null,
  message: '',
  attachment: null as File | null, // Added attachment field
});

const attachmentInput = ref<HTMLInputElement | null>(null);

const triggerAttachmentInput = () => {
  attachmentInput.value?.click();
};

const handleAttachmentChange = (e: Event) => {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    form.attachment = target.files[0];
    console.log('Attachment selected:', form.attachment.name);
  } else {
    form.attachment = null;
    console.log('Attachment cleared.');
  }
};

const removeAttachment = () => {
  form.attachment = null;
  if (attachmentInput.value) {
    attachmentInput.value.value = ''; // Clear file input value
  }
};

// --- Emoji Handling (Placeholder) ---
const handleEmojiClick = () => {
  alert('Emoji picker will be integrated here! For now, please use your system emoji keyboard (Win + . or Ctrl + Cmd + Space).');
};

// --- Fetching Logic ---
const fetchMessages = async (recipientId: number | null = null, searchQuery: string = '') => {
  loading.value = true;
  // --- DEBUG: 2. Fetch Messages Call ---
  console.log('2. --- fetchMessages called ---');
  console.log('2.1 Recipient ID passed to fetchMessages:', recipientId);
  console.log('2.2 Search Query passed to fetchMessages:', searchQuery);

  try {
    const routeUrl = route('messages.data', { recipient: recipientId, search: searchQuery });
    console.log('2.3 Attempting to fetch from URL:', routeUrl);

    const response = await axios.get(routeUrl);

    // --- DEBUG: 3. Backend Response Received ---
    console.log('3. Backend response received. Status:', response.status);
    console.log('3.1 Full Backend response (response.data):', response.data);
    console.log('3.2 Raw conversations from backend:', response.data.conversations);
    console.log('3.3 Raw selectedConversation from backend:', response.data.selectedConversation);
    console.log('3.4 Raw messages from backend:', response.data.messages);


    let fetchedConversations: Conversation[] = response.data.conversations;

    // Add current user to the top of conversations if not present for "self-chat"
    const user = authUser.value;
    if (user && !fetchedConversations.some((c: Conversation) => c.id === user.id)) {
      fetchedConversations = [{
        id: user.id,
        name: user.name,
        email: user.email,
        profile_photo_url: user.profile_photo_url,
        staff: user.staff || null // Assuming staff property exists for users, or can be null
      }, ...fetchedConversations];
    }
    conversations.value = fetchedConversations;

    let newSelectedConversation: Conversation | null = response.data.selectedConversation;

    // --- DEBUG: 4. Determining selectedConversation ---
    console.log('4. Determining selectedConversation logic...');
    if (newSelectedConversation) {
        console.log('4.1 Selected conversation from backend (priority 1):', newSelectedConversation.name, '(ID:', newSelectedConversation.id + ')');
    } else if (props.initialConversationId) {
        newSelectedConversation = fetchedConversations.find(c => c.id === props.initialConversationId) || null;
        console.log('4.2 Selected conversation from initial prop (priority 2):', newSelectedConversation ? newSelectedConversation.name : 'Not Found');
    }

    if (!newSelectedConversation && fetchedConversations.length > 0) {
        newSelectedConversation = fetchedConversations[0];
        console.log('4.3 Selected first conversation locally (priority 3):', newSelectedConversation.name, '(ID:', newSelectedConversation.id + ')');
    }

    selectedConversation.value = newSelectedConversation;
    messages.value = response.data.messages || [];

    // --- DEBUG: 5. Updating Form and Final State ---
    form.receiver_id = selectedConversation.value?.id || null;
    console.log('5.1 form.receiver_id set to:', form.receiver_id);
    console.log('5.2 FINAL selectedConversation.value state after fetch:', selectedConversation.value ? selectedConversation.value.name + ' (ID: ' + selectedConversation.value.id + ')' : 'NULL');
    console.log('5.3 Does selectedConversation.value exist (truthy)?', !!selectedConversation.value); // This tells us what v-if sees

    if (selectedConversation.value) scrollToBottom();
  } catch (error) {
    // --- DEBUG: Error Handling ---
    console.error('X. Error fetching messages:', error);
    if (error.response) {
      console.error('X.1 Error response data:', error.response.data);
      console.error('X.2 Error response status:', error.response.status);
      console.error('X.3 Error response headers:', error.response.headers);
    } else if (error.request) {
      console.error('X.4 Error request:', error.request);
    } else {
      console.error('X.5 General error message:', error.message);
    }
  } finally {
    loading.value = false;
    console.log('2.4 --- fetchMessages finished ---');
  }
};

// --- Watchers ---
// --- DEBUG: 6. Watch for props.isOpen changes ---
watch(() => props.isOpen, (newVal: boolean) => {
  console.log('6. Watch: props.isOpen changed. New value:', newVal);
  if (newVal) {
    console.log('6.1 props.isOpen is true, calling fetchMessages.');
    // Ensure initialConversationId is used if provided when opening the modal
    fetchMessages(props.initialConversationId || null);
  } else {
    console.log('6.2 props.isOpen is false, modal is closing. Resetting state.');
    selectedConversation.value = null;
    messages.value = [];
    conversations.value = [];
    search.value = '';
    form.reset();
  }
}, { immediate: true }); // immediate: true means it runs once on component mount if isOpen is already true

// --- DEBUG: 7. Watch for search input changes ---
watch(search, (value: string) => {
  console.log('7. Watch: Search input changed. New value:', value);
  // Re-fetch only conversations, keep current selected conversation active if possible
  // Or fetch for a new recipient if search is for a new person
  // For simplicity, let's refetch all with the search query
  fetchMessages(selectedConversation.value?.id, value);
});

const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
      console.log('8. Scrolled to bottom.');
    }
  });
};

// --- DEBUG: 9. Watch for messages changes to scroll ---
watch(() => messages.value, scrollToBottom, { deep: true });

// --- DEBUG: 10. Watch for selectedConversation changes to update form and scroll ---
watch(() => selectedConversation.value, (newVal: Conversation | null) => {
  console.log('10. Watch: selectedConversation watch triggered.');
  console.log('10.1 New selectedConversation value:', newVal ? newVal.name + ' (ID: ' + newVal.id + ')' : 'NULL');
  form.receiver_id = newVal?.id || null;
  console.log('10.2 form.receiver_id updated to:', form.receiver_id);
  // Only re-fetch messages for the new conversation if it's different from current
  // This is handled by selectConversation, but if it changes by other means, fetch.
  // We'll trust selectConversation and fetchMessages already handles this
  // We don't want to double fetch here if selectConversation just set it
  scrollToBottom(); // Always scroll when conversation changes
  if (currentWindowWidth.value < 768 && newVal) {
    showConversationList.value = false; // Hide conversation list on mobile when a conversation is selected
  }
});

// --- Submission Logic ---
const submit = async () => {
  if (!form.receiver_id || (!form.message.trim() && !form.attachment)) {
    console.warn('11. Attempted to submit empty message/no attachment or no receiver_id. Aborting.');
    return;
  }

  console.log('11. Submitting message to receiver:', form.receiver_id);
  console.log('11.1 Message content:', form.message);
  console.log('11.2 Attachment present:', !!form.attachment);

  form.post(route('messages.store'), {
    preserveScroll: true,
    forceFormData: true, // Crucial for file uploads with Inertia
    onSuccess: () => {
      console.log('11.3 Message sent successfully. Resetting form and re-fetching messages.');
      form.reset('message', 'attachment'); // Reset both message and attachment
      if (attachmentInput.value) {
        attachmentInput.value.value = ''; // Clear file input value visually
      }
      if (selectedConversation.value) { // Ensure selectedConversation is not null before re-fetching
        fetchMessages(selectedConversation.value.id);
      } else {
        console.warn('11.4 No selected conversation after sending message, cannot re-fetch for specific recipient.');
      }
    },
    onError: (errors) => {
      console.error('11.5 Error sending message:', errors);
      alert('Failed to send message or upload file. Please check file size/type. ' + JSON.stringify(errors));
    },
    onFinish: () => {
      console.log('11.6 Form submission finished.');
      form.processing = false; // Ensure processing state is reset
    }
  });
};

const selectConversation = (convoId: number) => {
  console.log('12. Conversation selected by click. Convo ID:', convoId);
  if (selectedConversation.value?.id !== convoId) {
    console.log('12.1 Different conversation selected, clearing current state and re-fetching.');
    selectedConversation.value = null; // Clear to trigger placeholder or loading state
    messages.value = []; // Clear messages visually
    fetchMessages(convoId); // Fetch messages for the newly selected conversation
  } else {
    console.log('12.2 Same conversation clicked, no re-fetch needed.');
    if (currentWindowWidth.value < 768) {
      showConversationList.value = false; // Hide list if it's the same conversation
    }
  }
};

// --- Responsive Toggles ---
const showChatList = () => {
  console.log('13. Showing conversation list (mobile).');
  showConversationList.value = true;
};
</script>

<template>
  <div v-if="props.isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="emit('close')"></div>

    <div
      ref="modalRef"
      class="relative bg-card text-foreground rounded-xl shadow-2xl flex flex-col overflow-hidden transform transition-all duration-200 ease-in-out"
      :style="{
        width: currentWindowWidth < 768 ? '95vw' : (modalWidth + 'px'),
        height: currentWindowWidth < 768 ? '90vh' : (modalHeight + 'px'),
        maxWidth: currentWindowWidth < 768 ? '95vw' : '100%',
        maxHeight: currentWindowWidth < 768 ? '90vh' : '100%',
      }"
    >
      <div class="p-3 border-b border-border flex justify-between items-center bg-card">
        <h2 class="text-lg font-semibold flex items-center gap-2">
          <MessageSquareText class="w-5 h-5 text-primary" /> Chat messages
        </h2>
        <div class="flex items-center gap-2">
          <button @click="emit('minimize')" class="text-muted-foreground hover:text-foreground p-1 rounded-full hover:bg-muted transition">
            <Minus class="w-5 h-5" />
          </button>
          <button @click="emit('close')" class="text-muted-foreground hover:text-foreground p-1 rounded-full hover:bg-muted transition">
            <X class="w-5 h-5" />
          </button>
        </div>
      </div>

      <div class="flex-grow flex min-h-0"
           :class="{
             'flex-col': currentWindowWidth < 768,
             'md:flex-row': currentWindowWidth >= 768
           }">

        <div v-show="showConversationList || currentWindowWidth >= 768"
             class="w-full md:w-1/3 flex flex-col bg-background border-r md:border-border overflow-hidden"
             :class="{ 'border-b': currentWindowWidth < 768 }">
          <div class="p-4 border-b border-border relative">
            <input
              type="text"
              v-model="search"
              placeholder="Search conversations..."
              class="pr-10 form-input w-full rounded-full border border-input px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary focus:outline-none bg-background text-foreground placeholder-muted-foreground"
            />
            <Search class="absolute right-7 top-1/2 -translate-y-1/2 text-muted-foreground w-5 h-5" />
          </div>
          <div class="flex-grow overflow-y-auto custom-scrollbar">
            <div v-if="loading && conversations.length === 0" class="p-4 text-center text-muted-foreground">Loading conversations...</div>
            <div v-else-if="conversations.length === 0" class="p-4 text-center text-muted-foreground">No conversations found.</div>
            <div
              v-else
              v-for="convo in conversations"
              :key="convo.id"
              @click="selectConversation(convo.id)"
              class="flex items-center gap-3 p-4 border-b border-border hover:bg-muted transition cursor-pointer"
              :class="{ 'bg-primary/10 text-primary': selectedConversation?.id === convo.id, 'text-foreground': selectedConversation?.id !== convo.id }"
            >
              <template v-if="convo.profile_photo_url">
                <img :src="convo.profile_photo_url" :alt="convo.name" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
              </template>
              <div v-else class="w-10 h-10 rounded-full bg-accent flex items-center justify-center text-accent-foreground text-sm font-medium flex-shrink-0">
                {{ convo.name.charAt(0).toUpperCase() }}
              </div>
              <div class="flex-grow overflow-hidden">
                <p class="font-medium truncate">{{ convo.name }}</p>
                <p class="text-xs text-muted-foreground truncate">{{ convo.staff?.position || 'User' }}</p>
              </div>
            </div>
          </div>
        </div>

        <div v-show="!showConversationList || currentWindowWidth >= 768"
             class="w-full md:w-2/3 flex flex-col bg-background min-h-0">
          <template v-if="loading && !selectedConversation">
            <div class="flex-grow flex items-center justify-center">
              <div class="text-center text-muted-foreground">
                <svg class="animate-spin h-8 w-8 text-primary mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2">Loading messages...</p>
              </div>
            </div>
          </template>
          <template v-else-if="!selectedConversation">
            <div class="flex-grow flex items-center justify-center p-6 text-center">
              <div class="text-muted-foreground">
                <MessageSquareText class="w-12 h-12 mx-auto mb-4 text-primary" />
                <p class="text-lg font-semibold">Select a conversation</p>
                <p class="text-sm">Choose a person from the left pane to start chatting.</p>
              </div>
            </div>
          </template>
          <template v-else>
            <div class="p-4 border-b border-border bg-card flex justify-between items-center">
              <h2 class="text-lg font-semibold">{{ selectedConversation.name }}</h2>
              <p class="text-sm text-muted-foreground hidden md:block">{{ selectedConversation.staff?.position || 'User' }}</p>
              <button v-if="currentWindowWidth < 768" @click="showChatList" class="text-muted-foreground hover:text-foreground transition-colors px-3 py-1 rounded-md border border-border">
                Back
              </button>
            </div>
            <div ref="chatContainer" class="flex-grow p-6 overflow-y-auto space-y-4 custom-scrollbar min-h-0">
              <div v-if="messages.length === 0 && loading" class="text-center text-muted-foreground py-10">Loading messages...</div>
              <div v-else-if="messages.length === 0 && !loading" class="text-center text-muted-foreground py-10">
                <p>No messages yet with {{ selectedConversation.name }}.</p>
                <p>Start the conversation!</p>
              </div>
              <div v-else
                v-for="message in messages"
                :key="message.id"
                class="flex"
                :class="[message.sender_id === authUser.id ? 'justify-end' : 'justify-start']"
              >
                <div
                  v-if="message"
                  class="max-w-[75%] rounded-xl px-4 py-2 text-sm shadow relative"
                  :class="{
                    'bg-primary text-primary-foreground self-end message-sent': message.sender_id === authUser.id,
                    'bg-muted text-muted-foreground self-start message-received': message.sender_id !== authUser.id,
                  }"
                >
                  <p class="break-words" v-if="message.message">{{ message.message }}</p>
                  <div v-if="message.attachment_url" class="mt-2 p-2 bg-white/20 rounded-md">
                      <a :href="message.attachment_url" target="_blank" download class="flex items-center gap-2 text-white hover:underline">
                          <template v-if="message.attachment_mime_type && message.attachment_mime_type.startsWith('image/')">
                            <Image class="w-5 h-5" />
                          </template>
                          <template v-else>
                            <File class="w-5 h-5" />
                          </template>
                          <span>{{ message.attachment_filename || 'Download File' }}</span>
                      </a>
                  </div>
                  <p class="text-xs mt-1 opacity-80 text-right">{{ format(new Date(message.created_at), 'p') }}</p>
                </div>
              </div>
            </div>
            <div class="p-4 border-t border-border bg-card">
              <form @submit.prevent="submit" class="flex items-center gap-3">
                <img :src="authUser.profile_photo_url || `https://ui-avatars.com/api/?name=${authUser.name}&color=7F9CF5&background=EBF4FF`" :alt="authUser.name" class="w-10 h-10 rounded-full object-cover">

                <input
                  type="file"
                  ref="attachmentInput"
                  @change="handleAttachmentChange"
                  class="hidden"
                  accept="image/*, application/pdf, .doc, .docx, .xlsx, .txt"
                />
                <button
                  type="button"
                  @click="triggerAttachmentInput"
                  class="text-muted-foreground hover:text-foreground p-2 rounded-full hover:bg-muted transition"
                  title="Attach File"
                >
                  <Paperclip class="w-5 h-5" />
                </button>
                <button
                  type="button"
                  @click="handleEmojiClick"
                  class="text-muted-foreground hover:text-foreground p-2 rounded-full hover:bg-muted transition"
                  title="Emoji"
                >
                  <Smile class="w-5 h-5" />
                </button>

                <input
                  v-model="form.message"
                  type="text"
                  placeholder="Type your message here..."
                  class="flex-grow form-input rounded-full border border-input px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary focus:outline-none bg-background text-foreground placeholder-muted-foreground"
                  autocomplete="off"
                />
                <div v-if="form.attachment" class="flex items-center text-sm bg-muted rounded-full px-3 py-1 ml-2">
                    <span class="truncate max-w-[100px]">{{ form.attachment.name }}</span>
                    <button type="button" @click="removeAttachment" class="ml-2 text-muted-foreground hover:text-foreground">
                        <X class="w-4 h-4" />
                    </button>
                </div>
                <button
                  type="submit"
                  :disabled="form.processing || (!form.message.trim() && !form.attachment) || !selectedConversation?.id"
                  class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-primary hover:bg-primary/90 text-primary-foreground disabled:opacity-40 disabled:cursor-not-allowed transition duration-150 ease-in-out"
                >
                  <Send class="w-5 h-5" />
                </button>
              </form>
            </div>
          </template>
        </div>
      </div>
      <div
        v-if="currentWindowWidth >= 768"
        @mousedown="startResize"
        class="resize-handle absolute -bottom-2 -right-2 w-8 h-8 cursor-se-resize flex items-center justify-center text-muted-foreground hover:text-foreground transition"
      >
        <GripVertical class="w-5 h-5 rotate-45" />
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Scoped styles specific to this component */
.chat-modal-widget {
  /* When currentWindowWidth < 768, the `fixed inset-0` class will apply, making it fullscreen */
  /* For larger screens, its positioning will be handled by `fixed bottom-6 right-6` from the main div */
}

/* Custom Scrollbar for nicer appearance */
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: hsl(var(--background)); /* Track matches background */
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: hsl(var(--muted)); /* Thumb is muted color */
  border-radius: 5px;
  border: 2px solid hsl(var(--background)); /* Border matches background */
  background-clip: content-box;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: hsl(var(--muted-foreground)); /* Darker on hover */
}
/* Firefox scrollbar styling */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: hsl(var(--muted)) hsl(var(--background));
}

/* Message Bubble Tail Styles (ensure these match your theme colors) */
/* The .message-bubble class itself is not used directly in the template, only for context */
/*
.message-bubble {
  position: relative;
  max-width: 75%;
}
*/

.message-sent {
  border-bottom-right-radius: 4px !important;
}

.message-received {
  border-bottom-left-radius: 4px !important;
}

/* Base tail properties */
.message-sent::after,
.message-received::after {
  content: '';
  position: absolute;
  bottom: 0; /* Align to the bottom of the message div */
  width: 0;
  height: 0;
  border: 8px solid transparent; /* Base border for triangle shape */
  z-index: -1; /* Place behind the message content */
}

.message-sent::after {
  right: -7px; /* Position to the right */
  border-bottom-color: hsl(var(--primary)); /* Match sender bubble color */
  border-right-color: hsl(var(--primary)); /* Match sender bubble color */
  border-bottom-left-radius: 4px; /* Match bubble radius */
  transform: rotate(45deg);
}

.message-received::after {
  left: -7px; /* Position to the left */
  border-bottom-color: hsl(var(--muted)); /* Match receiver bubble color */
  border-left-color: hsl(var(--muted)); /* Match receiver bubble color */
  border-bottom-right-radius: 4px; /* Match bubble radius */
  transform: rotate(-45deg);
}

/* Dark mode adjustments for tails (Tailwind's dark: variant handles hsl(var(--...)) if properly configured) */
/* If the above isn't sufficient for dark mode, you might need:
.dark .message-sent::after {
  border-bottom-color: hsl(var(--primary-dark)); // Assuming a dark mode primary variable
  border-right-color: hsl(var(--primary-dark));
}

.dark .message-received::after {
  border-bottom-color: hsl(var(--muted-dark)); // Assuming a dark mode muted variable
  border-left-color: hsl(var(--muted-dark));
}
*/
</style>