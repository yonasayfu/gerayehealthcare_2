<script setup lang="ts">
import AppContent from '@/components/AppContent.vue'
import AppShell from '@/components/AppShell.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import AppSidebarHeader from '@/components/AppSidebarHeader.vue'
import ChatLauncher from '@/components/ChatLauncher.vue';
import type { BreadcrumbItemType } from '@/types'

interface Props {
  breadcrumbs?: BreadcrumbItemType[];
  unreadCount?: number;
  inventoryAlertCount?: number; // New prop
  myTasksCount?: number;
  myTodoCount?: number;
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
  unreadCount: 0,
  inventoryAlertCount: 0, // Default value
  myTasksCount: 0,
  myTodoCount: 0,
});
</script>

<template>
  <AppShell variant="sidebar">
    <AppSidebar 
      class="print:hidden" 
      :unread-count="unreadCount" 
      :inventory-alert-count="inventoryAlertCount" 
      :my-tasks-count="myTasksCount" 
      :my-todo-count="myTodoCount"
    /> 
    
    <AppContent variant="sidebar">
      <AppSidebarHeader :breadcrumbs="breadcrumbs" class="print:hidden" />
      <slot />
    </AppContent>
    <ChatLauncher class="print:hidden" />
  </AppShell>
</template>