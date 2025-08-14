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
  <div v-if="links.length > 1">
    <div class="flex flex-wrap items-center gap-2">
      <template v-for="(link, key) in links">
        <span
          v-if="link.url === null"
          :key="`span-${key}`"
          class="btn btn-outline opacity-50 cursor-not-allowed select-none"
          v-html="link.label"
        />
        <Link
          v-else
          :key="`link-${key}`"
          class="btn"
          :class="link.active ? 'btn-info' : 'btn-outline'"
          :href="link.url"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>
