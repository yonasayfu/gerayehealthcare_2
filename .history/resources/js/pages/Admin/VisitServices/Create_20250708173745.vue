// In resources/js/Pages/Admin/VisitServices/Create.vue

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import VisitServiceForm from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  patients: Array<{ id: number; full_name: string }>
  staff: Array<{ id: number; first_name: string; last_name: string }>
}>()

const breadcrumbs: BreadcrumbItemType[] = [
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
  prescription_file: null,
  vitals_file: null,
})

// Add this transform function
form.transform((data) => ({
    ...data,
    scheduled_at: data.scheduled_at ? new Date(data.scheduled_at).toISOString() : null,
}))

function submit() {
  form.post(route('admin.visit-services.store'))
}
</script>

// The <template> section remains unchanged.