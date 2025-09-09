<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
}>();

// Ensure pagination preserves current query (search, sort, filters, per_page, etc.)
const mergedLinks = computed(() => {
  try {
    const currentUrl = new URL(window.location.href);
    const currentParams = currentUrl.searchParams;

    return props.links.map((link) => {
      if (!link.url) return link;
      const target = new URL(link.url, window.location.origin);
      // Merge params from current URL that are not already present in the target
      currentParams.forEach((value, key) => {
        if (!target.searchParams.has(key)) {
          target.searchParams.set(key, value);
        }
      });
      // Normalize URL format for Inertia Link
      const normalized = target.pathname + (target.search ? target.search : '');
      return { ...link, url: normalized };
    });
  } catch (e) {
    // Fallback: return original links if URL API fails for any reason
    return props.links;
  }
});
</script>

<template>
  <div v-if="links.length > 1">
    <div class="flex flex-wrap items-center gap-2">
      <template v-for="(link, key) in mergedLinks">
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
