<template>
  <div class="messaging-container h-screen flex">
    <!-- Sidebar with conversations -->
    <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
      <!-- Header -->
      <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Messages</h2>
        <div v-if="unreadCount > 0" class="text-sm text-gray-500">
          {{ unreadCount }} unread messages
        </div>
      </div>

      <!-- Conversations list -->
      <div class="flex-1 overflow-y-auto">
        <div v-if="isLoading" class="p-4">
          <div class="animate-pulse space-y-2">
            <div class="h-12 bg-gray-200 rounded"></div>
            <div class="h-12 bg-gray-200 rounded"></div>
            <div class="h-12 bg-gray-200 rounded"></div>
          </div>
        </div>

        <div v-else-if="conversations.length === 0" class="p-4 text-center text-gray-500">
          No conversations yet
        </div>

        <div v-else>
          <div
            v-for="conversation in conversations"
            :key="conversation.id"
            @click="selectConversation(conversation.id)"
            class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer flex items-center space-x-3"
            :class="{ 'bg-blue-50': selectedConversation?.id === conversation.id }"
          >
            <!-- Avatar -->
            <div class="relative">
              <img
                v-if="conversation.avatar"
                :src="conversation.avatar"
                :alt="conversation.name"
                class="w-12 h-12 rounded-full object-cover"
              >
              <div
                v-else
                class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium"
              >
                {{ conversation.name.charAt(0).toUpperCase() }}
              </div>
              
              <!-- Online indicator -->
              <div
                v-if="conversation.type === 'direct' && isUserOnline(conversation.id)"
                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"
              ></div>
            </div>

            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-900 truncate">
                  {{ conversation.name }}
                </h3>
                <span v-if="conversation.last_message_at" class="text-xs text-gray-500">
                  {{ formatMessageTime(conversation.last_message_at) }}
                </span>
              </div>

              <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 truncate">
                  {{ conversation.last_message?.message || 'No messages yet' }}
                </p>
                <span
                  v-if="conversation.unread_count > 0"
                  class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-blue-600 rounded-full"
                >
                  {{ conversation.unread_count }}
                </span>
              </div>

              <!-- Typing indicator -->
              <div v-if="getTypingUsers(conversation.id).length > 0" class="text-xs text-blue-500 mt-1">
                {{ getTypingUsers(conversation.id).map(u => u.name).join(', ') }} typing...
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Chat area -->
    <div class="flex-1 flex flex-col">
      <div v-if="!selectedConversation" class="flex-1 flex items-center justify-center text-gray-500">
        Select a conversation to start chatting
      </div>

      <div v-else class="flex-1 flex flex-col">
        <!-- Chat header -->
        <div class="p-4 border-b border-gray-200 bg-white">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <img
                v-if="selectedConversation.avatar"
                :src="selectedConversation.avatar"
                :alt="selectedConversation.name"
                class="w-10 h-10 rounded-full object-cover"
              >
              <div
                v-else
                class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium"
              >
                {{ selectedConversation.name.charAt(0).toUpperCase() }}
              </div>
              
              <div>
                <h3 class="text-sm font-medium text-gray-900">
                  {{ selectedConversation.name }}
                </h3>
                <p class="text-xs text-gray-500">
                  {{ selectedConversation.type === 'direct' && isUserOnline(selectedConversation.id) ? 'Online' : 'Offline' }}
                </p>
              </div>
            </div>

            <div class="flex items-center space-x-2">
              <!-- Voice call button -->
              <button
                type="button"
                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full"
                title="Voice call"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
              </button>

              <!-- Video call button -->
              <button
                type="button"
                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full"
                title="Video call"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
              </button>

              <!-- Info button -->
              <button
                type="button"
                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full"
                title="Conversation info"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Messages area -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4" ref="messagesContainer">
          <div v-if="isLoading" class="flex justify-center">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
          </div>

          <div
            v-for="message in messages"
            :key="message.id"
            class="flex"
            :class="message.sender_id === currentUser?.id ? 'justify-end' : 'justify-start'"
          >
            <div
              class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg"
              :class="message.sender_id === currentUser?.id 
                ? 'bg-blue-500 text-white' 
                : 'bg-gray-200 text-gray-900'"
            >
              <!-- Reply indicator -->
              <div v-if="message.reply_to_id" class="mb-2 pb-2 border-b border-opacity-30">
                <div class="text-xs opacity-75">
                  Replying to: {{ message.replyTo?.message || 'Message' }}
                </div>
              </div>

              <!-- Message content -->
              <div v-if="message.message" class="break-words">
                {{ message.message }}
              </div>

              <!-- Attachment -->
              <div v-if="message.attachment_url" class="mt-2">
                <div v-if="message.attachment_type === 'image'" class="max-w-sm">
                  <img :src="message.attachment_url" :alt="message.attachment_filename" class="rounded max-w-full h-auto">
                </div>
                <div v-else-if="message.attachment_type === 'voice'" class="flex items-center space-x-2">
                  <button class="p-2 bg-white bg-opacity-20 rounded-full">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                    </svg>
                  </button>
                  <span class="text-sm">{{ message.duration }}s</span>
                </div>
                <div v-else class="flex items-center space-x-2 p-2 bg-white bg-opacity-20 rounded">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                  </svg>
                  <span class="text-sm">{{ message.attachment_filename }}</span>
                </div>
              </div>

              <!-- Message reactions -->
              <div v-if="message.reactions && message.reactions.length > 0" class="mt-2 flex space-x-1">
                <span
                  v-for="reaction in message.reactions"
                  :key="`${reaction.emoji}-${reaction.id}`"
                  class="px-2 py-1 bg-white bg-opacity-20 rounded-full text-xs flex items-center space-x-1"
                >
                  <span>{{ reaction.emoji }}</span>
                  <span>1</span>
                </span>
              </div>

              <!-- Message metadata -->
              <div class="flex items-center justify-between mt-2">
                <div class="text-xs opacity-75">
                  {{ formatMessageTime(message.created_at) }}
                  <span v-if="message.edited_at">(edited)</span>
                </div>
                
                <!-- Message status for sent messages -->
                <div v-if="message.sender_id === currentUser?.id" class="text-xs opacity-75">
                  <span v-if="message.read_at">✓✓</span>
                  <span v-else>✓</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Typing indicators -->
        <div v-if="getTypingUsers(selectedConversation.id).length > 0" class="px-4 py-2 text-sm text-gray-500">
          {{ getTypingUsers(selectedConversation.id).map(u => u.name).join(', ') }} 
          {{ getTypingUsers(selectedConversation.id).length === 1 ? 'is' : 'are' }} typing...
        </div>

        <!-- Message input -->
        <div class="p-4 border-t border-gray-200 bg-white">
          <div class="flex items-end space-x-3">
            <!-- Attachment button -->
            <button
              type="button"
              class="flex-shrink-0 p-2 text-gray-500 hover:text-gray-700"
              @click="$refs.fileInput.click()"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
              </svg>
            </button>

            <input
              ref="fileInput"
              type="file"
              class="hidden"
              @change="handleFileSelect"
              accept="*/*"
            >

            <!-- Message input -->
            <div class="flex-1 min-w-0">
              <textarea
                v-model="messageText"
                @keydown="handleKeyDown"
                @input="handleTyping(selectedConversation.id)"
                rows="1"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Type a message..."
                :disabled="isProcessing"
              ></textarea>
            </div>

            <!-- Voice message button -->
            <button
              type="button"
              class="flex-shrink-0 p-2 text-gray-500 hover:text-gray-700"
              :class="{ 'text-red-500': recordingAudio }"
              @mousedown="startRecording"
              @mouseup="stopRecording"
              @mouseleave="stopRecording"
              title="Hold to record voice message"
            >
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clip-rule="evenodd"></path>
              </svg>
            </button>

            <!-- Send button -->
            <button
              type="button"
              @click="sendTextMessage"
              :disabled="!messageText.trim() || isProcessing"
              class="flex-shrink-0 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="isProcessing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick, watch } from 'vue'
