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
  <Head title="Edit Visit Service" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Visit Service</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update visit service information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <VisitServiceForm :form="form" v-bind="$props" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.visit-services.index')"
              class="btn-glass btn-glass-sm"
            >
              Cancel
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>