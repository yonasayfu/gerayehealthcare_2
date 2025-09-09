<script setup lang="ts">
const props = withDefaults(defineProps<{
  title: string
  subtitle?: string
}>(), {
  subtitle: ''
})
</script>

<template>
  <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
    <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
    <div class="liquidGlass-content flex items-center justify-between p-6">
      <div class="print:hidden">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ props.title }}</h1>
        <p v-if="props.subtitle" class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ props.subtitle }}</p>
      </div>
      <div class="flex items-center gap-2">
        <slot name="actions" />
      </div>
    </div>
  </div>
</template>
