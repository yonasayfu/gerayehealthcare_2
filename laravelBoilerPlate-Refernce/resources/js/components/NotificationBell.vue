<template>
  <div class="relative">
    <!-- Notification Bell Button -->
    <button
      @click="toggleDropdown"
      class="notification-bell relative p-2 text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-full"
      :class="{ 'has-notifications': unreadCount > 0 }"
    >
      <Bell class="h-6 w-6" />
      
      <!-- Notification Badge -->
      <span
        v-if="unreadCount > 0"
        class="notification-badge"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown Menu -->
    <Transition name="dropdown">
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
        @click.stop
      >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white">
              Notifications
            </h3>
            <div class="flex items-center space-x-2">
              <button
                v-if="unreadCount > 0"
                @click="markAllAsRead"
                class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
              >
                Mark all read
              </button>
              <button
                @click="clearAll"
                class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
              >
                Clear all
              </button>
            </div>
          </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
          <div v-if="notifications.length === 0" class="px-4 py-8 text-center">
            <Bell class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
              No notifications
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              You're all caught up!
            </p>
          </div>

          <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
            <div
              v-for="notification in notifications"
              :key="notification.id"
              class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-150"
              :class="{ 'bg-blue-50 dark:bg-blue-900/10': !notification.read }"
              @click="handleNotificationClick(notification)"
            >
              <div class="flex items-start space-x-3">
                <!-- Icon -->
                <div class="flex-shrink-0">
                  <div
                    class="w-8 h-8 rounded-full flex items-center justify-center"
                    :class="getNotificationIconClass(notification.type)"
                  >
                    <component
                      :is="getNotificationIcon(notification.type)"
                      class="w-4 h-4"
                    />
                  </div>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ notification.title }}
                  </p>
                  <p v-if="notification.message" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ notification.message }}
                  </p>
                  <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                    {{ formatTime(notification.created_at) }}
                  </p>
                </div>

                <!-- Unread indicator -->
                <div v-if="!notification.read" class="flex-shrink-0">
                  <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
          <Link
            href="/notifications"
            class="block text-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium"
            @click="closeDropdown"
          >
            View all notifications
          </Link>
        </div>
      </div>
    </Transition>

    <!-- Backdrop -->
    <div
      v-if="isOpen"
      class="fixed inset-0 z-40"
      @click="closeDropdown"
    ></div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { 
  Bell, 
  MessageSquare, 
  User, 
  AlertTriangle, 
  CheckCircle, 
  Info 
} from 'lucide-vue-next'

interface Notification {
  id: string
  type: 'message' | 'user' | 'system' | 'warning' | 'success' | 'info'
  title: string
  message?: string
  read: boolean
  created_at: string
  data?: Record<string, any>
}

const isOpen = ref(false)
const notifications = ref<Notification[]>([
  // Mock notifications - replace with real data
  {
    id: '1',
    type: 'message',
    title: 'New message from John Doe',
    message: 'Hey, how are you doing?',
    read: false,
    created_at: new Date(Date.now() - 5 * 60 * 1000).toISOString()
  },
  {
    id: '2',
    type: 'user',
    title: 'New user registered',
    message: 'Jane Smith has joined the platform',
    read: false,
    created_at: new Date(Date.now() - 15 * 60 * 1000).toISOString()
  },
  {
    id: '3',
    type: 'system',
    title: 'System maintenance',
    message: 'Scheduled maintenance completed successfully',
    read: true,
    created_at: new Date(Date.now() - 60 * 60 * 1000).toISOString()
  }
])

const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.read).length
})

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
}

const markAllAsRead = () => {
  notifications.value.forEach(notification => {
    notification.read = true
  })
  // TODO: Send API request to mark all as read
}

const clearAll = () => {
  notifications.value = []
  // TODO: Send API request to clear all notifications
}

const handleNotificationClick = (notification: Notification) => {
  if (!notification.read) {
    notification.read = true
    // TODO: Send API request to mark as read
  }
  
  // Handle navigation based on notification type
  switch (notification.type) {
    case 'message':
      // Navigate to messages
      break
    case 'user':
      // Navigate to users
      break
    default:
      // Default action
      break
  }
  
  closeDropdown()
}

const getNotificationIcon = (type: Notification['type']) => {
  const icons = {
    message: MessageSquare,
    user: User,
    system: Info,
    warning: AlertTriangle,
    success: CheckCircle,
    info: Info
  }
  return icons[type] || Info
}

const getNotificationIconClass = (type: Notification['type']) => {
  const classes = {
    message: 'bg-blue-100 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400',
    user: 'bg-green-100 text-green-600 dark:bg-green-900/20 dark:text-green-400',
    system: 'bg-gray-100 text-gray-600 dark:bg-gray-900/20 dark:text-gray-400',
    warning: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400',
    success: 'bg-green-100 text-green-600 dark:bg-green-900/20 dark:text-green-400',
    info: 'bg-blue-100 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400'
  }
  return classes[type] || classes.info
}

const formatTime = (dateString: string) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInMinutes = Math.floor((now.getTime() - date.getTime()) / (1000 * 60))
  
  if (diffInMinutes < 1) {
    return 'Just now'
  } else if (diffInMinutes < 60) {
    return `${diffInMinutes}m ago`
  } else if (diffInMinutes < 1440) {
    return `${Math.floor(diffInMinutes / 60)}h ago`
  } else {
    return `${Math.floor(diffInMinutes / 1440)}d ago`
  }
}

const handleClickOutside = (event: Event) => {
  if (isOpen.value && !event.target?.closest('.notification-bell')) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}
</style>
