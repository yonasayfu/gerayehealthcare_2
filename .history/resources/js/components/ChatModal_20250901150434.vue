<script setup lang="ts">
import { ref, watch, nextTick, computed, onMounted, onUnmounted } from 'vue';
import debounce from 'lodash/debounce';
import { useForm, usePage } from '@inertiajs/vue3';
import { Send, X, MessageSquareText, Search, Minus, GripVertical, Paperclip, Smile, File, Image, Check, CheckCheck, Clock } from 'lucide-vue-next';
import { format } from 'date-fns';
import axios from 'axios';
import type { User, Message, Conversation, AppPageProps } from '@/types';
import EmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css';

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
const groups = ref<Group[]>([]);
const selectedConversation = ref<Conversation | null>(null);
const selectedGroup = ref<Group | null>(null);
const messages = ref<Message[]>([]);
const loading = ref<boolean>(false);
const showConversationList = ref(true);
const isCounterpartTyping = ref(false)
const isGroupsTab = ref(false)
let typingPollHandle: number | null = null

// Minimize handling (local, in addition to parent emit)
const isMinimized = ref(false)
const toggleMinimize = () => {
  isMinimized.value = true
  emit('minimize')
}
const restoreFromMinimize = () => {
  isMinimized.value = false
}

const form = useForm({
  receiver_id: null as number | null,
  message: '',
  attachment: null as File | null,
});

const attachmentInput = ref<HTMLInputElement | null>(null);
const messageInput = ref<HTMLTextAreaElement | null>(null);
const attachmentPreviewUrl = ref<string | null>(null)
const isSending = ref(false)

// Reply / Edit / Draft state
const replyTarget = ref<Message | null>(null)
const editingMessageId = ref<number | null>(null)
const editingText = ref<string>('')

// Draft persistence per conversation
const draftKey = computed(() => selectedConversation.value ? `chat:draft:${selectedConversation.value.id}` : null)
const saveDraft = () => {
  if (!draftKey.value) return
  localStorage.setItem(draftKey.value, form.message)
}
const loadDraft = () => {
  if (!draftKey.value) { form.message = ''; return }
  const d = localStorage.getItem(draftKey.value)
  form.message = d !== null ? d : ''
}

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
  form.attachment = target.files?.[0] || null;
  if (attachmentPreviewUrl.value) URL.revokeObjectURL(attachmentPreviewUrl.value)
  if (form.attachment && form.attachment.type.startsWith('image/')) {
    attachmentPreviewUrl.value = URL.createObjectURL(form.attachment)
  } else {
    attachmentPreviewUrl.value = null
  }
};

const removeAttachment = () => {
  form.attachment = null;
  if (attachmentInput.value) attachmentInput.value.value = '';
  if (attachmentPreviewUrl.value) URL.revokeObjectURL(attachmentPreviewUrl.value)
  attachmentPreviewUrl.value = null
};

// --- Emoji Handling (Fixed) ---
const showEmojiPicker = ref(false);
const handleEmojiClick = () => {
  showEmojiPicker.value = !showEmojiPicker.value;
};

const onEmojiSelect = (emoji: { i: string }) => {
  if (messageInput.value) {
    const start = messageInput.value.selectionStart || 0;
    const end = messageInput.value.selectionEnd || 0;
    const text = form.message;
    const emojiChar = emoji.i || '';
    form.message = text.substring(0, start) + emojiChar + text.substring(end);

    nextTick(() => {
      if (messageInput.value) {
        messageInput.value.selectionEnd = start + emojiChar.length;
        messageInput.value.focus();
      }
    });
  }
  showEmojiPicker.value = false;
};

