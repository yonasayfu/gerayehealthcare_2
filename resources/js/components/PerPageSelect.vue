<script setup lang="ts">
import { computed, watch } from 'vue'
import { useAttrs } from 'vue'

type OptionInput = number | { value: number | string; label?: string }

type NormalizedOption = { value: number; label: string }

const props = withDefaults(defineProps<{
  modelValue: number | string | null | undefined
  options?: OptionInput[]
  id?: string
}>(), {
  options: () => [5, 10, 25, 50, 100],
  id: undefined,
})

const emit = defineEmits<{ 'update:modelValue': [number] }>()
const attrs = useAttrs()

const normalizedOptions = computed<NormalizedOption[]>(() => props.options
  .map((opt) => {
    if (typeof opt === 'number') {
      return { value: opt, label: String(opt) }
    }
    const value = Number(opt.value)
    return { value, label: opt.label ?? String(value) }
  })
  .filter((opt) => Number.isFinite(opt.value)))

const baseClasses = 'per-page-select rounded-md border border-gray-300 bg-white text-gray-900 text-sm px-2 py-1 focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-600 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700'

const filteredAttrs = computed(() => {
  const { class: _class, id: _id, ...rest } = attrs as Record<string, unknown>
  return rest
})

const mergedClass = computed(() => {
  const attrClass = (attrs as Record<string, unknown>).class
  return [baseClasses, attrClass]
})

const resolvedValue = computed(() => {
  const value = props.modelValue
  if (value === null || value === undefined || value === '') {
    return normalizedOptions.value[0]?.value
  }
  const numeric = Number(value)
  return normalizedOptions.value.some((opt) => opt.value === numeric)
    ? numeric
    : normalizedOptions.value[0]?.value
})

watch(resolvedValue, (val) => {
  if (val !== undefined && val !== null) {
    const numeric = Number(props.modelValue)
    if (!Number.isFinite(numeric) || numeric !== val) {
      emit('update:modelValue', val)
    }
  }
}, { immediate: true })

function onChange(event: Event) {
  const target = event.target as HTMLSelectElement
  emit('update:modelValue', Number(target.value))
}
</script>

<template>
  <select
    :id="id"
    :value="String(resolvedValue ?? '')"
    :class="mergedClass"
    v-bind="filteredAttrs"
    @change="onChange"
  >
    <option
      v-for="option in normalizedOptions"
      :key="option.value"
      :value="option.value"
    >
      {{ option.label }}
    </option>
  </select>
</template>
