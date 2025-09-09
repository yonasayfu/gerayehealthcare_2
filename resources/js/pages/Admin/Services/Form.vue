<script setup lang="ts">
import { ref, onMounted, watchEffect } from 'vue'
import InputError from '@/components/InputError.vue'

interface LocalErrors {
  // Add any local validation errors if needed
}

const props = defineProps<{
  form: any,
  localErrors?: LocalErrors
}>()

const emit = defineEmits(['submit'])

// Define options for dropdowns here
const categories = ref<string[]>([
  'Nursing Care',
  'Physical Therapy',
  'Occupational Therapy',
  'Medical Consultation',
  'Home Health Aide',
  'Other',
])

// Ensure current category is available in dropdown during edit
onMounted(() => {
  const current = (props.form as any)?.category
  if (current && !categories.value.includes(current)) {
    categories.value.unshift(current)
  }
})

// Also react if form.category changes later (e.g., async load)
watchEffect(() => {
  const current = (props.form as any)?.category
  if (current && !categories.value.includes(current)) {
    categories.value.unshift(current)
  }
})
</script>

<template>
  <div>
    <div class="border-b border-gray-900/10 pb-12">
      <h2 class="text-base font-semibold text-gray-900 dark:text-white">Service Information</h2>
      <p class="mt-1 text-sm text-muted-foreground">
        Provide accurate service details for patient care.
      </p>

      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Service Name</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.name"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <InputError class="mt-1" :message="form.errors.name" />
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Category</label>
          <div class="mt-2">
            <select
              v-model="form.category"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            >
              <option value="">Select category</option>
              <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
            </select>
            <InputError class="mt-1" :message="form.errors.category" />
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Price ($)</label>
          <div class="mt-2">
            <input
              type="number"
              step="0.01"
              min="0"
              v-model="form.price"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <InputError class="mt-1" :message="form.errors.price" />
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Duration (minutes)</label>
          <div class="mt-2">
            <input
              type="number"
              min="1"
              v-model="form.duration"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <InputError class="mt-1" :message="form.errors.duration" />
          </div>
        </div>

        <div class="col-span-full">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Description</label>
          <div class="mt-2">
            <textarea
              v-model="form.description"
              rows="3"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            ></textarea>
            <InputError class="mt-1" :message="form.errors.description" />
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
          <div class="mt-2">
            <select
              v-model="form.is_active"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            >
              <option :value="true">Active</option>
              <option :value="false">Inactive</option>
            </select>
            <InputError class="mt-1" :message="form.errors.is_active" />
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
