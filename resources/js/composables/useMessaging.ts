import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useMessagingStore } from '@/stores/messaging'
import { getMessagingWebSocketService } from '@/services/messagingWebSocket'
import type { GerayeUser, GerayeMessage, MessageFormData } from '@/types/messaging'

export function useMessaging() {
  const messagingStore = useMessagingStore()
  const webSocketService = getMessagingWebSocketService()
  
  const isTyping = ref(false)
  const typingTimeout = ref<NodeJS.Timeout | null>(null)
  const recordingAudio = ref(false)
  const mediaRecorder = ref<MediaRecorder | null>(null)
  const audioChunks = ref<Blob[]>([])

  // Computed properties
  const currentUser = computed(() => messagingStore.user)
  const conversations = computed(() => messagingStore.conversations)
  const selectedConversation = computed(() => messagingStore.selectedConversation)
  const messages = computed(() => messagingStore.messages)
  const isLoading = computed(() => messagingStore.isLoading)
  const isProcessing = computed(() => messagingStore.isProcessing)
  const unreadCount = computed(() => messagingStore.unreadCount)
  const onlineUsers = computed(() => messagingStore.onlineUsers)
  const typingUsers = computed(() => messagingStore.typingUsers)

  // Initialize messaging system
  const initialize = async (user: GerayeUser) => {
    try {
      // Set current user
      messagingStore.setUser(user)
      
      // Initialize WebSocket connection
      webSocketService.init(user.id)
      
      // Request notification permission
      await webSocketService.requestNotificationPermission()
      
      // Fetch initial data
      await Promise.all([
        messagingStore.fetchConversations(),
        messagingStore.fetchOnlineUsers()
      ])
      
      console.log('Messaging system initialized successfully')
    } catch (error) {
      console.error('Failed to initialize messaging system:', error)
      throw error
    }
  }

  // Send a text message
  const sendMessage = async (data: MessageFormData) => {
    try {
      const message = await messagingStore.sendMessage(data)
      return message
    } catch (error) {
      console.error('Failed to send message:', error)
      throw error
    }
  }

  // Send voice message
  const sendVoiceMessage = async (userId: number, audioBlob: Blob, duration: number) => {
    try {
      const audioFile = new File([audioBlob], 'voice-message.webm', { type: 'audio/webm' })
      const message = await messagingStore.sendVoiceMessage(userId, {
        voice_message: audioFile,
        duration: duration
      })
      return message
    } catch (error) {
      console.error('Failed to send voice message:', error)
      throw error
    }
  }

  // Start recording audio
  const startVoiceRecording = async () => {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true })
      mediaRecorder.value = new MediaRecorder(stream)
      audioChunks.value = []
      
      mediaRecorder.value.ondataavailable = (event) => {
        if (event.data.size > 0) {
          audioChunks.value.push(event.data)
        }
      }
      
      mediaRecorder.value.start()
      recordingAudio.value = true
      
      console.log('Started voice recording')
    } catch (error) {
      console.error('Failed to start voice recording:', error)
      throw error
    }
  }

  // Stop recording audio
  const stopVoiceRecording = (): Promise<{ blob: Blob; duration: number }> => {
    return new Promise((resolve, reject) => {
      if (!mediaRecorder.value || recordingAudio.value === false) {
        reject(new Error('No active recording'))
        return
      }

      const startTime = Date.now()
      
      mediaRecorder.value.onstop = () => {
        const duration = Math.floor((Date.now() - startTime) / 1000)
        const audioBlob = new Blob(audioChunks.value, { type: 'audio/webm' })
        
        // Stop all tracks to release microphone
        if (mediaRecorder.value?.stream) {
          mediaRecorder.value.stream.getTracks().forEach(track => track.stop())
        }
        
        recordingAudio.value = false
        mediaRecorder.value = null
        audioChunks.value = []
        
        resolve({ blob: audioBlob, duration })
      }
      
      mediaRecorder.value.onerror = (event) => {
        reject(new Error('Recording failed'))
      }
      
      mediaRecorder.value.stop()
    })
  }

  // Send typing indicator
  const sendTypingIndicator = (userId: number, typing: boolean) => {
    webSocketService.sendTypingIndicator(userId, typing)
  }

  // Handle typing with debounce
  const handleTyping = (userId: number) => {
    if (!isTyping.value) {
      isTyping.value = true
      sendTypingIndicator(userId, true)
    }

    // Clear existing timeout
    if (typingTimeout.value) {
      clearTimeout(typingTimeout.value)
    }

    // Set new timeout to stop typing indicator
    typingTimeout.value = setTimeout(() => {
      isTyping.value = false
      sendTypingIndicator(userId, false)
      typingTimeout.value = null
    }, 1000)
  }

  // Select conversation
  const selectConversation = async (conversationId: number) => {
    messagingStore.selectConversation(conversationId)
    
    // Subscribe to conversation events
    webSocketService.subscribeToConversation(conversationId)
    
    // Fetch messages for the conversation
    await messagingStore.fetchMessages(conversationId)
    
    // Mark conversation as read
    await messagingStore.markConversationAsRead(conversationId)
  }

  // React to message
  const reactToMessage = async (messageId: number, emoji: string) => {
    try {
      await messagingStore.addReaction(messageId, emoji)
    } catch (error) {
      console.error('Failed to react to message:', error)
      throw error
    }
  }

  // Remove reaction
  const removeReaction = async (messageId: number, emoji: string) => {
    try {
      await messagingStore.removeReaction(messageId, emoji)
    } catch (error) {
      console.error('Failed to remove reaction:', error)
      throw error
    }
  }

  // Edit message
  const editMessage = async (messageId: number, newContent: string) => {
    try {
      const message = await messagingStore.updateMessage(messageId, newContent)
      return message
    } catch (error) {
      console.error('Failed to edit message:', error)
      throw error
    }
  }

  // Delete message
  const deleteMessage = async (messageId: number) => {
    try {
      await messagingStore.deleteMessage(messageId)
    } catch (error) {
      console.error('Failed to delete message:', error)
      throw error
    }
  }

  // Pin message
  const pinMessage = async (messageId: number) => {
    try {
      await messagingStore.pinMessage(messageId)
    } catch (error) {
      console.error('Failed to pin message:', error)
      throw error
    }
  }

  // Unpin message
  const unpinMessage = async (messageId: number) => {
    try {
      await messagingStore.unpinMessage(messageId)
    } catch (error) {
      console.error('Failed to unpin message:', error)
      throw error
    }
  }

  // Forward message
  const forwardMessage = async (messageId: number, userIds: number[]) => {
    try {
      const messages = await messagingStore.forwardMessage(messageId, userIds)
      return messages
    } catch (error) {
      console.error('Failed to forward message:', error)
      throw error
    }
  }

  // Reply to message
  const replyToMessage = async (messageId: number, data: { message?: string; attachment?: File }) => {
    try {
      const message = await messagingStore.replyToMessage(messageId, data)
      return message
    } catch (error) {
      console.error('Failed to reply to message:', error)
      throw error
    }
  }

  // Search messages
  const searchMessages = async (userId: number, query: string) => {
    try {
      const results = await messagingStore.searchMessages(userId, query)
      return results
    } catch (error) {
      console.error('Failed to search messages:', error)
      throw error
    }
  }

  // Get media files
  const getMediaFiles = async (userId: number) => {
    try {
      const mediaFiles = await messagingStore.getMediaFiles(userId)
      return mediaFiles
    } catch (error) {
      console.error('Failed to get media files:', error)
      throw error
    }
  }

  // Get files
  const getFiles = async (userId: number) => {
    try {
      const files = await messagingStore.getFiles(userId)
      return files
    } catch (error) {
      console.error('Failed to get files:', error)
      throw error
    }
  }

  // Clear thread
  const clearThread = async (userId: number) => {
    try {
      await messagingStore.clearThread(userId)
    } catch (error) {
      console.error('Failed to clear thread:', error)
      throw error
    }
  }

  // Check if user is online
  const isUserOnline = (userId: number) => {
    return onlineUsers.value.includes(userId)
  }

  // Check if user is typing in conversation
  const isUserTyping = (conversationId: number, userId: number) => {
    const users = typingUsers.value[conversationId] || []
    return users.some(user => user.id === userId)
  }

  // Get typing users for conversation
  const getTypingUsers = (conversationId: number) => {
    return typingUsers.value[conversationId] || []
  }

  // Format file size
  const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }

  // Format message time
  const formatMessageTime = (timestamp: string): string => {
    const date = new Date(timestamp)
    const now = new Date()
    const diffInHours = (now.getTime() - date.getTime()) / (1000 * 60 * 60)

    if (diffInHours < 24) {
      return date.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
      })
    } else if (diffInHours < 24 * 7) {
      return date.toLocaleDateString('en-US', { weekday: 'short' })
    } else {
      return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric' 
      })
    }
  }

  // Cleanup on component unmount
  onUnmounted(() => {
    // Clear typing timeout
    if (typingTimeout.value) {
      clearTimeout(typingTimeout.value)
    }
    
    // Stop any ongoing recording
    if (recordingAudio.value && mediaRecorder.value) {
      mediaRecorder.value.stop()
    }
    
    // Disconnect WebSocket
    webSocketService.disconnect()
  })

  return {
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
    isTyping,
    recordingAudio,

    // Methods
    initialize,
    sendMessage,
    sendVoiceMessage,
    startVoiceRecording,
    stopVoiceRecording,
    sendTypingIndicator,
    handleTyping,
    selectConversation,
    reactToMessage,
    removeReaction,
    editMessage,
    deleteMessage,
    pinMessage,
    unpinMessage,
    forwardMessage,
    replyToMessage,
    searchMessages,
    getMediaFiles,
    getFiles,
    clearThread,

    // Utilities
    isUserOnline,
    isUserTyping,
    getTypingUsers,
    formatFileSize,
    formatMessageTime,
  }
}

// Export store for direct access if needed
export { useMessagingStore }