// --- Fetching Logic (Fixed) ---
const fetchMessages = async (recipientId: number | null = null, searchQuery: string = '') => {
  loading.value = true;
  try {
    const response = await axios.get(route('messages.data', { recipient: recipientId, search: searchQuery }));
    let fetchedConversations: Conversation[] = response.data.conversations || [];

    conversations.value = fetchedConversations;

    // Handle selected conversation
    let newSelectedConversation = null as any;
    // Prefer the recipientId explicitly requested
    if (recipientId) {
      newSelectedConversation = fetchedConversations.find(c => c.id === recipientId) || null;
    } else {
      newSelectedConversation = response.data.selectedConversation || null;
      if (!newSelectedConversation && props.initialConversationId) {
        newSelectedConversation = fetchedConversations.find(c => c.id === props.initialConversationId) || null;
      }
      if (!newSelectedConversation && fetchedConversations.length > 0) {
        // Do not auto-select current user; pick the first non-self if available
        const userId = authUser.value?.id
        newSelectedConversation = fetchedConversations.find(c => c.id !== userId) || fetchedConversations[0];
      }
    }

    if (selectedConversation.value?.id !== newSelectedConversation?.id) {
      selectedConversation.value = newSelectedConversation;
    }

    // Ensure messages is always a valid array
    messages.value = (response.data.messages || []).filter((msg: any) => 
      msg && typeof msg === 'object' && msg.id && msg.created_at
    );

    form.receiver_id = selectedConversation.value?.id || null;
    scrollToBottom();
  } catch (error) {
    console.error('Error fetching messages:', error);
    messages.value = [];
  } finally {
    loading.value = false;
  }
};

const fetchGroups = async (searchQuery: string = '') => {
  loading.value = true;
  try {
    const response = await axios.get(route('groups.list', { search: searchQuery }));
    groups.value = response.data.data;
  } catch (error) {
    console.error('Error fetching groups:', error);
    groups.value = [];
  } finally {
    loading.value = false;
  }
};

const fetchGroupMessages = async (groupId: number) => {
  loading.value = true;
  try {
    const response = await axios.get(route('groups.messages.index', { group: groupId }));
    messages.value = response.data.data;
    scrollToBottom();
  } catch (error) {
    console.error('Error fetching group messages:', error);
    messages.value = [];
  } finally {
    loading.value = false;
  }
};

const selectConversation = (convoId: number) => {
  selectedConversation.value = conversations.value.find(c => c.id === convoId) || null;
  if (selectedConversation.value) {
    showConversationList.value = false
    fetchMessages(convoId)
  }
};

const selectGroup = (groupId: number) => {
  selectedGroup.value = groups.value.find(g => g.id === groupId) || null;
  if (selectedGroup.value) {
    showConversationList.value = false;
    fetchGroupMessages(groupId);
  }
};

// --- Watchers ---
watch(() => props.isOpen, (newVal: boolean) => {
  if (newVal) {
    if (currentWindowWidth.value < 768) showConversationList.value = true;
    if (isGroupsTab.value) {
      fetchGroups();
    } else {
      fetchMessages(props.initialConversationId || null);
    }
    startTypingPoll();
  }
});

watch(isGroupsTab, (isGroup) => {
  if (isGroup) {
    fetchGroups(search.value)
  } else {
    fetchMessages(null, search.value)
  }
})

const debouncedSearch = debounce((val:string) => {
  if (isGroupsTab.value) {
    fetchGroups(val)
  } else if (selectedConversation.value) {
    fetchMessages(selectedConversation.value.id, val)
  } else {
    fetchMessages(null, val)
  }
}, 300)

watch(search, (value: string) => {
  debouncedSearch(value)
});

const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
  });
};

watch(() => messages.value, scrollToBottom, { deep: true });

const previousSelectedConversationId = ref<number | null>(null);
watch(() => selectedConversation.value, (newVal: Conversation | null) => {
  if (!newVal) return;
  const newId = newVal.id;
  
  if (newId !== previousSelectedConversationId.value) {
    form.receiver_id = newId;
    fetchMessages(newId);
    if (currentWindowWidth.value < 768) showConversationList.value = false;
    startTypingPoll();
    // load saved draft for this conversation
    nextTick(loadDraft)
  }
  previousSelectedConversationId.value = newId;
}, { immediate: true });

