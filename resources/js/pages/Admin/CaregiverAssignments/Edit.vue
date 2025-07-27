<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  assignment: {
    id: number;
    patient_id: number;
    staff_id: number;
    shift_start: string;
    shift_end: string;
    status: string;
  };
  patients: Array<{ id: number; full_name: string }>;
  staff: Array<{ id: number; first_name: string; last_name: string }>;
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Assignments', href: route('admin.assignments.index') },
  { title: 'Edit', href: route('admin.assignments.edit', props.assignment.id) },
]

const form = useForm({
  patient_id: props.assignment.patient_id,
  staff_id: props.assignment.staff_id,
  shift_start: props.assignment.shift_start,
  shift_end: props.assignment.shift_end,
  status: props.assignment.status,
})

function submit() {
  // THIS IS THE FIX: Convert local datetime strings to UTC before submitting
  form.transform(data => ({
    ...data,
    shift_start: data.shift_start ? new Date(data.shift_start).toISOString() : null,
    shift_end: data.shift_end ? new Date(data.shift_end).toISOString() : null,
  })).put(route('admin.assignments.update', props.assignment.id))
}
</script>

<template>
  <Head title="Edit Assignment" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Assignment
            </h3>
            <Link :href="route('admin.assignments.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <Form :form="form" :patients="props.patients" :staff="props.staff" @submit="submit" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <button @click="submit" :disabled="form.processing" class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
        </div>

    </div>
  </AppLayout>
</template>
