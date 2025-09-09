<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Bell, MessageSquare } from 'lucide-vue-next';
import axios from 'axios';

interface Notification {
  id: string;
  data: {
    sender_name: string;
    message_preview: string;
    url: string;
  };
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

const markAsReadAndRedirect = async (notification: Notification) => {
  try {
    // Navigate first for better UX; mark as read after navigation succeeds
    isDropdownOpen.value = false;
    router.visit(notification.data.url, {
      onSuccess: async () => {
        try {
          await axios.post(route('notifications.markAsRead', { notification: notification.id }));
          await fetchNotifications();
        } catch (e) {
          console.error('Failed to mark notification as read after navigation:', e);
        }
      },
    });
  } catch (error) {
    console.error('Failed handling notification redirect:', error);
  }
};

onMounted(() => {
  fetchNotifications();
  pollingInterval = window.setInterval(fetchNotifications, 15000);
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
      class="relative inline-flex items-center justify-center w-10 h-10 rounded-full text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
      class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-900 rounded-md shadow-lg border dark:border-gray-700 z-50"
      @click.away="isDropdownOpen = false"
    >
      <div class="p-3 border-b dark:border-gray-700">
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Notifications</h3>
      </div>
      <div class="max-h-96 overflow-y-auto">
        <div v-if="notifications.length > 0">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            @click="markAsReadAndRedirect(notification)"
            class="flex items-start gap-3 p-3 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer"
          >
            <div class="flex-shrink-0 pt-1">
              <MessageSquare class="h-5 w-5 text-indigo-500" />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ notification.data.sender_name }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ notification.data.message_preview }}</p>
            </div>
          </div>
        </div>
        <div v-else class="p-4 text-center text-sm text-gray-500">
          You have no unread notifications.
        </div>
      </div>
    </div>
  </div>
</template>
