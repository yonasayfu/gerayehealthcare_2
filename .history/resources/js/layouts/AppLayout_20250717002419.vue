<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { eventBus } from '@/lib/eventBus'; // Import eventBus
import { Bell, MessageSquareText } from 'lucide-vue-next'; // Import icons
import { formatDistanceToNow } from 'date-fns'; // For time formatting

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

const notifications = ref<any[]>([]);
const unreadCount = ref(0);
const showNotifications = ref(false);

const fetchNotifications = async () => {
  try {
    const response = await axios.get(route('notifications.index'));
    notifications.value = response.data.notifications;
    unreadCount.value = response.data.unread_count;
  } catch (error) {
    console.error('Error fetching notifications:', error);
  }
};

const markAsRead = async (notificationId: string, conversationId: number | null = null) => {
  try {
    await axios.post(route('notifications.markAsRead', notificationId));
    await fetchNotifications(); // Re-fetch to update count and list
    if (conversationId) {
      eventBus.emit('open-chat', conversationId); // Emit event to open chat
    }
  } catch (error) {
    console.error('Error marking notification as read:', error);
  }
};

onMounted(() => {
  fetchNotifications();
  // Optionally, poll for new notifications every X seconds
  // setInterval(fetchNotifications, 30000); // e.g., every 30 seconds
});

onUnmounted(() => {
  // Clear any intervals if set
});
</script>

<template>
  <AppSidebarLayout :breadcrumbs="breadcrumbs">
    <slot />

    <!-- Simple Notification Bell and Dropdown -->
    <div class="fixed top-4 right-4 z-50">
      <button @click="showNotifications = !showNotifications" class="relative p-3 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition transform hover:scale-110">
        <Bell class="w-6 h-6 text-gray-700 dark:text-gray-200" />
        <span v-if="unreadCount > 0" class="absolute top-0 right-0 block h-4 w-4 rounded-full ring-2 ring-white bg-red-500 text-white text-xs flex items-center justify-center">
          {{ unreadCount }}
        </span>
      </button>

      <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 overflow-hidden">
        <div class="px-4 py-2 text-sm font-semibold text-gray-800 dark:text-gray-100 border-b dark:border-gray-700">
          Notifications ({{ unreadCount }} unread)
        </div>
        <div v-if="notifications.length === 0" class="p-4 text-sm text-gray-500 dark:text-gray-400">
          No new notifications.
        </div>
        <div v-else class="max-h-60 overflow-y-auto custom-scrollbar">
          <a
            v-for="notification in notifications"
            :key="notification.id"
            @click.prevent="markAsRead(notification.id, notification.data.conversation_id)"
            class="flex items-start px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b dark:border-gray-700"
            :class="{ 'bg-indigo-50 dark:bg-indigo-900/20': !notification.read_at }"
          >
            <div class="flex-shrink-0 pt-1">
              <MessageSquareText class="w-5 h-5 text-indigo-500" />
            </div>
            <div class="ml-3 overflow-hidden">
              <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                New message from {{ notification.data.sender_name }}
              </p>
              <p class="text-xs text-gray-600 dark:text-gray-300 break-words">
                {{ notification.data.message_preview }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                {{ formatDistanceToNow(new Date(notification.created_at), { addSuffix: true }) }}
              </p>
            </div>
          </a>
        </div>
      </div>
    </div>
  </AppSidebarLayout>
</template>

<style>
/* Re-using custom-scrollbar styles from ChatModal.vue */
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
</style>
