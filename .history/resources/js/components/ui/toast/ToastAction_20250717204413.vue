<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue'
import { ToastAction, type ToastActionProps } from 'radix-vue'
import { cn } from '@/lib/utils'

const props = defineProps<ToastActionProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props
  return delegated
})
</script>

<template>