// --- Message Submission (Fixed) ---
const submit = async () => {
  if (isGroupsTab.value) {
    if (!selectedGroup.value || (!form.message.trim() && !form.attachment) || isSending.value) return;

    try {
      isSending.value = true;
      const formData = new FormData();
      formData.append('message', form.message);
      if (form.attachment) formData.append('attachment', form.attachment);
      if (replyTarget.value) formData.append('reply_to_id', String(replyTarget.value.id));

      await axios.post(route('groups.messages.store', { group: selectedGroup.value.id }), formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });

      form.message = '';
      form.attachment = null;
      if (attachmentInput.value) attachmentInput.value.value = '';
      if (attachmentPreviewUrl.value) URL.revokeObjectURL(attachmentPreviewUrl.value);
      attachmentPreviewUrl.value = null;
      replyTarget.value = null;

      fetchGroupMessages(selectedGroup.value.id);
    } catch (error) {
      console.error('Error sending group message:', error);
      alert('Failed to send message. Please try again.');
    } finally {
      isSending.value = false;
      nextTick(() => messageInput.value?.focus());
      if (messageInput.value) {
        messageInput.value.style.height = 'auto';
      }
    }
  } else {
    if (!form.receiver_id || (!form.message.trim() && !form.attachment) || isSending.value) return;
    
    try {
      isSending.value = true
      // Create optimistic message
      const tempMessage: Message = {
        id: Date.now(), // Temporary ID
        sender_id: authUser.value.id,
        receiver_id: form.receiver_id,
        message: form.message,
        attachment: form.attachment ? {
          filename: form.attachment.name,
          url: URL.createObjectURL(form.attachment),
          mime_type: form.attachment.type
        } : null,
        created_at: new Date().toISOString(),
        isOptimistic: true
      };
      
      // Add to messages immediately
      messages.value = [...messages.value, tempMessage];
      scrollToBottom();
      
      // Prepare form data
      const formData = new FormData();
      formData.append('receiver_id', form.receiver_id.toString());
      formData.append('message', form.message);
      if (form.attachment) formData.append('attachment', form.attachment);
      if (replyTarget.value) formData.append('reply_to_id', String(replyTarget.value.id))
      
      // Submit to server
      await axios.post(route('messages.store'), formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      
      // Clear form fields
      form.message = '';
      form.attachment = null;
      if (attachmentInput.value) attachmentInput.value.value = '';
      if (attachmentPreviewUrl.value) URL.revokeObjectURL(attachmentPreviewUrl.value)
      attachmentPreviewUrl.value = null
      replyTarget.value = null
      // clear draft for this conversation
      if (draftKey.value) localStorage.removeItem(draftKey.value)

      // Refetch to get actual message from server
      fetchMessages(form.receiver_id);
    } catch (error) {
      console.error('Error sending message:', error);
      // Remove optimistic message on error
      messages.value = messages.value.filter(m => m.id !== tempMessage.id);
      alert('Failed to send message. Please try again.');
    } finally {
      isSending.value = false
      nextTick(() => messageInput.value?.focus())
      if (messageInput.value) {
        messageInput.value.style.height = 'auto'
      }
    }
  }
};

// --- Typing indicator ---
const sendTyping = () => {
  if (!form.receiver_id || isGroupsTab.value) return;
  // Fire-and-forget typing ping
  axios.post(route('messages.typing'), { receiver_id: form.receiver_id }).catch(() => {})
  // Save draft as user types
  saveDraft()
}

const startTypingPoll = () => {
  if (!selectedConversation.value || isGroupsTab.value) return;
  if (typingPollHandle) window.clearInterval(typingPollHandle);
  const poll = async () => {
    if (!selectedConversation.value) return;
    try {
      const res = await axios.get(route('messages.typingStatus', selectedConversation.value.id))
      isCounterpartTyping.value = !!(res.data?.typing)
    } catch (_) { /* ignore */ }
  }
  poll();
  typingPollHandle = window.setInterval(poll, 2000)
}

// Actions: reply, edit, delete
const startReply = (m: Message) => {
  replyTarget.value = m
  nextTick(() => messageInput.value?.focus())
}
const clearReply = () => { replyTarget.value = null }
const startEdit = (m: Message) => {
  editingMessageId.value = m.id
  editingText.value = m.message || ''
}
const cancelEdit = () => {
  editingMessageId.value = null
  editingText.value = ''
}
const saveEdit = async (m: Message) => {
  if (!editingMessageId.value) return
  try {
    await axios.patch(route('messages.update', m.id), { message: editingText.value })
    const idx = messages.value.findIndex(x => x.id === m.id)
    if (idx !== -1) messages.value[idx].message = editingText.value
    cancelEdit()
  } catch (e) {
    alert('Failed to edit message')
  }
}
const deleteMessage = async (m: Message) => {
  if (!confirm('Delete this message?')) return
  try {
    await axios.delete(route('messages.destroy', m.id))
    messages.value = messages.value.filter(x => x.id !== m.id)
  } catch (e) {
    alert('Failed to delete message')
  }
}

