<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Bell, MessageSquare, CheckCircle, AlertCircle, Info } from 'lucide-vue-next';
import axios from 'axios';

interface Notification {
  id: string;
  type: string;
  data: {
    sender_name: string;
    message_preview: string;
    url: string;
    type?: string;
  };
  read_at: string | null;
}

const notifications = ref<Notification[]>([]);
const unreadCount = ref(0);
const isDropdownOpen = ref(false);
let pollingInterval: number | null = null;

const fetchNotifications = async () => {
  try {
    const response = await axios.get(route('notifications.index'));
    notifications.value = response.data.notifications;
    unreadCount.value = response.data.unread_count;
  } catch (error) {
    console.error('Failed to fetch notifications:', error);
  }
};

const markAsRead = async (notificationId: string) => {
  try {
    await axios.post(route('notifications.markAsRead', { notification: notificationId }));
    // Update local state
    const notification = notifications.value.find(n => n.id === notificationId);
    if (notification) {
      notification.read_at = new Date().toISOString();
    }
    // Update unread count
    unreadCount.value = Math.max(0, unreadCount.value - 1);
  } catch (error) {
    console.error('Failed to mark notification as read:', error);
  }
};

const markAllRead = async () => {
  if (window.confirm('Are you sure you want to mark all notifications as read?')) {
    try {
      await axios.post(route('notifications.markAllRead'));
      // Update local state
      notifications.value.forEach(notification => {
        notification.read_at = new Date().toISOString();
      });
      unreadCount.value = 0;
    } catch (error) {
      console.error('Failed to mark all notifications as read:', error);
    }
  }
};

const getNotificationIcon = (type: string) => {
  if (type.includes('message')) return MessageSquare;
  if (type.includes('task')) return CheckCircle;
  if (type.includes('alert') || type.includes('warning')) return AlertCircle;
  return Info;
};

const handleNotificationClick = async (notification: Notification) => {
  try {
    // Close dropdown first for better UX
    isDropdownOpen.value = false;
    
    // Check if this is a message notification that should open message modal
    if (notification.type.includes('message') && !notification.data.url) {
      // Emit event to open message modal
      window.dispatchEvent(new CustomEvent('open-message-modal', {
        detail: { sender: notification.data.sender_name }
      }));
      // Mark as read
      await markAsRead(notification.id);
    } else if (notification.data.url) {
      // Navigate to the notification URL
      router.visit(notification.data.url, {
        onSuccess: async () => {
          // Mark as read after successful navigation
          await markAsRead(notification.id);
        },
        onError: async () => {
          // Even if navigation fails, we might want to mark as read
          await markAsRead(notification.id);
        }
      });
    } else {
      // If no URL, just mark as read
      await markAsRead(notification.id);
    }
  } catch (error) {
    console.error('Failed handling notification click:', error);
    // Still mark as read even if there's an error
    await markAsRead(notification.id);
  }
};

onMounted(() => {
  fetchNotifications();
  pollingInterval = window.setInterval(fetchNotifications, 30000); // Poll every 30 seconds instead of 15
});

onUnmounted(() => {
  if (pollingInterval) {
    clearInterval(pollingInterval);
  }
});
</script>

<template>
  <div class="relative">
    <button
      @click="isDropdownOpen = !isDropdownOpen"
      class="relative inline-flex items-center justify-center w-10 h-10 rounded-full text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 backdrop-blur-md bg-white/30 dark:bg-gray-800/30 border border-white/20 dark:border-gray-700/50"
    >
      <Bell class="h-6 w-6" />
      <span
        v-if="unreadCount > 0"
        class="absolute top-1 right-1 flex items-center justify-center h-5 w-5 transform rounded-full bg-red-600 text-xs font-bold text-white"
        style="font-size: 0.7rem;"
      >
        {{ unreadCount }}
      </span>
    </button>

    <div
      v-if="isDropdownOpen"
      class="absolute right-0 mt-2 w-80 bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg rounded-xl shadow-xl border border-white/30 dark:border-gray-700/50 z-50 overflow-hidden"
      @click.away="isDropdownOpen = false"
    >
      <div class="p-4 border-b border-white/30 dark:border-gray-700/50 flex justify-between items-center bg-white/50 dark:bg-gray-900/50">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Notifications</h3>
        <button 
          v-if="unreadCount > 0"
          @click="markAllRead"
          class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300"
        >
          Mark all as read
        </button>
      </div>
      <div class="max-h-96 overflow-y-auto">
        <div v-if="notifications.length > 0">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            @click="handleNotificationClick(notification)"
            class="flex items-start gap-3 p-4 hover:bg-white/50 dark:hover:bg-gray-800/50 cursor-pointer border-b border-white/20 dark:border-gray-700/30 transition-all duration-200"
            :class="{ 'bg-white/30 dark:bg-gray-800/30': notification.read_at }"
          >
            <div class="flex-shrink-0 pt-1">
              <component :is="getNotificationIcon(notification.type)" class="h-5 w-5 text-indigo-500" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ notification.data.sender_name }}</p>
              <p class="text-sm text-gray-600 dark:text-gray-300">{{ notification.data.message_preview }}</p>
              <p v-if="notification.read_at" class="text-xs text-gray-400 dark:text-gray-500 mt-1">Read</p>
            </div>
          </div>
        </div>
        <div v-else class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
          You have no notifications.
        </div>
      </div>
    </div>
  </div>
</template>