// In resources/js/Pages/Admin/VisitServices/Edit.vue

<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3'
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

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Visit Services', href: route('admin.visit-services.index') },
  { title: `Edit Visit #${props.visitService.id}`, href: route('admin.visit-services.edit', props.visitService.id) },
];

const form = useForm({
  _method: 'PUT',
  patient_id: props.visitService.patient_id,
  staff_id: props.visitService.staff_id,
  scheduled_at: formattedDate,
  status: props.visitService.status,
  visit_notes: props.visitService.visit_notes || '',
  // This line is the fix:
  service_description: props.visitService.service_description || '', 
  prescription_file: null,
  vitals_file: null,
});

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
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Visit Service
            </h3>
            <Link :href="route('admin.visit-services.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <VisitServiceForm :form="form" :patients="props.patients" :staff="props.staff" :visit-service="props.visitService" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <div class="flex flex-wrap items-center gap-2">
              <Link :href="route('admin.visit-services.index')" class="btn btn-outline">Cancel</Link>
              <button @click="submit" :disabled="form.processing" class="btn btn-primary" type="submit">
                {{ form.processing ? 'Saving...' : 'Update Visit' }}
              </button>
              <button
                class="btn btn-danger ml-auto"
                @click="() => { if (confirm('Delete this visit service?')) { router.delete(route('admin.visit-services.destroy', props.visitService.id)) } }"
              >
                Delete
              </button>
            </div>
        </div>

    </div>
  </AppLayout>
</template>