// Context menu state and handlers
const contextMenu = ref<{visible: boolean, x: number, y: number, msg: any|null, owned: boolean}>({visible: false, x: 0, y: 0, msg: null, owned: false})
const longPressTimer = ref<number | null>(null);

const onTouchStart = (event: TouchEvent, message: Message) => {
  longPressTimer.value = window.setTimeout(() => {
    openContextMenu(event, message);
  }, 500); // 500ms for long press
};

const onTouchEnd = () => {
  if (longPressTimer.value) {
    clearTimeout(longPressTimer.value);
    longPressTimer.value = null;
  }
};

const openContextMenu = (e: MouseEvent | TouchEvent, m: any) => {
  e.preventDefault();
  const owned = m.sender_id === authUser.value?.id
  const touch = e instanceof TouchEvent ? e.touches[0] || e.changedTouches[0] : e;
  contextMenu.value = { visible: true, x: touch.clientX, y: touch.clientY, msg: m, owned }
  document.addEventListener('click', closeContextMenu, { once: true })
}
const closeContextMenu = () => { contextMenu.value.visible = false; contextMenu.value.msg = null }
const onCtxReply = () => { if (contextMenu.value.msg) startReply(contextMenu.value.msg); closeContextMenu() }
const onCtxEdit = () => { if (contextMenu.value.msg) startEdit(contextMenu.value.msg); closeContextMenu() }
const onCtxDelete = () => { if (contextMenu.value.msg) deleteMessage(contextMenu.value.msg); closeContextMenu() }
const onCtxReact = (emoji: string) => {
  if (!contextMenu.value.msg) return;
  if (isGroupsTab.value) {
    axios.post(route('groups.messages.react', { group: selectedGroup.value.id, message: contextMenu.value.msg.id }), { emoji }).catch(()=>{})
  } else {
    axios.post(route('messages.react', contextMenu.value.msg.id), { emoji }).catch(()=>{})
  }
  closeContextMenu()
}

const groupedReactions = (reactions: any[]) => {
  if (!reactions) return {}
  return reactions.reduce((acc, reaction) => {
    if (!acc[reaction.emoji]) {
      acc[reaction.emoji] = []
    }
    acc[reaction.emoji].push(reaction)
    return acc
  }, {} as Record<string, any[]>)
}
</script>

