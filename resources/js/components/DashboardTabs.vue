<script setup lang="ts">
import { ref, defineEmits } from 'vue'
// Button not used here

const tabs = ['Overview', 'Analytics', 'Reports', 'Notifications']
const activeTab = ref('Overview')

const emit = defineEmits(['tab-change'])

function selectTab(tab: string) {
  activeTab.value = tab
  emit('tab-change', tab)
}
</script>

<template>
  <div class="border-b">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
      <button
        v-for="tab in tabs"
        :key="tab"
        @click="selectTab(tab)"
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
</template>
