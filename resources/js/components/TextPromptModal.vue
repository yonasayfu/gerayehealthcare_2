<template>
  <div v-if="open" class="fixed inset-0 z-[70] flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" @click="onCancel" aria-hidden="true"></div>
    <div role="dialog" aria-modal="true" :aria-labelledby="labelId"
         class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
      <h3 :id="labelId" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
        {{ title }}
      </h3>
      <p v-if="description" class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ description }}</p>
      <div class="mt-4">
        <label v-if="label" class="block text-sm font-medium text-gray-900 dark:text-white">{{ label }}</label>
        <textarea v-model="internalValue" rows="4"
                  class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700"></textarea>
      </div>
      <div class="mt-6 flex justify-end gap-2">
        <button type="button" @click="onCancel"
                class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
          {{ cancelText }}
        </button>
        <button type="button" @click="onConfirm"
                class="px-4 py-2 rounded-md bg-cyan-600 text-white hover:bg-cyan-700">
          {{ confirmText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'

const props = withDefaults(defineProps<{
  open: boolean
  modelValue?: string
  title?: string
  description?: string
  label?: string
  confirmText?: string
  cancelText?: string
  idSuffix?: string
}>(), {
  modelValue: '',
  title: 'Add Notes',
  description: '',
  label: 'Notes',
  confirmText: 'Confirm',
  cancelText: 'Cancel',
  idSuffix: 'text-prompt-modal'
})

const emit = defineEmits<{
  (e: 'update:open', v: boolean): void
  (e: 'update:modelValue', v: string): void
  (e: 'confirm', v: string): void
  (e: 'cancel'): void
}>()

const labelId = computed(() => `modal-title-${props.idSuffix}`)
const internalValue = ref(props.modelValue || '')

watch(() => props.modelValue, (v) => { internalValue.value = v || '' })

function onCancel() {
  emit('update:open', false)
  emit('cancel')
}

function onConfirm() {
  emit('confirm', internalValue.value)
}
</script>