<template>
  <div v-if="props.isOpen" class="fixed bottom-6 right-6 z-50 chat-modal-widget"
       :class="{ 'fixed inset-0 !bottom-0 !right-0 !top-0 !left-0': currentWindowWidth < 768 }" >
    <div
      ref="modalRef"
      class="relative bg-white/70 dark:bg-gray-900/60 backdrop-blur-xl border border-gray-200/60 dark:border-gray-700/50 text-foreground rounded-2xl shadow-2xl flex flex-col overflow-hidden transition-all duration-200 ease-in-out"
      :style="{
        width: currentWindowWidth < 768 ? '95vw' : (modalWidth + 'px'),
        height: currentWindowWidth < 768 ? '90vh' : (modalHeight + 'px'),
        maxWidth: currentWindowWidth < 768 ? '95vw' : '100%',
        maxHeight: currentWindowWidth < 768 ? '90vh' : '100%',
      }"
      v-show="!isMinimized"
    >
      <div class="p-3 border-b border-gray-200/70 dark:border-gray-700/60 flex justify-between items-center bg-transparent">
        <h2 class="text-lg font-semibold flex items-center gap-2">
          <MessageSquareText class="w-5 h-5 text-primary" /> GeraChat 
        </h2>
        <div class="flex items-center gap-2">
          <button @click="toggleMinimize" class="text-muted-foreground hover:text-foreground p-1 rounded-full hover:bg-muted transition">
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

        <!-- Conversation List -->
        <div v-show="showConversationList || currentWindowWidth >= 768"
             class="w-full md:w-1/3 flex flex-col bg-background border-r md:border-border overflow-hidden"
             :class="{ 'border-b': currentWindowWidth < 768 }">
          <div class="p-4 border-b border-gray-200/70 dark:border-gray-700/60 relative">
            <div class="flex border-b border-gray-200/70 dark:border-gray-700/60 mb-4">
              <button @click="isGroupsTab = false" class="px-4 py-2 text-sm font-medium w-1/2" :class="!isGroupsTab ? 'border-b-2 border-primary text-primary' : 'text-muted-foreground'">People</button>
              <button @click="isGroupsTab = true" class="px-4 py-2 text-sm font-medium w-1/2" :class="isGroupsTab ? 'border-b-2 border-primary text-primary' : 'text-muted-foreground'">Groups</button>
            </div>
            <input
              type="text"
              v-model="search"
              placeholder="Search conversations..."
              class="pr-10 form-input w-full rounded-full border border-input px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary focus:outline-none bg-background text-foreground placeholder-muted-foreground"
            />
            <Search class="absolute right-7 top-1/2 -translate-y-1/2 text-muted-foreground w-5 h-5" />
          </div>
          <div class="flex-grow overflow-y-auto custom-scrollbar">
            <template v-if="!isGroupsTab">
              <div v-if="loading && conversations.length === 0" class="p-4 text-center text-muted-foreground">Loading conversations...</div>
              <div v-else-if="conversations.length === 0" class="p-4 text-center text-muted-foreground">No conversations found.</div>
                <div
                  v-else
                  v-for="convo in conversations"
                  :key="convo.id"
                  @click="selectConversation(convo.id)"
                  class="flex items-center gap-3 p-4 border-b border-border hover:bg-muted transition cursor-pointer"
                  :class="{ 'bg-primary/10': selectedConversation?.id === convo.id }"
                >
                  <img 
                    v-if="convo.profile_photo_url" 
                    :src="convo.profile_photo_url" 
                    :alt="convo.name" 
                    class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                  >
                  <div v-else class="w-10 h-10 rounded-full bg-accent flex items-center justify-center text-accent-foreground text-sm font-medium flex-shrink-0">
                    {{ convo.name.charAt(0).toUpperCase() }}
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="truncate" :class="convo.unread > 0 ? 'font-semibold' : 'font-medium'">{{ convo.name }}</p>
                    <p class="text-xs text-muted-foreground truncate">{{ convo.staff?.position || 'User' }}</p>
                  </div>
                  <div v-if="convo.unread > 0" class="ml-2 inline-flex items-center justify-center rounded-full bg-indigo-600 text-white text-xs px-2 py-0.5">
                    {{ convo.unread }}
                  </div>
                </div>
            </template>
            <template v-else>
              <div v-if="loading && groups.length === 0" class="p-4 text-center text-muted-foreground">Loading groups...</div>
              <div v-else-if="groups.length === 0" class="p-4 text-center text-muted-foreground">No groups found.</div>
              <div
                v-else
                v-for="group in groups"
                :key="group.id"
                @click="selectGroup(group.id)"
                class="flex items-center gap-3 p-4 border-b border-border hover:bg-muted transition cursor-pointer"
                :class="{ 'bg-primary/10': selectedGroup?.id === group.id }"
              >
                <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center text-accent-foreground text-sm font-medium flex-shrink-0">
                  {{ group.name.charAt(0).toUpperCase() }}
                </div>
                <div class="min-w-0 flex-1">
                  <p class="truncate font-medium">{{ group.name }}</p>
                  <p class="text-xs text-muted-foreground truncate">{{ group.members_count }} members</p>
                </div>
              </div>
            </template>
            </div>
        </div>

        <!-- Chat Area -->
        <div v-show="!showConversationList || currentWindowWidth >= 768"
             class="w-full md:w-2/3 flex flex-col bg-transparent min-h-0">
          <template v-if="loading && !selectedConversation && !selectedGroup">
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
          
          <template v-else-if="!selectedConversation && !selectedGroup">
            <div class="flex-grow flex items-center justify-center p-6 text-center">
              <div class="text-muted-foreground">
                <MessageSquareText class="w-12 h-12 mx-auto mb-4 text-primary" />
                <p class="text-lg font-semibold">Select a conversation or group</p>
                <p class="text-sm">Choose a person or group from the left pane to start chatting.</p>
              </div>
            </div>
