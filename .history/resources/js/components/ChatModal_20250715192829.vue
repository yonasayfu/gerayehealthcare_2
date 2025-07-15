<script setup lang="ts">
import { ref, watch, nextTick, computed, onMounted, onUnmounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { Send, X, MessageSquareText, Search, Minus, GripVertical, Paperclip, Smile, File, Image, Bell, BellOff, Pin } from 'lucide-vue-next';
import { format } from 'date-fns';
import axios from 'axios';
import type { User, Message, Conversation, AppPageProps } from '@/types';
import EmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css';

const props = defineProps<{
  isOpen: boolean;
  initialConversationId?: number | null;
  newMessageNotification?: boolean;
}>();

const emit = defineEmits(['close', 'minimize', 'new-message', 'notification-toggle']);

const page = usePage<AppPageProps>();
const authUser = computed<User>(() => page.props.auth.user);

// --- Notification Settings ---
const notificationsEnabled = ref(localStorage.getItem('chatNotifications') !== 'false');
const toggleNotifications = () => {
  notificationsEnabled.value = !notificationsEnabled.value;
  localStorage.setItem('chatNotifications', notificationsEnabled.value.toString());
  emit('notification-toggle', notificationsEnabled.value);
};

// --- Pinned Messages ---
const pinnedMessages = ref<Message[]>([]);
const showPinnedMessages = ref(false);

// --- Fetch Pinned Messages ---
const fetchPinnedMessages = async () => {
  try {
    const response = await axios.get(route('messages.pinned'));
    pinnedMessages.value = response.data;
  } catch (error) {
    console.error('Error fetching pinned messages:', error);
  }
};

// --- Pin/Unpin Message ---
const togglePinMessage = async (message: Message) => {
  try {
    await axios.post(route('messages.toggle-pin', { message_id: message.id }));
    
    if (message.is_pinned) {
      pinnedMessages.value = pinnedMessages.value.filter(m => m.id !== message.id);
    } else {
      pinnedMessages.value = [...pinnedMessages.value, { ...message, is_pinned: true }];
    }
    
    message.is_pinned = !message.is_pinned;
  } catch (error) {
    console.error('Error toggling pin:', error);
  }
};

// --- Existing code remains the same until the watchers section ---

// --- Watchers ---
watch(() => props.isOpen, (newVal: boolean) => {
  if (newVal) {
    fetchPinnedMessages();
    if (currentWindowWidth.value < 768) {
      showConversationList.value = true;
    }
    fetchMessages(props.initialConversationId || null);
  }
});

// --- Notification Sound ---
const playNotificationSound = () => {
  if (!notificationsEnabled.value) return;
  
  try {
    const audio = new Audio('/notification.mp3');
    audio.volume = 0.3;
    audio.play().catch(e => console.log('Audio play failed:', e));
  } catch (e) {
    console.error('Notification sound error:', e);
  }
};

// --- In submit function ---
const submit = async () => {
  // ... existing code ...
  
  try {
    // ... existing optimistic UI code ...
    
    // Submit to server
    const response = await axios.post(route('messages.store'), formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    // Add mention notifications
    if (form.message.includes('@')) {
      const mentionedUsernames = form.message.match(/@(\w+)/g) || [];
      for (const username of mentionedUsernames) {
        const cleanUsername = username.replace('@', '');
        // In real app, this would send a notification to the mentioned user
        console.log(`Mentioned: ${cleanUsername}`);
      }
    }
    
    // ... existing code ...
    
  } catch (error) {
    // ... error handling ...
  }
};

// --- In fetchMessages function ---
const fetchMessages = async (recipientId: number | null = null, searchQuery: string = '') => {
  // ... existing code ...
  
  try {
    const response = await axios.get(route('messages.data', { recipient: recipientId, search: searchQuery }));
    
    // ... existing conversation handling ...
    
    // Check for new messages to trigger notification
    if (!props.isOpen && messages.value.length > 0 && response.data.messages.length > messages.value.length) {
      const newMessages = response.data.messages.slice(messages.value.length);
      if (newMessages.some(msg => msg.sender_id !== authUser.value.id)) {
        playNotificationSound();
        emit('new-message');
      }
    }
    
    // ... existing message handling ...
    
  } catch (error) {
    // ... error handling ...
  }
};

// --- Mention Handling ---
const mentionUsers = ref<Conversation[]>([]);
const showMentionList = ref(false);
const mentionSearch = ref('');
const mentionPosition = ref(0);

const checkForMention = (e: KeyboardEvent) => {
  if (e.key === '@') {
    mentionPosition.value = messageInput.value?.selectionStart || 0;
    mentionUsers.value = conversations.value;
    showMentionList.value = true;
  }
};

const selectMention = (user: Conversation) => {
  if (!messageInput.value) return;
  
  const currentText = form.message;
  const textBefore = currentText.substring(0, mentionPosition.value);
  const textAfter = currentText.substring(mentionPosition.value);
  
  form.message = `${textBefore}@${user.name} ${textAfter}`;
  showMentionList.value = false;
  
  nextTick(() => {
    if (messageInput.value) {
      const newPosition = mentionPosition.value + user.name.length + 2;
      messageInput.value.selectionStart = newPosition;
      messageInput.value.selectionEnd = newPosition;
      messageInput.value.focus();
    }
  });
};
</script>

<template>
  <div v-if="props.isOpen" class="fixed bottom-6 right-6 z-50 chat-modal-widget"
       :class="{ 'fixed inset-0 !bottom-0 !right-0 !top-0 !left-0': currentWindowWidth < 768 }" >
    <!-- ... existing header code ... -->

    <div class="flex-grow flex min-h-0"
         :class="{
           'flex-col': currentWindowWidth < 768,
           'md:flex-row': currentWindowWidth >= 768
         }">
      
      <!-- ... conversation list ... -->

      <div v-show="!showConversationList || currentWindowWidth >= 768"
           class="w-full md:w-2/3 flex flex-col bg-background min-h-0">
        
        <!-- ... loading and empty states ... -->
        
        <template v-else>
          <!-- Chat Header with Notification Toggle -->
          <div class="p-4 border-b border-border bg-card flex justify-between items-center">
            <div class="flex items-center gap-3">
              <h2 class="text-lg font-semibold">{{ selectedConversation.name }}</h2>
              <button 
                @click="toggleNotifications"
                class="p-1 rounded-full hover:bg-muted transition"
                :title="notificationsEnabled ? 'Disable notifications' : 'Enable notifications'"
              >
                <component :is="notificationsEnabled ? Bell : BellOff" class="w-4 h-4 text-primary" />
              </button>
            </div>
            
            <div class="flex items-center gap-2">
              <button
                @click="showPinnedMessages = !showPinnedMessages"
                class="text-muted-foreground hover:text-foreground p-1 rounded-full hover:bg-muted transition"
                :class="{ 'bg-primary/10 text-primary': showPinnedMessages }"
                title="Toggle pinned messages"
              >
                <Pin class="w-5 h-5" />
              </button>
              
              <button 
                v-if="currentWindowWidth < 768" 
                @click="showConversationList = true" 
                class="text-muted-foreground hover:text-foreground transition-colors px-3 py-1 rounded-md border border-border"
              >
                Back
              </button>
            </div>
          </div>
          
          <!-- Pinned Messages Section -->
          <div v-if="showPinnedMessages" class="bg-yellow-50 border-b border-yellow-200 p-3">
            <h3 class="font-semibold text-yellow-800 flex items-center gap-2 mb-2">
              <Pin class="w-4 h-4" /> Pinned Messages
            </h3>
            <div v-if="pinnedMessages.length === 0" class="text-sm text-yellow-700">
              No pinned messages
            </div>
            <div v-else class="space-y-2 max-h-40 overflow-y-auto">
              <div 
                v-for="message in pinnedMessages"
                :key="message.id"
                class="bg-white p-2 rounded border border-yellow-200"
              >
                <div class="flex justify-between">
                  <span class="font-medium text-sm">{{ conversations.find(c => c.id === message.sender_id)?.name || 'Unknown' }}</span>
                  <button 
                    @click="togglePinMessage(message)"
                    class="text-yellow-600 hover:text-yellow-800"
                    title="Unpin message"
                  >
                    <X class="w-4 h-4" />
                  </button>
                </div>
                <p class="text-sm mt-1">{{ message.message }}</p>
                <p class="text-xs text-yellow-600 mt-1">{{ format(new Date(message.created_at), 'MMM d, yyyy h:mm a') }}</p>
              </div>
            </div>
          </div>
          
          <!-- Messages Area -->
          <div ref="chatContainer" class="flex-grow p-6 overflow-y-auto space-y-4 custom-scrollbar min-h-0">
            <!-- ... existing messages code ... -->
            
            <!-- Add pin button to messages -->
            <div 
              v-for="message in messages"
              :key="message.id"
              class="flex"
              :class="[message.sender_id === authUser.id ? 'justify-end' : 'justify-start']"
            >
              <div class="relative group">
                <div
                  class="max-w-[75%] rounded-xl px-4 py-2 text-sm shadow relative"
                  :class="{
                    'bg-blue-500 text-white self-end': message.sender_id === authUser.id,
                    'bg-gray-200 text-gray-800 self-start': message.sender_id !== authUser.id,
                    'opacity-80': message.isOptimistic,
                    'border-2 border-yellow-400': message.is_pinned
                  }"
                >
                  <!-- ... message content ... -->
                  
                  <!-- Pin button -->
                  <button
                    v-if="authUser.roles.includes('Admin') || authUser.roles.includes('Super Admin')"
                    @click="togglePinMessage(message)"
                    class="absolute -top-2 -right-2 bg-white rounded-full p-1 shadow-md hover:bg-gray-100 transition opacity-0 group-hover:opacity-100"
                    :title="message.is_pinned ? 'Unpin message' : 'Pin message'"
                  >
                    <Pin class="w-3 h-3 text-yellow-500" :fill="message.is_pinned ? '#f59e0b' : 'none'" />
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Message Input with Mention Support -->
          <div class="p-4 border-t border-border bg-card relative">
            <!-- Emoji picker... -->
            
            <!-- Mention dropdown -->
            <div 
              v-if="showMentionList" 
              class="absolute bottom-full left-0 mb-2 z-20 bg-white shadow-lg rounded-md w-48 max-h-60 overflow-y-auto"
            >
              <input
                v-model="mentionSearch"
                placeholder="Search users..."
                class="w-full p-2 border-b"
                autofocus
              />
              <div 
                v-for="user in mentionUsers.filter(u => 
                  u.name.toLowerCase().includes(mentionSearch.toLowerCase()) && 
                  u.id !== authUser.id
                )"
                :key="user.id"
                @click="selectMention(user)"
                class="p-2 hover:bg-gray-100 cursor-pointer flex items-center gap-2"
              >
                <img 
                  v-if="user.profile_photo_url" 
                  :src="user.profile_photo_url" 
                  class="w-6 h-6 rounded-full" 
                />
                <span>{{ user.name }}</span>
              </div>
            </div>
            
            <form @submit.prevent="submit" class="flex items-end gap-2">
              <!-- ... existing input elements ... -->
              
              <input
                ref="messageInput"
                v-model="form.message"
                type="text"
                placeholder="Type your message here..."
                class="flex-grow form-input rounded-full border border-input px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary focus:outline-none bg-background text-foreground placeholder-muted-foreground"
                autocomplete="off"
                @keydown="checkForMention"
              />
              
              <!-- ... submit button ... -->
            </form>
            
            <!-- Downloadable Files Notice -->
            <div class="text-xs text-muted-foreground mt-2 text-center">
              <File class="w-3 h-3 inline mr-1" /> Files are downloadable for 30 days
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Add to existing styles */
.bg-yellow-50 {
  background-color: #fffbeb;
}
.border-yellow-200 {
  border-color: #fde68a;
}
.text-yellow-800 {
  color: #92400e;
}
</style>