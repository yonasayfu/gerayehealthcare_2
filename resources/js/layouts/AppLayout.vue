<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { eventBus } from '@/lib/eventBus'; // Import eventBus
import { Bell, MessageSquareText } from 'lucide-vue-next'; // Import icons
import { formatDistanceToNow } from 'date-fns'; // For time formatting
import Toast from '@/components/Toast.vue';
import ConfirmModal from '@/components/ConfirmModal.vue';

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
});

const notifications = ref<any[]>([]);
const unreadCount = ref(0);
const inventoryAlertCount = ref(0); // New ref for inventory alerts
const showNotifications = ref(false);

// Global confirm modal state
const confirmOpen = ref(false)
const confirmTitle = ref<string>('Are you sure?')
const confirmMessage = ref<string>('')
const confirmOkText = ref<string>('Confirm')
const confirmCancelText = ref<string>('Cancel')
let confirmResolve: ((v: boolean) => void) | null = null

const fetchNotifications = async () => {
  try {
    const response = await axios.get(route('notifications.index'));
    notifications.value = response.data.notifications;
    unreadCount.value = response.data.unread_count;
  } catch (error) {
    console.error('Error fetching notifications:', error);
  }
};

const fetchInventoryAlertCount = async () => {
  try {
    const response = await axios.get(route('admin.inventory-alerts.count'));
    inventoryAlertCount.value = response.data.count;
    
  } catch (error) {
    console.error('Error fetching inventory alert count:', error);
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
  fetchInventoryAlertCount();
  // REMOVED: Continuous polling that was killing performance
  // Only fetch on page load, not continuously

  eventBus.on('confirm:open', (payload) => {
    confirmTitle.value = payload.title || 'Are you sure?'
    confirmMessage.value = payload.message || ''
    confirmOkText.value = payload.confirmText || 'Confirm'
    confirmCancelText.value = payload.cancelText || 'Cancel'
    confirmResolve = payload.__resolve
    confirmOpen.value = true
  })
});

onUnmounted(() => {
  // Clear any intervals if set
  // Optionally, remove listeners if needed (mitt keeps a single instance)
});

function handleConfirmCancel() {
  confirmOpen.value = false
  confirmResolve?.(false)
  confirmResolve = null
}

function handleConfirmOk() {
  confirmOpen.value = false
  confirmResolve?.(true)
  confirmResolve = null
}
</script>

<template>
  <AppSidebarLayout :breadcrumbs="breadcrumbs" :unread-count="unreadCount" :inventory-alert-count="inventoryAlertCount">
    <Toast />
    <slot />
    <ConfirmModal
      :open="confirmOpen"
      :title="confirmTitle"
      :message="confirmMessage"
      :confirm-text="confirmOkText"
      :cancel-text="confirmCancelText"
      @update:open="(v:boolean)=>{ if(!v) handleConfirmCancel() }"
      @confirm="handleConfirmOk"
      @cancel="handleConfirmCancel"
    />
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
