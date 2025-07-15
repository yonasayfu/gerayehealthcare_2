<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Send, X, MessageSquareText, Search, Minus, GripVertical, Paperclip, Smile, File, Image } from 'lucide-vue-next'; // Added File, Image for attachments
import { format } from 'date-fns';
import axios from 'axios';
import type { User, Message, Conversation, AppPageProps } from '@/types';

// --- DEBUG: Log when component script is loaded ---
console.log('ChatModal component script loaded. (New UI version with Attachments, Resize, Responsiveness)');

const props = defineProps<{
  isOpen: boolean;
  initialConversationId?: number | null; // New prop
}>();

const emit = defineEmits(['close', 'minimize']);

const page = usePage<AppPageProps>();
const authUser = computed<User>(() => page.props.auth.user);

const chatContainer = ref<HTMLElement | null>(null);
const search = ref<string>('');
const conversations = ref<Conversation[]>([]);
const selectedConversation = ref<Conversation | null>(null);
const messages = ref<Message[]>([]);
const loading = ref<boolean>(false);
const showConversationList = ref(true); // New: for mobile responsiveness

const form = useForm({
  receiver_id: null as number | null,
  message: '',
  attachment: null as File | null, // New: for file input
});

const attachmentInput = ref<HTMLInputElement | null>(null); // Ref for hidden file input

// --- Resizing Logic Fix ---
const modalRef = ref<HTMLElement | null>(null); // Ref for the modal container
const modalWidth = ref(window.innerWidth > 1024 ? 800 : Math.min(window.innerWidth - 40, 600)); // Default larger width
const modalHeight = ref(window.innerHeight > 768 ? 600 : Math.min(window.innerHeight - 80, 500)); // Default larger height
let startX = 0, startY = 0, startWidth = 0, startHeight = 0;

const startResize = (e: MouseEvent) => {
  e.preventDefault(); // Prevent default browser drag behavior
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

  // Set min/max constraints
  const minModalWidth = window.innerWidth < 768 ? window.innerWidth * 0.9 : 500; // More flexible on mobile
  const maxModalWidth = window.innerWidth * 0.95; // Don't exceed screen width
  const minModalHeight = window.innerHeight < 768 ? window.innerHeight * 0.8 : 400; // More flexible on mobile
  const maxModalHeight = window.innerHeight * 0.95;

  modalWidth.value = Math.min(maxModalWidth, Math.max(minModalWidth, startWidth + dx));
  modalHeight.value = Math.min(maxModalHeight, Math.max(minModalHeight, startHeight + dy));
};

const onMouseUp = () => {
  isResizing.value = false;
  document.removeEventListener('mousemove', onMouseMove);
  document.removeEventListener('mouseup', onMouseUp);
};

// --- Attachment Handling ---
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
  // In a real implementation, you'd show an emoji picker component
  // and insert the selected emoji into form.message
};

// --- Fetching Logic ---
const fetchMessages = async (recipientId: number | null = null, searchQuery: string = '') => {
  loading.value = true;
  try {
    const response = await axios.get(route('messages.data', { recipient: recipientId, search: searchQuery }));
    let fetchedConversations: Conversation[] = response.data.conversations;

    // Add current user to the top of conversations if not present for "self-chat"
    const user = authUser.value;
    if (user && !fetchedConversations.some((c: Conversation) => c.id === user.id)) {
      // Create a simplified user object for the conversation list
      fetchedConversations = [{
        id: user.id,
        name: user.name,
        email: user.email,
        profile_photo_url: user.profile_photo_url,
        staff: user.staff || null // Ensure staff is included if user has it
      }, ...fetchedConversations];
    }
    conversations.value = fetchedConversations;

    let newSelectedConversation: Conversation | null = response.data.selectedConversation;

    // Logic for initial selection if no recipient was provided or selected by backend
    if (!newSelectedConversation) {
        // If an initialConversationId was passed as prop and it's in the list
        if (props.initialConversationId) {
            newSelectedConversation = fetchedConversations.find(c => c.id === props.initialConversationId) || null;
        }
        // If still no selected conversation, default to the first in the list (could be self-chat)
        if (!newSelectedConversation && fetchedConversations.length > 0) {
            newSelectedConversation = fetchedConversations[0];
        }
    }

    selectedConversation.value = newSelectedConversation;
    messages.value = response.data.messages || []; // Ensure it's an array

    form.receiver_id = selectedConversation.value?.id || null;
    if (selectedConversation.value) scrollToBottom();
  } catch (error) {
    console.error('Error fetching messages:', error);
    // Optionally display an error message to the user
  } finally {
    loading.value = false;
  }
};

