<script setup lang="ts">
import { computed } from 'vue'
import AuthSimpleLayout from '@/layouts/auth/AuthSimpleLayout.vue'
import AuthCardLayout from '@/layouts/auth/AuthCardLayout.vue'
import AuthSplitLayout from '@/layouts/auth/AuthSplitLayout.vue'
import AuthGlassLayout from '@/layouts/auth/AuthGlassLayout.vue'

const props = withDefaults(defineProps<{
  title?: string;
  description?: string;
  /**
   * Choose auth layout variant without breaking existing pages.
   * - 'simple' (default)
   * - 'card'
   * - 'split'
   */
  variant?: 'glass' | 'simple' | 'card' | 'split'
}>(), {
  variant: 'glass'
})

const ResolvedLayout = computed(() => {
  switch (props.variant) {
    case 'glass':
      return AuthGlassLayout
    case 'card':
      return AuthCardLayout
    case 'split':
      return AuthSplitLayout
    default:
      return AuthGlassLayout
  }
})
</script>

<template>
  <component :is="ResolvedLayout" :title="title" :description="description">
    <slot />
  </component>
</template>
