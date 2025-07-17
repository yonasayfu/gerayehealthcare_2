<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue'
import { ToastRoot, type ToastRootEmits, type ToastRootProps, useForwardProps } from 'radix-vue'
import { type ToastVariants, toastVariants } from '.'
import { cn } from '@/lib/utils'

const props = defineProps<ToastRootProps & {
  class?: HTMLAttributes['class']
  variant?: ToastVariants['variant']
}>()

const emits = defineEmits<ToastRootEmits>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props
  return delegated
})

const forwarded = useForwardProps(delegatedProps)
</script>

<template>
  <ToastRoot
    v-bind="forwarded"
    :class="cn(toastVariants({ variant }), props.class)"
    @update:open="emits('update:open', $event)"
  >
    <slot />
  </ToastRoot>