// --- Watchers ---
watch(() => props.isOpen, (newVal: boolean) => {
  if (newVal) {
    // On open, ensure conversation list is visible if on mobile, and fetch data
    if (window.innerWidth < 768) { // Assuming 768px is breakpoint for mobile
      showConversationList.value = true;
    }
    fetchMessages(props.initialConversationId || null);
  }
});

watch(search, (value: string) => {
  fetchMessages(selectedConversation.value?.id, value); // Re-fetch current convo with search query
});

const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
  });
};

watch(() => messages.value, scrollToBottom, { deep: true });
watch(() => selectedConversation.value, (newVal: Conversation | null) => {
  form.receiver_id = newVal?.id || null;
  if (newVal) {
    fetchMessages(newVal.id); // Re-fetch messages when conversation changes
    if (window.innerWidth < 768) {
      showConversationList.value = false; // Hide list, show chat on mobile
    }
  } else {
    messages.value = []; // Clear messages if no conversation selected
  }
});

// --- Submission Logic ---
const submit = async () => {
  if (!form.receiver_id || (!form.message.trim() && !form.attachment)) {
    console.warn('Attempted to submit empty message or no receiver_id, and no attachment.');
    return;
  }

  // Use Inertia's form.post with file upload
  form.post(route('messages.store'), {
    preserveScroll: true,
    forceFormData: true, // Important for file uploads with Inertia v1
    onSuccess: () => {
      form.reset('message', 'attachment'); // Reset both message and attachment
      if (attachmentInput.value) {
        attachmentInput.value.value = ''; // Clear file input manually
      }
      if (selectedConversation.value) {
        fetchMessages(selectedConversation.value.id); // Re-fetch messages
      }
    },
    onError: (errors) => {
      console.error('Error sending message:', errors);
      // Display errors to user, e.g., via a flash message or inline form errors
      alert('Failed to send message or upload file. Please check file size/type. ' + JSON.stringify(errors));
    },
    onFinish: () => {
      // Any final cleanup after success/error
    }
  });
};

// --- Responsive Toggles ---
const showChatList = () => {
  showConversationList.value = true;
};

</script>

<template>
  <div v-if="props.isOpen" class="fixed bottom-6 right-6 z-50 chat-modal-widget"
       :class="{ 'fixed inset-0': window.innerWidth < 768 }" > <div
      ref="modalRef"
      class="relative bg-card text-foreground rounded-xl shadow-2xl flex flex-col overflow-hidden transition-all duration-200 ease-in-out"
      :style="{
        width: window.innerWidth < 768 ? '95vw' : (modalWidth + 'px'),
        height: window.innerWidth < 768 ? '90vh' : (modalHeight + 'px'),
        maxWidth: window.innerWidth < 768 ? '95vw' : '100%', /* Max width on mobile */
        maxHeight: window.innerWidth < 768 ? '90vh' : '100%', /* Max height on mobile */
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
             'flex-col': window.innerWidth < 768, /* Stack vertically on small screens */
             'md:flex-row': window.innerWidth >= 768 /* Side-by-side on medium+ */
           }">

        <div v-show="showConversationList || window.innerWidth >= 768"
             class="w-full md:w-1/3 flex flex-col bg-background border-r md:border-border overflow-hidden"
             :class="{ 'border-b': window.innerWidth < 768 }"> <div class="p-4 border-b border-border relative">
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
              <div>
                <p class="font-medium truncate">{{ convo.name }}</p>
                <p class="text-xs text-muted-foreground truncate">{{ convo.staff?.position || 'User' }}</p>
              </div>
            </div>
          </div>
        </div>

        <div v-show="!showConversationList || window.innerWidth >= 768"
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
              <button v-if="window.innerWidth < 768" @click="showChatList" class="text-muted-foreground hover:text-foreground transition-colors px-3 py-1 rounded-md border border-border">
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
        v-if="window.innerWidth >= 768"
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
  /* No fixed positioning or sizing here, handled by responsive classes in template */
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
/* Adjust these values to match your theme's primary and muted/gray colors */
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
  border-bottom-color: hsl(var(--primary)); /* Match sender bubble color */
  border-right-color: hsl(var(--primary)); /* Match sender bubble color */
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
  border-bottom-color: hsl(var(--muted)); /* Match receiver bubble color */
  border-left-color: hsl(var(--muted)); /* Match receiver bubble color */
  border-bottom-right-radius: 4px; /* Match bubble radius */
  transform: rotate(-45deg);
  z-index: -1;
}

/* Dark mode adjustments for tails */
.dark .message-sent::after {
  border-bottom-color: hsl(var(--primary));
  border-right-color: hsl(var(--primary));
}

.dark .message-received::after {
  border-bottom-color: hsl(var(--muted));
  border-left-color: hsl(var(--muted));
}
</style>