<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
}>();
</script>

<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap -mb-1">
      <template v-for="(link, key) in links">
        <div
          v-if="link.url === null"
          :key="key"
          class="mb-1 mr-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-blue-500 hover:text-white focus:border-blue-500 focus:text-blue-500 dark:border-gray-700 dark:hover:bg-blue-900 dark:focus:border-blue-700 dark:focus:text-blue-400"
          :class="{
            'bg-blue-600 text-white dark:bg-blue-700': link.active, // <-- ACTIVE STATE STYLING
            'text-gray-700 dark:text-gray-300': !link.active,
            'bg-white dark:bg-gray-800': !link.active,
          }"
          :href="link.url"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>