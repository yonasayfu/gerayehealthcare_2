<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import VisitServiceForm from './Form.vue'

const props = defineProps<{
  patients: Array<{ id: number; full_name: string }>
  staff: Array<{ id: number; first_name: string; last_name: string }>
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Visit Services', href: route('admin.visit-services.index') },
  { title: 'Schedule New Visit', href: route('admin.visit-services.create') },
]

const form = useForm({
  patient_id: '',
  staff_id: '',
  scheduled_at: '',
  status: 'Pending',
  visit_notes: '',
})

function submit() {
  form.post(route('admin.visit-services.store'))
}
</script>

<template>
  <Head title="Schedule New Visit" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="max-w-3xl mx-auto">
        <div class="rounded-lg bg-white dark:bg-gray-900 p-6 shadow-sm">
          <h1 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Schedule a New Visit</h1>
          <VisitServiceForm :form="form" :patients="props.patients" :staff="props.staff" @submit="submit" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>