</template>
          
          <template v-else-if="selectedConversation || selectedGroup">
            <!-- Chat Header -->
            <div class="p-4 border-b border-gray-200/70 dark:border-gray-700/60 bg-transparent flex justify-between items-center">
              <h2 v-if="selectedConversation" class="text-lg font-semibold">{{ selectedConversation.name }}</h2>
              <h2 v-if="selectedGroup" class="text-lg font-semibold">{{ selectedGroup.name }}</h2>
              <div v-if="selectedConversation" class="hidden md:flex items-center gap-3 text-sm text-muted-foreground">
                <span>{{ selectedConversation.staff?.position || 'User' }}</span>
                <a :href="route('messages.export', selectedConversation.id)" class="px-2 py-1 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-accent">Download Chat</a>
              </div>
              <button 
                v-if="currentWindowWidth < 768" 
              <h2 v-if="selectedConversation" class="text-lg font-semibold">{{ selectedConversation.name }}</h2>
              <h2 v-if="selectedGroup" class="text-lg font-semibold">{{ selectedGroup.name }}</h2>
              <div v-if="selectedConversation" class="hidden md:flex items-center gap-3 text-sm text-muted-foreground">
                <span>{{ selectedConversation.staff?.position || 'User' }}</span>
                <a :href="route('messages.export', selectedConversation.id)" class="px-2 py-1 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-accent">Download Chat</a>
              </div>
              <button 
                v-if="currentWindowWidth < 768" 
                @click="showConversationList = true" 
                class="text-muted-foreground hover:text-foreground transition-colors px-3 py-1 rounded-md border border-border"
              >
                Back
              </button>
            </div>
            
            <!-- Messages Area -->
            <div ref="chatContainer" class="flex-grow p-6 overflow-y-auto space-y-4 custom-scrollbar min-h-0">
              <div v-if="isCounterpartTyping && !isGroupsTab" class="flex items-center gap-2 text-sm text-muted-foreground mb-1">
                <span class="inline-flex w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                <span>{{ selectedConversation.name }} is typing...</span>
              </div>
              <div v-if="messages.length === 0 && loading" class="text-center text-muted-foreground py-10">Loading messages...</div>
              <div v-else-if="messages.length === 0 && !loading" class="text-center text-muted-foreground py-10">
                <p v-if="selectedConversation">No messages yet with {{ selectedConversation.name }}.</p>
                <p v-if="selectedGroup">No messages yet in {{ selectedGroup.name }}.</p>
                <p>Start the conversation!</p>
              </div>
              
              <!-- Messages -->
              <div 
                v-for="message in messages"
                :key="message.id"
                class="flex"
                :class="[message.sender_id === authUser.id ? 'justify-end' : 'justify-start']"
              >
                <div
                  @contextmenu.prevent="openContextMenu($event, message)"
                  @touchstart="onTouchStart($event, message)"
                  @touchend="onTouchEnd"
                  @touchcancel="onTouchEnd"
                  class="group max-w-[75%] rounded-xl px-4 py-2 text-sm shadow relative"
                  :class="{
                    'bg-blue-500 text-white self-end': message.sender_id === authUser.id,
                    'bg-gray-200 text-gray-800 self-start': message.sender_id !== authUser.id,
                    'opacity-80': message.isOptimistic
                  }"
                >
                  <!-- Reply preview (quoted) -->
                  <div v-if="message.reply_to_id" class="mb-2 pl-2 border-l-2"
                    :class="message.sender_id === authUser.id ? 'border-white/50 text-white/80' : 'border-gray-500 text-gray-700'">
                    <p class="text-xs truncate">Replying to #{{ message.reply_to_id }}</p>
                  </div>

                  <!-- Edit mode -->
                  <template v-if="editingMessageId === message.id">
                    <textarea v-model="editingText" class="w-full rounded-md p-2 text-sm text-gray-900" rows="2"></textarea>
                    <div class="flex justify-end gap-2 mt-2">
                      <button class="text-xs px-2 py-1 rounded bg-gray-200" @click="cancelEdit">Cancel</button>
                      <button class="text-xs px-2 py-1 rounded bg-indigo-600 text-white" @click="saveEdit(message)">Save</button>
                    </div>
