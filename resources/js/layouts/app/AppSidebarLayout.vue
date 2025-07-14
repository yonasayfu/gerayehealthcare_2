<script setup lang="ts">
import AppContent from '@/components/AppContent.vue'
import AppShell from '@/components/AppShell.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import AppSidebarHeader from '@/components/AppSidebarHeader.vue'
import FloatingChatButton from '@/components/FloatingChatButton.vue'; // Import the new component
import type { BreadcrumbItemType } from '@/types'

interface Props {
  breadcrumbs?: BreadcrumbItemType[]
}

withDefaults(defineProps<Props>(), {
  breadcrumbs: () => [],
})
</script>

<template>
  <AppShell variant="sidebar">
    <AppSidebar class="print:hidden" /> 
    
    <AppContent variant="sidebar">
      <AppSidebarHeader :breadcrumbs="breadcrumbs" class="print:hidden" />
      <slot />
    </AppContent>
    <FloatingChatButton /> <!-- Add the floating chat button here -->
  </AppShell>
</template>