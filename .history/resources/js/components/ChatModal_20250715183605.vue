<script setup lang="ts">
import { ref, watch, nextTick, computed, onMounted, onUnmounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Send, X, MessageSquareText, Search, Minus, GripVertical, Paperclip, Smile, File, Image } from 'lucide-vue-next';
import { format } from 'date-fns';
import axios from 'axios';
import type { User, Message, Conversation, AppPageProps } from '@/types';
// Import Emoji Picker
import EmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css';

console.log('GeraChat component script loaded. (Fixing current issues)');

const props = defineProps<{
  isOpen: boolean;
  initialConversationId?: number | null;
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
const showConversationList = ref(true);

const form = useForm({
  receiver_id: null as number | null,
  message: '',
  attachment: null as File | null,
});

const attachmentInput = ref<HTMLInputElement | null>(null);
const messageInput = ref<HTMLInputElement | null>); // Ref for message input for emoji insertion

// --- Window Width for Responsiveness ---
const currentWindowWidth = ref(0);

const updateWindowWidth = () => {
  currentWindowWidth.value = window.innerWidth;
};

onMounted(() => {
  updateWindowWidth();
  window.addEventListener('resize', updateWindowWidth);

  modalWidth.value = currentWindowWidth.value > 1024 ? 800 : Math.min(currentWindowWidth.value - 40, 600);
  modalHeight.value = currentWindowWidth.value > 768 ? 600 : Math.min(currentWindowWidth.value - 80, 500);
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
    attachmentInput.value.value = '';
  }
};

// --- Emoji Handling ---
const showEmojiPicker = ref(false);
const handleEmojiClick = () => {
  showEmojiPicker.value = !showEmojiPicker.value;
};

const onEmojiSelect = (emoji: { native: string }) => { // Changed type to emoji.native
  if (messageInput.value) {
    const start = messageInput.value.selectionStart || 0;
    const end = messageInput.value.selectionEnd || 0;
    const text = form.message;
    form.message = text.substring(0, start) + emoji.native + text.substring(end); // Used emoji.native

    nextTick(() => {
      if (messageInput.value) {
        messageInput.value.selectionEnd = start + emoji.native.length;
        messageInput.value.focus();
      }
    });
  }
  showEmojiPicker.value = false;
};

// --- Fetching Logic ---
const fetchMessages = async (recipientId: number | null = null, searchQuery: string = '') => {
  loading.value = true;
  console.log(`[fetchMessages] Initiated for recipientId: ${recipientId}, search: '${searchQuery}'`);
  try {
    const response = await axios.get(route('messages.data', { recipient: recipientId, search: searchQuery }));
    let fetchedConversations: Conversation[] = response.data.conversations;

    const user = authUser.value;
    if (user && !fetchedConversations.some((c: Conversation) => c.id === user.id)) {
      fetchedConversations = [{
        id: user.id,
        name: user.name,
        email: user.email,
        profile_photo_url: user.profile_photo_url,
        staff: user.staff || null
      }, ...fetchedConversations];
    }
    conversations.value = fetchedConversations;
    console.log('[fetchMessages] Fetched Conversations:', conversations.value);

    let newSelectedConversation: Conversation | null = response.data.selectedConversation;

    if (!newSelectedConversation) {
        if (props.initialConversationId) {
            newSelectedConversation = fetchedConversations.find(c => c.id === props.initialConversationId) || null;
        }
        if (!newSelectedConversation && fetchedConversations.length > 0) {
            newSelectedConversation = fetchedConversations[0];
        }
    }

    if (selectedConversation.value?.id !== newSelectedConversation?.id) {
        selectedConversation.value = newSelectedConversation;
        console.log('[fetchMessages] Selected conversation changed to:', selectedConversation.value?.name);
    } else if (!selectedConversation.value && newSelectedConversation) {
        selectedConversation.value = newSelectedConversation;
        console.log('[fetchMessages] Selected conversation initialized to:', selectedConversation.value?.name);
    }

    const cleanedMessages = (response.data.messages || []).filter(msg => msg !== null && msg !== undefined);
    messages.value = cleanedMessages;
    console.log('[fetchMessages] Fetched Messages (cleaned):', messages.value);


    form.receiver_id = selectedConversation.value?.id || null;
    if (selectedConversation.value) scrollToBottom();
  } catch (error) {
    console.error('[fetchMessages] Error fetching messages:', error);
  } finally {
    loading.value = false;
    console.log('[fetchMessages] Loading state set to false.');
  }
};

// --- Watchers ---
watch(() => props.isOpen, (newVal: boolean) => {
  console.log('[Watcher: isOpen] New value:', newVal);
  if (newVal) {
    if (currentWindowWidth.value < 768) {
      showConversationList.value = true;
    }
    fetchMessages(props.initialConversationId || null);
  }
});

watch(search, (value: string) => {
  console.log('[Watcher: search] New value:', value);
  if (selectedConversation.value) {
    fetchMessages(selectedConversation.value.id, value);
  } else {
    fetchMessages(null, value);
  }
});

const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
      console.log('[scrollToBottom] Scrolled.');
    }
  });
};

watch(() => messages.value, () => {
  console.log('[Watcher: messages] Messages array updated. Triggering scroll.');
  scrollToBottom();
}, { deep: true });