</template>
                  <template v-else>
                  <p class="break-words" v-if="message.message">{{ message.message }}</p>
                  <div v-if="message.attachment || message.attachment_url" class="mt-2 p-2 bg-white/20 rounded-md">
                      <a 
                        :href="message.attachment_url || (message.attachment && message.attachment.url)" 
                        target="_blank" 
                        download 
                        class="flex items-center gap-2 hover:underline"
                        :class="message.sender_id === authUser.id ? 'text-white' : 'text-gray-700'"
                      >
                          <template v-if="(message.attachment_mime_type && message.attachment_mime_type.startsWith('image/')) || 
                                         (message.attachment && message.attachment.mime_type?.startsWith('image/'))">
                            <Image class="w-5 h-5" />
</template>
                          <template v-else>
                            <File class="w-5 h-5" />
</template>
                          <span>{{ (message.attachment_filename || (message.attachment && message.attachment.filename)) || 'Download File' }}</span>
                      </a>
                  </div>
</template>
                  <div class="flex items-center justify-end gap-1 mt-1">
                    <span 
                      class="text-[11px]"
                      :class="message.sender_id === authUser.id ? 'text-blue-100' : 'text-gray-500'"
                    >
                      {{ format(new Date(message.created_at), 'p') }}
                    </span>
                    <template v-if="message.sender_id === authUser.id">
                      <Clock v-if="message.isOptimistic" class="w-3.5 h-3.5 text-blue-100" />
                      <CheckCheck v-else-if="message.read_at" class="w-3.5 h-3.5 text-blue-100" title="Read" />
                      <Check v-else class="w-3.5 h-3.5 text-blue-100" title="Sent" />
