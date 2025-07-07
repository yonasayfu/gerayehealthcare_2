<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import VisitServiceForm from './Form.vue'
import { format } from 'date-fns'

const props = defineProps<{
  visitService: any
  patients: Array<{ id: number; full_name: string }>
  staff: Array<{ id: number; first_name: string; last_name: string }>
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Visit Services', href: route('admin.visit-services.index') },
  { title: `Edit Visit #${props.visitService.id}`, href: '#' },
]

// Format the date for the datetime-local input, which requires 'YYYY-MM-DDTHH:mm'
const formattedDate = props.visitService.scheduled_at
  ? format(new Date(props.visitService.scheduled_at), "yyyy-MM-dd'T'HH:mm")
  : ''

const form = useForm({
  patient_id: props.visitService.patient_id,
  staff_id: props.visitService.staff_id,
  scheduled_at: formattedDate,
  status: props.visitService.status,
  visit_notes: props.visitService.visit_notes || '',
})

function submit() {
  form.put(route('admin.visit-services.update', props.visitService.id))
}
</script>

<template>
  <Head :title="`Edit Visit #${visitService.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="max-w-3xl mx-auto">
        <div class="rounded-lg bg-white dark:bg-gray-900 p-6 shadow-sm">
          <h1 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Edit Visit Service</h1>
          <VisitServiceForm :form="form" :patients="props.patients" :staff="props.staff" @submit="submit" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>