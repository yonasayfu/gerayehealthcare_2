<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import { Save } from 'lucide-vue-next'
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
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Assignment</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create an assignment.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" :patients="props.patients" :staff="props.staff" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.assignments.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              <Save class="icon" />
              {{ form.processing ? 'Creating...' : 'Save Assignment' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
