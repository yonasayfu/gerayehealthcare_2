import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import axios from 'axios'
import type {
  GerayeUser,
  GerayeMessage,
  GerayeConversation,
  MessageFormData,
  ConversationSection,
  MessagingSettings,
  GerayeNotification,
  ConversationsResponse,
  MessagesResponse,
  SendMessageResponse,
  MessagingState,
} from '@/types/messaging'

export const useMessagingStore = defineStore('messaging', () => {
  // State
  const user = ref<GerayeUser | null>(null)
  const conversations = ref<GerayeConversation[]>([])
  const selectedConversationId = ref<number | null>(null)
  const messages = ref<GerayeMessage[]>([])
  const isLoading = ref(false)
  const isProcessing = ref(false)
  const search = ref('')
  const notifications = ref<GerayeNotification[]>([])
  const typingUsers = ref<Record<number, GerayeUser[]>>({})
  const onlineUsers = ref<number[]>([])
  const activeSidebarComponent = ref<'messages' | 'contacts' | 'settings' | 'notifications' | 'calls'>('messages')

  const settings = ref<MessagingSettings>({
    read_receipts: true,
    typing_indicators: true,
    online_status: true,
    notifications: true,
    sound_notifications: false,
    dark_mode: false,
    message_preview: true,
    auto_download_media: true,
    compact_mode: false,
  })

  // Computed
  const selectedConversation = computed(() => 
    conversations.value.find(conv => conv.id === selectedConversationId.value)
  )

  const filteredConversations = computed(() => {
    if (!search.value.trim()) return conversations.value
    
    const searchTerm = search.value.toLowerCase()
    return conversations.value.filter(conv => 
      conv.name.toLowerCase().includes(searchTerm) ||
      conv.participants.some(user => user.name.toLowerCase().includes(searchTerm))
    )
  })

  const unreadCount = computed(() => 
    conversations.value.reduce((total, conv) => total + conv.unread_count, 0)
  )

  const conversationSections = computed((): ConversationSection[] => {
    const directConversations = conversations.value.filter(conv => conv.type === 'direct')
    const groupConversations = conversations.value.filter(conv => conv.type === 'group')

    const sections: ConversationSection[] = []

    if (directConversations.length > 0) {
      sections.push({
        key: 'direct',
        label: 'Direct Messages',
        type: 'direct',
        conversations: directConversations,
      })
    }

    if (groupConversations.length > 0) {
      sections.push({
        key: 'groups',
        label: 'Groups',
        type: 'group',
        conversations: groupConversations,
      })
    }

    return sections
  })

  // Actions
  const setUser = (userData: GerayeUser) => {
    user.value = userData
  }

  const fetchConversations = async () => {
    try {
      isLoading.value = true
      const response = await axios.get<ConversationsResponse>('/api/v1/conversations')
      
      if (response.data.sections) {
        conversations.value = response.data.sections.flatMap(section => section.conversations)
      }
    } catch (error) {
      console.error('Failed to fetch conversations:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  const fetchMessages = async (conversationId: number, page = 1) => {
    try {
      isLoading.value = true
      const response = await axios.get<MessagesResponse>(
        `/api/v1/conversations/${conversationId}/messages`,
        { params: { page } }
      )
      
      if (page === 1) {
        messages.value = response.data.data
      } else {
        messages.value.unshift(...response.data.data)
      }
      
      return response.data
    } catch (error) {
      console.error('Failed to fetch messages:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  const sendMessage = async (data: MessageFormData): Promise<GerayeMessage> => {
    try {
      isProcessing.value = true
      
      const formData = new FormData()
      if (data.message) formData.append('message', data.message)
      if (data.attachment) formData.append('attachment', data.attachment)
      if (data.receiver_id) formData.append('receiver_id', data.receiver_id.toString())
      if (data.group_id) formData.append('group_id', data.group_id.toString())
      if (data.reply_to_id) formData.append('reply_to_id', data.reply_to_id.toString())
      if (data.priority) formData.append('priority', data.priority)
      if (data.message_type) formData.append('message_type', data.message_type)

      const conversationId = data.group_id || data.receiver_id
      const response = await axios.post<SendMessageResponse>(
        `/api/v1/conversations/${conversationId}/messages`,
        formData,
        {
          headers: { 'Content-Type': 'multipart/form-data' }
        }
      )

      // Add message to local state
      addMessage(response.data.message)
      
      // Update conversation
      updateConversation(response.data.conversation)

      return response.data.message
    } catch (error) {
      console.error('Failed to send message:', error)
      throw error
    } finally {
      isProcessing.value = false
    }
  }

  const updateMessage = async (messageId: number, content: string): Promise<GerayeMessage> => {
    try {
      const response = await axios.put<GerayeMessage>(`/api/v1/messages/${messageId}`, {
        message: content
      })

      // Update local state
      const index = messages.value.findIndex(msg => msg.id === messageId)
      if (index !== -1) {
        messages.value[index] = response.data
      }

      return response.data
    } catch (error) {
      console.error('Failed to update message:', error)
      throw error
    }
  }

  const deleteMessage = async (messageId: number) => {
    try {
      await axios.delete(`/api/v1/messages/${messageId}`)
      
      // Remove from local state
      messages.value = messages.value.filter(msg => msg.id !== messageId)
    } catch (error) {
      console.error('Failed to delete message:', error)
      throw error
    }
  }

  const addReaction = async (messageId: number, emoji: string) => {
    try {
      await axios.post(`/api/v1/messages/${messageId}/react`, { emoji })
    } catch (error) {
      console.error('Failed to add reaction:', error)
      throw error
    }
  }

  const removeReaction = async (messageId: number, emoji: string) => {
    try {
      await axios.delete(`/api/v1/messages/${messageId}/react`, {
        data: { emoji }
      })
    } catch (error) {
      console.error('Failed to remove reaction:', error)
      throw error
    }
  }

  const pinMessage = async (messageId: number) => {
    try {
      await axios.post(`/api/v1/messages/${messageId}/pin`)
      
      // Update local state
      const message = messages.value.find(msg => msg.id === messageId)
      if (message) {
        message.is_pinned = true
      }
    } catch (error) {
      console.error('Failed to pin message:', error)
      throw error
    }
  }

  const unpinMessage = async (messageId: number) => {
    try {
      await axios.post(`/api/v1/messages/${messageId}/unpin`)
      
      // Update local state
      const message = messages.value.find(msg => msg.id === messageId)
      if (message) {
        message.is_pinned = false
      }
    } catch (error) {
      console.error('Failed to unpin message:', error)
      throw error
    }
  }

  const markConversationAsRead = async (conversationId: number) => {
    try {
      await axios.post(`/api/v1/conversations/${conversationId}/read`)
      
      // Update local state
      const conversation = conversations.value.find(conv => conv.id === conversationId)
      if (conversation) {
        conversation.unread_count = 0
      }
    } catch (error) {
      console.error('Failed to mark conversation as read:', error)
      throw error
    }
  }

  // Enhanced API methods
  const sendTypingIndicator = async (userId: number, isTyping: boolean) => {
    try {
      await axios.post('/api/v1/messages/typing', {
        user_id: userId,
        is_typing: isTyping
      })
    } catch (error) {
      console.error('Failed to send typing indicator:', error)
    }
  }

  const fetchOnlineUsers = async () => {
    try {
      const response = await axios.get('/api/v1/messages/online-users')
      setOnlineUsers(response.data.data.map((user: any) => user.id))
      return response.data.data
    } catch (error) {
      console.error('Failed to fetch online users:', error)
      throw error
    }
  }

  const sendVoiceMessage = async (userId: number, voiceData: { voice_message: File; duration: number }) => {
    try {
      isProcessing.value = true
      
      const formData = new FormData()
      formData.append('user_id', userId.toString())
      formData.append('voice_message', voiceData.voice_message)
      formData.append('duration', voiceData.duration.toString())

      const response = await axios.post('/api/v1/messages/voice', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })

      // Add message to local state
      addMessage(response.data.data)
      
      return response.data.data
    } catch (error) {
      console.error('Failed to send voice message:', error)
      throw error
    } finally {
      isProcessing.value = false
    }
  }

  const forwardMessage = async (messageId: number, userIds: number[]) => {
    try {
      const response = await axios.post(`/api/v1/messages/${messageId}/forward`, {
        user_ids: userIds
      })
      return response.data.data
    } catch (error) {
      console.error('Failed to forward message:', error)
      throw error
    }
  }

  const replyToMessage = async (messageId: number, data: { message?: string; attachment?: File }) => {
    try {
      isProcessing.value = true
      
      const formData = new FormData()
      if (data.message) formData.append('message', data.message)
      if (data.attachment) formData.append('attachment', data.attachment)

      const response = await axios.post(`/api/v1/messages/${messageId}/reply`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })

      // Add message to local state
      addMessage(response.data.data)
      
      return response.data.data
    } catch (error) {
      console.error('Failed to reply to message:', error)
      throw error
    } finally {
      isProcessing.value = false
    }
  }

  const getMediaFiles = async (userId: number) => {
    try {
      const response = await axios.get(`/api/v1/messages/threads/${userId}/media`)
      return response.data.data
    } catch (error) {
      console.error('Failed to fetch media files:', error)
      throw error
    }
  }

  const getFiles = async (userId: number) => {
    try {
      const response = await axios.get(`/api/v1/messages/threads/${userId}/files`)
      return response.data.data
    } catch (error) {
      console.error('Failed to fetch files:', error)
      throw error
    }
  }

  const clearThread = async (userId: number) => {
    try {
      await axios.post(`/api/v1/messages/threads/${userId}/clear`)
      // Clear messages for this thread
      messages.value = []
    } catch (error) {
      console.error('Failed to clear thread:', error)
      throw error
    }
  }

  const markMessageAsRead = async (messageId: number) => {
    try {
      await axios.post(`/api/v1/messages/${messageId}/read-receipt`)
      
      // Update local state
      const message = messages.value.find(msg => msg.id === messageId)
      if (message) {
        message.read_at = new Date().toISOString()
      }
    } catch (error) {
      console.error('Failed to mark message as read:', error)
    }
  }

  const searchMessages = async (userId: number, query: string) => {
    try {
      const response = await axios.get(`/api/v1/messages/threads/${userId}/search`, {
        params: { q: query }
      })
      return response.data.data
    } catch (error) {
      console.error('Failed to search messages:', error)
      throw error
    }
  }

  const selectConversation = (conversationId: number) => {
    selectedConversationId.value = conversationId
    messages.value = [] // Clear messages when switching conversations
  }

  const addMessage = (message: GerayeMessage) => {
    // Avoid duplicates
    if (!messages.value.some(msg => msg.id === message.id)) {
      messages.value.push(message)
    }
  }

  const updateConversation = (conversation: GerayeConversation) => {
    const index = conversations.value.findIndex(conv => conv.id === conversation.id)
    if (index !== -1) {
      conversations.value[index] = conversation
    } else {
      conversations.value.push(conversation)
    }
  }

  const setTyping = (conversationId: number, users: GerayeUser[]) => {
    if (users.length > 0) {
      typingUsers.value[conversationId] = users
    } else {
      delete typingUsers.value[conversationId]
    }
  }

  const setOnlineUsers = (userIds: number[]) => {
    onlineUsers.value = userIds
  }

  const setSidebarComponent = (component: 'messages' | 'contacts' | 'settings' | 'notifications' | 'calls') => {
    activeSidebarComponent.value = component
  }

  const updateSettings = (newSettings: Partial<MessagingSettings>) => {
    settings.value = { ...settings.value, ...newSettings }
  }

  const clearMessages = () => {
    messages.value = []
  }

  const reset = () => {
    user.value = null
    conversations.value = []
    selectedConversationId.value = null
    messages.value = []
    isLoading.value = false
    isProcessing.value = false
    search.value = ''
    notifications.value = []
    typingUsers.value = {}
    onlineUsers.value = []
    activeSidebarComponent.value = 'messages'
  }

  return {
    // State
    user,
    conversations,
    selectedConversationId,
    messages,
    isLoading,
    isProcessing,
    search,
    settings,
    notifications,
    typingUsers,
    onlineUsers,
    activeSidebarComponent,
    
    // Computed
    selectedConversation,
    filteredConversations,
    unreadCount,
    conversationSections,
    
    // Actions
    setUser,
    fetchConversations,
    fetchMessages,
    sendMessage,
    updateMessage,
    deleteMessage,
    addReaction,
    removeReaction,
    pinMessage,
    unpinMessage,
    markConversationAsRead,
    selectConversation,
    addMessage,
    updateConversation,
    setTyping,
    setOnlineUsers,
    setSidebarComponent,
    updateSettings,
    clearMessages,
    reset,
    
    // Enhanced methods
    sendTypingIndicator,
    fetchOnlineUsers,
    sendVoiceMessage,
    forwardMessage,
    replyToMessage,
    getMediaFiles,
    getFiles,
    clearThread,
    markMessageAsRead,
    searchMessages,
  }
})