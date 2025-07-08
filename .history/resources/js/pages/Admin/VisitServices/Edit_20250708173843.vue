// In resources/js/Pages/Admin/VisitServices/Edit.vue

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import VisitServiceForm from './Form.vue'
import { format } from 'date-fns'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  visitService: any
  patients: Array<{ id: number; full_name: string }>
  staff: Array<{ id: number; first_name: string; last_name: string }>
}>()

// ... breadcrumbs definition ...

const formattedDate = props.visitService.scheduled_at
  ? format(new Date(props.visitService.scheduled_at), "yyyy-MM-dd'T'HH:mm")
  : ''

const form = useForm({
  _method: 'PUT',
  patient_id: props.visitService.patient_id,
  staff_id: props.visitService.staff_id,
  scheduled_at: formattedDate,
  status: props.visitService.status,
  visit_notes: props.visitService.visit_notes || '',
  prescription_file: null,
  vitals_file: null,
})

// Add this transform function
form.transform((data) => ({
    ...data,
    scheduled_at: data.scheduled_at ? new Date(data.scheduled_at).toISOString() : null,
}))

function submit() {
  form.post(route('admin.visit-services.update', props.visitService.id), {
      forceFormData: true,
  });
}
</script>



<template>
  <Head :title="`Edit Visit #${visitService.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Visit Service</h1>
        <p class="text-sm text-muted-foreground">Update the details for visit #{{ visitService.id }}.</p>
      </div>
      <div class="rounded-lg border border-border bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <VisitServiceForm :form="form" :patients="props.patients" :staff="props.staff" :visit-service="props.visitService" />
        <div class="flex justify-end space-x-3">
          <Link :href="route('admin.visit-services.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition">Cancel</Link>
          <button @click="submit" :disabled="form.processing" class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-md text-sm font-medium transition">{{ form.processing ? 'Saving...' : 'Update Visit' }}</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>