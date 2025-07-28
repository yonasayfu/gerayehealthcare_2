<script setup lang="ts">
import AppContent from '@/components/AppContent.vue'
import AppShell from '@/components/AppShell.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import AppSidebarHeader from '@/components/AppSidebarHeader.vue'
import FloatingChatButton from '@/components/FloatingChatButton.vue'; // Import the new component
import type { BreadcrumbItemType } from '@/types'

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
  unreadCount?: number;
  inventoryAlertCount?: number; // New prop
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
  unreadCount: 0,
  inventoryAlertCount: 0, // Default value
});
</script>

<template>
  <AppShell variant="sidebar">
    <AppSidebar class="print:hidden" :unread-count="unreadCount" :inventory-alert-count="inventoryAlertCount"/> 
    
    <AppContent variant="sidebar">
      <AppSidebarHeader :breadcrumbs="breadcrumbs" class="print:hidden" />
      <slot />
    </AppContent>
    <FloatingChatButton class="print:hidden" /> <!-- Add the floating chat button here -->
  </AppShell>
</template>