const previousSelectedConversationId = ref<number | null>(null);
watch(() => selectedConversation.value, (newVal: Conversation | null) => {
    const newId = newVal?.id || null;
    console.log(`[Watcher: selectedConversation] Changed. New ID: ${newId}, Previous ID: ${previousSelectedConversationId.value}`);

    if (newId !== previousSelectedConversationId.value) {
        form.receiver_id = newId;
        scrollToBottom();
        if (newId) {
            console.log(`[Watcher: selectedConversation] Fetching messages for new convo ID: ${newId}`);
            fetchMessages(newId);
            if (currentWindowWidth.value < 768) {
                showConversationList.value = false;
            }
        } else {
            console.log('[Watcher: selectedConversation] No conversation selected, clearing messages.');
            messages.value = [];
        }
    }
    previousSelectedConversationId.value = newId;
}, { immediate: true });


const submit = async () => {
  if (!form.receiver_id || (!form.message.trim() && !form.attachment)) {
    console.warn('Attempted to submit empty message or no receiver_id, and no attachment.');
    return;
  }
  console.log('[submit] Sending message...');
  try {
    form.post(route('messages.store'), {
      preserveScroll: true,
      forceFormData: true,
      onSuccess: (page) => { // Access page object from onSuccess callback
        console.log('[submit] Message sent successfully. Resetting form and re-fetching.');
        console.log('[submit] onSuccess page object:', page); // Inspect page for flash messages etc.

        form.reset('message', 'attachment');
        if (attachmentInput.value) {
          attachmentInput.value.value = '';
        }
        if (selectedConversation.value) {
          console.log('[submit] Re-fetching messages after send for conversation:', selectedConversation.value.id);
          fetchMessages(selectedConversation.value.id);
        } else {
          console.warn('[submit] No selected conversation after sending message, cannot re-fetch.');
        }
      },
      onError: (errors) => {
        console.error('[submit] Error sending message:', errors);
        alert('Failed to send message or upload file. Please check file size/type. ' + JSON.stringify(errors));
      },
      onFinish: () => {
        console.log('[submit] Form submission finished.');
      }
    });
  } catch (error) {
    console.error('[submit] Axios or Inertia post error:', error);
  }
};

const selectConversation = (convoId: number) => {
  console.log('Conversation selected by click. Convo ID:', convoId);
  selectedConversation.value = conversations.value.find(c => c.id === convoId) || null;
};

const showChatList = () => {
  console.log('Showing conversation list (mobile back button).');
  showConversationList.value = true;
};
</script>

<template>
  <div v-if="props.isOpen" class="fixed bottom-6 right-6 z-50 chat-modal-widget"
       :class="{ 'fixed inset-0 !bottom-0 !right-0 !top-0 !left-0': currentWindowWidth < 768 }" >
    <div
      ref="modalRef"
      class="relative bg-card text-foreground rounded-xl shadow-2xl flex flex-col overflow-hidden transition-all duration-200 ease-in-out"
      :style="{
        width: currentWindowWidth < 768 ? '95vw' : (modalWidth + 'px'),
        height: currentWindowWidth < 768 ? '90vh' : (modalHeight + 'px'),
        maxWidth: currentWindowWidth < 768 ? '95vw' : '100%',
        maxHeight: currentWindowWidth < 768 ? '90vh' : '100%',
      }"
    >
      <div class="p-3 border-b border-border flex justify-between items-center bg-card">
        <h2 class="text-lg font-semibold flex items-center gap-2">
          <MessageSquareText class="w-5 h-5 text-primary" /> GeraChat </h2>
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
              <div>
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
                v-for="(message, index) in messages"
                :key="message?.id || index"
                v-if="message && typeof message === 'object'"
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
            <div class="p-4 border-t border-border bg-card relative">
              <div v-if="showEmojiPicker" class="absolute bottom-full right-0 mb-2 z-10 shadow-lg rounded-lg overflow-hidden">
                <EmojiPicker :native="true" @select="onEmojiSelect" />
              </div>

              <form @submit.prevent="submit" class="flex items-end gap-2">
                <img :src="authUser.profile_photo_url || `https://ui-avatars.com/api/?name=${authUser.name}&color=7F9CF5&background=EBF4FF`" :alt="authUser.name" class="w-10 h-10 rounded-full object-cover flex-shrink-0">

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
                  class="text-muted-foreground hover:text-foreground p-2 rounded-full hover:bg-muted transition flex-shrink-0"
                  title="Attach File"
                >
                  <Paperclip class="w-5 h-5" />
                </button>
                <button
                  type="button"
                  @click="handleEmojiClick"
                  class="text-muted-foreground hover:text-foreground p-2 rounded-full hover:bg-muted transition flex-shrink-0"
                  title="Emoji"
                >
                  <Smile class="w-5 h-5" />
                </button>

                <div v-if="form.attachment" class="flex items-center text-sm bg-muted rounded-full px-3 py-1 flex-shrink-0 max-w-[120px] overflow-hidden whitespace-nowrap text-ellipsis">
                    <span class="truncate max-w-[80px]">{{ form.attachment.name }}</span>
                    <button type="button" @click="removeAttachment" class="ml-2 text-muted-foreground hover:text-foreground">
                        <X class="w-4 h-4" />
                    </button>
                </div>

                <input
                  ref="messageInput"
                  v-model="form.message"
                  type="text"
                  placeholder="Type your message here..."
                  class="flex-grow form-input rounded-full border border-input px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary focus:outline-none bg-background text-foreground placeholder-muted-foreground"
                  autocomplete="off"
                />
                
                <button
                  type="submit"
                  :disabled="form.processing || (!form.message.trim() && !form.attachment) || !selectedConversation?.id"
                  class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-primary hover:bg-primary/90 text-primary-foreground disabled:opacity-40 disabled:cursor-not-allowed transition duration-150 ease-in-out flex-shrink-0"
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