<template>
  <div class="flex items-center space-x-1">
    <button 
      v-for="link in links" 
      :key="link.label"
      @click="changePage(link.url)"
      :disabled="!link.url || link.active"
      :class="[
        'px-3 py-1 rounded text-sm',
        link.active 
          ? 'bg-blue-600 text-white' 
          : link.url 
            ? 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600' 
            : 'text-gray-400 dark:text-gray-500 cursor-not-allowed'
      ]"
      v-html="link.label"
    >
    </button>
  </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  links: Array<{
    url: string | null
    label: string
    active: boolean
  }>
}>()

function changePage(url: string | null) {
  if (!url) return
  router.get(url, {}, { preserveState: true })
}
</script>