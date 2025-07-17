<script setup lang="ts">
import { ref, defineEmits } from 'vue'
import { Button } from '@/components/ui/button'

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
      >
        {{ tab }}
      </button>
    </nav>
  </div>
</template>
