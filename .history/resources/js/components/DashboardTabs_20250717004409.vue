<script setup lang="ts">
import { ref } from 'vue'
import { Button } from '@/components/ui/button'

const tabs = ['Overview', 'Analytics', 'Reports', 'Notifications']
const activeTab = ref('Overview')
</script>

<template>
  <div class="border-b">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
      <button
        v-for="tab in tabs"
        :key="tab"
        @click="activeTab = tab"
        :class="[
          tab === activeTab
            ? 'border-primary text-primary'
            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border',
          'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
        ]"
      >
        {{ tab }}
      </button>
    </nav>
  </div>
