<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  patients: Array<{ id: number; full_name: string }>,
  staff: Array<{ id: number; first_name: string; last_name: string }>,
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Assignments', href: route('admin.assignments.index') },
  { title: 'Create', href: route('admin.assignments.create') },
]

const form = useForm({
  patient_id: '',
  staff_id: '',
  shift_start: '',
  shift_end: '',
  status: 'Assigned', // Default status
})

function submit() {
  // THIS IS THE FIX: Convert local datetime strings to UTC before submitting
  form.transform(data => ({
    ...data,
    shift_start: data.shift_start ? new Date(data.shift_start).toISOString() : null,
    shift_end: data.shift_end ? new Date(data.shift_end).toISOString() : null,
  })).post(route('admin.assignments.store'))
}
</script>

<template>
  <Head title="Create Assignment" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create New Assignment</h1>
        <p class="text-sm text-muted-foreground">Fill in the form to register a new assignment.</p>
      </div>

      <!-- Form Card -->
      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 p-6 shadow-sm">
        <Form :form="form" :patients="props.patients" :staff="props.staff" @submit="submit" />
      </div>

      <!-- Actions -->
      <div class="flex justify-end space-x-3 mt-6 pt-6 border-t dark:border-gray-700">
        <Link
          :href="route('admin.assignments.index')"
          class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none"
        >
          Cancel
        </Link>
        <button
          @click="submit"
          :disabled="form.processing"
          class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white font-semibold rounded-md shadow-sm"
        >
          {{ form.processing ? 'Saving...' : 'Save Assignment' }}
        </button>
      </div>
    </div>
  </AppLayout>
</template>