</template>
                  </div>

                  <!-- Actions menu (ellipsis on hover) -->
                  <div class="absolute top-1 opacity-0 group-hover:opacity-100 transition" :class="message.sender_id === authUser.id ? 'left-1' : 'right-1'">
                    <button
                      class="text-xs px-2 py-0.5 rounded border border-gray-300/70 dark:border-gray-600/70 bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-gray-100 shadow"
                      title="More"
                      @click.stop="openContextMenu($event, message)"
                    >
                      •••
                    </button>
                  </div>

                  <!-- Reactions -->
                  <div v-if="message.reactions && message.reactions.length > 0" class="absolute -bottom-3 right-2 flex items-center gap-1">
                    <div v-for="(group, emoji) in groupedReactions(message.reactions)" :key="emoji" class="flex items-center bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full px-1.5 py-0.5 text-xs shadow-sm">
                      <span>{{ emoji }}</span>
                      <span class="ml-1 text-gray-600 dark:text-gray-300 text-[10px]">{{ group.length }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Message Input Area -->
            <div class="p-4 border-t border-gray-200/70 dark:border-gray-700/60 bg-transparent relative">
              <div v-if="showEmojiPicker" class="absolute bottom-full right-0 mb-2 z-10 shadow-lg rounded-lg overflow-hidden">
                <EmojiPicker :native="true" @select="onEmojiSelect" />
              </div>

              <!-- Reply target chip -->
              <div v-if="replyTarget" class="absolute -top-7 left-4 right-4 text-xs flex items-center justify-between bg-accent/40 px-2 py-1 rounded">
                <span>Replying to: {{ replyTarget.message?.slice(0,40) || 'Attachment' }}</span>
                <button class="text-muted-foreground hover:text-foreground" @click="clearReply"><X class="w-3 h-3" /></button>
              </div>

              <form @submit.prevent="submit" class="flex items-end gap-2">
                <img 
                  :src="authUser.profile_photo_url || `https://ui-avatars.com/api/?name=${authUser.name}&background=random`" 
                  :alt="authUser.name" 
                  class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                >

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

                <div v-if="form.attachment" class="flex items-center gap-3 flex-shrink-0 max-w-[220px]">
                  <div v-if="attachmentPreviewUrl" class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                    <img :src="attachmentPreviewUrl" alt="preview" class="w-full h-full object-cover" />
                  </div>
                  <div v-else class="text-xs bg-muted rounded-full px-3 py-1 overflow-hidden whitespace-nowrap text-ellipsis">
                    {{ form.attachment.name }}
                  </div>
                  <button type="button" @click="removeAttachment" class="text-muted-foreground hover:text-foreground">
                    <X class="w-4 h-4" />
                  </button>
                </div>

                <textarea
                  ref="messageInput"
                  v-model="form.message"
                  placeholder="Type a message… (Enter to send, Shift+Enter for newline)"
                  class="w-full flex-grow resize-none rounded-xl border border-input px-4 py-2.5 text-sm leading-5 shadow-sm focus:ring-2 focus:ring-primary focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder:text-gray-500 dark:placeholder:text-gray-400 min-h-[44px] max-h-[140px] overflow-y-auto"
                  rows="1"
                  @input="() => { sendTyping(); if (messageInput.value) { messageInput.value.style.height = 'auto'; messageInput.value.style.height = Math.min(messageInput.value.scrollHeight, 140) + 'px'; } }"
                  @keydown.enter.prevent="!$event.shiftKey ? submit() : null"
                />
                
                <button
                  type="submit"
                  :disabled="form.processing || (!form.message.trim() && !form.attachment) || (!selectedConversation?.id && !selectedGroup?.id)"
                  class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-primary hover:bg-primary/90 text-primary-foreground disabled:opacity-40 disabled:cursor-not-allowed transition duration-150 ease-in-out flex-shrink-0"
                >
                  <svg v-if="isSending" class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  <Send v-else class="w-5 h-5" />
                </button>
              </form>
            </div>
</template>
        </div>
      </div>
      
      <!-- Resize Handle -->
      <div
        v-if="currentWindowWidth >= 768"
        @mousedown="startResize"
        class="resize-handle absolute -bottom-2 -right-2 w-8 h-8 cursor-se-resize flex items-center justify-center text-muted-foreground hover:text-foreground transition"
      >
        <GripVertical class="w-5 h-5 rotate-45" />
      </div>
      <!-- Minimized pill -->
    </div>
    <button
      v-if="isMinimized && currentWindowWidth >= 768"
      @click="restoreFromMinimize"
      class="mt-2 ml-auto flex items-center gap-2 rounded-full px-4 py-2 bg-white/80 dark:bg-gray-900/70 backdrop-blur border border-gray-200/60 dark:border-gray-700/60 shadow hover:shadow-md transition"
    >
      <MessageSquareText class="w-4 h-4 text-primary" />
      <span class="text-sm">Open Chat</span>
    </button>
  </div>

  
  
  <!-- Context Menu (teleport to body to avoid z-index/overflow issues) -->
  <teleport to="body">
    <div v-if="contextMenu.visible"
         :style="{ top: contextMenu.y + 'px', left: contextMenu.x + 'px' }"
         class="fixed z-[2147483647] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-md text-sm">
      <button class="block w-full text-left px-3 py-2 hover:bg-accent" @click="onCtxReply">Reply</button>
      <button v-if="contextMenu.owned && !isGroupsTab" class="block w-full text-left px-3 py-2 hover:bg-accent" @click="onCtxEdit">Edit</button>
      <button v-if="contextMenu.owned && !isGroupsTab" class="block w-full text-left px-3 py-2 hover:bg-accent" @click="onCtxDelete">Delete</button>
      <div class="px-3 py-2 border-t border-gray-200 dark:border-gray-700 flex gap-2">
        <button class="hover:scale-110" @click="onCtxReact('👍')">👍</button>
        <button class="hover:scale-110" @click="onCtxReact('❤️')">❤️</button>
        <button class="hover:scale-110" @click="onCtxReact('😊')">😊</button>
      </div>
    </div>
  </teleport>
</template>

<style scoped>
.chat-modal-widget {
  z-index: 9999;
}

.resize-handle {
  z-index: 10000;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: hsl(var(--background));
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: hsl(var(--muted));
  border-radius: 5px;
  border: 2px solid hsl(var(--background));
  background-clip: content-box;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: hsl(var(--muted-foreground));
}
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: hsl(var(--muted)) hsl(var(--background));
}

/* Message bubble styling */
.bg-blue-500 {
  background-color: #3b82f6;
}

.bg-gray-200 {
  background-color: #e5e7eb;
}
</style>
