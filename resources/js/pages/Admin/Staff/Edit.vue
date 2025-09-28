<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType } from '@/types'

const props = defineProps<{
  staff: {
    id: number
    first_name: string
    last_name: string
    email: string
    phone: string | null
    position: string | null
    department: string | null
    status: 'Active' | 'Inactive'
    hire_date: string | null
    photo: string | null
    photo_url: string | null
    hourly_rate: string | number | null // Corrected type definition
  },
  departments: string[],
  positions: string[]
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('admin.staff.index') },
  { title: 'Staff', href: route('admin.staff.index') },
  { title: 'Edit', href: route('admin.staff.edit', { staff: props.staff.id }) },
]

const form = useForm({
  _method: 'PUT', // Method spoofing for PUT request
  first_name: props.staff.first_name,
  last_name: props.staff.last_name,
  email: props.staff.email,
  phone: props.staff.phone,
  position: props.staff.position,
  department: props.staff.department,
  status: props.staff.status,
  hire_date: props.staff.hire_date,
  hourly_rate: props.staff.hourly_rate ?? null, // Ensure hourly_rate is null if not set
  photo: null as File | null,
});

function submit() {
  // Use form.post for multipart/form-data with method spoofing
  form.post(route('admin.staff.update', { staff: props.staff.id }), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      form.reset('photo'); // Only reset the photo field on success
    },
  });
}
</script>

<template>
  <Head title="Edit Staff Member" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Staff Member</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update staff member information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" :existing-photo="props.staff.photo" :departments="props.departments" :positions="props.positions" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.staff.index')"
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