<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

const props = withDefaults(defineProps<{
  cancelHref: string
  cancelText?: string
  submitText?: string
  processing?: boolean
}>(), {
  cancelText: 'Cancel',
  submitText: 'Save',
  processing: false,
})
</script>

<template>
  <div class="flex justify-end gap-2 pt-2">
    <Link :href="props.cancelHref" class="btn-glass btn-glass-sm">{{ props.cancelText }}</Link>
    <button type="submit" :disabled="props.processing" class="btn-glass btn-glass-sm">
      {{ props.processing ? 'Processing...' : props.submitText }}
    </button>
  </div>
  
</template>