import { useMessaging } from '@/composables/useMessaging'
import type { GerayeUser } from '@/types/messaging'

// Props
interface Props {
  user: GerayeUser
}

const props = defineProps<Props>()

// Messaging composable
const {
  // State
  currentUser,
  conversations,
  selectedConversation,
  messages,
  isLoading,
  isProcessing,
  unreadCount,
  onlineUsers,
  typingUsers,
  recordingAudio,

  // Methods
  initialize,
  sendMessage,
  sendVoiceMessage,
  startVoiceRecording,
  stopVoiceRecording,
  handleTyping,
  selectConversation,

  // Utilities
  isUserOnline,
  getTypingUsers,
  formatMessageTime,
} = useMessaging()

// Local state
const messageText = ref('')
const selectedFile = ref<File | null>(null)
const messagesContainer = ref<HTMLElement>()

// Initialize messaging system
onMounted(async () => {
  try {
    await initialize(props.user)
  } catch (error) {
    console.error('Failed to initialize messaging:', error)
  }
})

// Auto scroll to bottom when new messages arrive
watch(messages, async () => {
  await nextTick()
  scrollToBottom()
}, { deep: true })

// Methods
const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const sendTextMessage = async () => {
  if (!messageText.value.trim() || !selectedConversation.value) return

  try {
    const data = {
      message: messageText.value.trim(),
      receiver_id: selectedConversation.value.type === 'direct' ? selectedConversation.value.id : undefined,
      group_id: selectedConversation.value.type === 'group' ? selectedConversation.value.id : undefined,
      attachment: selectedFile.value || undefined,
    }

    await sendMessage(data)
    
    // Clear input
    messageText.value = ''
    selectedFile.value = null
  } catch (error) {
    console.error('Failed to send message:', error)
  }
}

const handleKeyDown = (event: KeyboardEvent) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault()
    sendTextMessage()
  }
}

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    selectedFile.value = target.files[0]
    // Auto send file
    sendTextMessage()
  }
}

const startRecording = async () => {
  try {
    await startVoiceRecording()
  } catch (error) {
    console.error('Failed to start recording:', error)
  }
}

const stopRecording = async () => {
  if (!recordingAudio.value || !selectedConversation.value) return

  try {
    const { blob, duration } = await stopVoiceRecording()
    
    if (selectedConversation.value.type === 'direct') {
      await sendVoiceMessage(selectedConversation.value.id, blob, duration)
    }
  } catch (error) {
    console.error('Failed to stop recording:', error)
  }
}
</script>

<style scoped>
/* Custom scrollbar styles */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>