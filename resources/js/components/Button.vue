<template>
  <button
    :class="computedClass"
    :type="type"
    @click="$emit('click', $event)"
  >
    <slot />
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
  variant: { type: String, default: 'default' },
  size: { type: String, default: 'md' },
  type: { type: String, default: 'button' },
  full: { type: Boolean, default: false },
})

const base = 'inline-flex items-center gap-2 rounded font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-2'
const sizes = { sm: 'px-2 py-1 text-sm', md: 'px-3 py-1.5 text-sm', lg: 'px-4 py-2 text-base' }
const variants = {
  default: 'bg-white text-gray-900 border border-gray-200 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700',
  primary: 'bg-indigo-600 text-white border border-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:border-indigo-500',
  danger: 'bg-red-600 text-white border border-red-600 hover:bg-red-700 dark:bg-red-500 dark:border-red-500',
  ghost: 'bg-transparent text-gray-900 dark:text-gray-100',
}

const computedClass = computed(() => [
  base,
  sizes[props.size] || sizes.md,
  variants[props.variant] || variants.default,
  props.full ? 'w-full justify-center' : 'inline-flex',
  'shadow-sm'
].join(' '))
</script>
