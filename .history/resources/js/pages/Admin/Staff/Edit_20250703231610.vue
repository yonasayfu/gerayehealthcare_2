<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
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
    photo: string | null // Path to the existing photo
  }
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Staff', href: '/dashboard/staff' },
  { title: 'Edit', href: `/dashboard/staff/${props.staff.id}/edit` },
]

const form = useForm({
  first_name: props.staff.first_name,
  last_name: props.staff.last_name,
  email: props.staff.email,
  phone: props.staff.phone,
  position: props.staff.position,
  department: props.staff.department,
  status: props.staff.status,
  hire_date: props.staff.hire_date,
  photo: null as File | null, // To hold the new photo file for upload
});

function submit() {
  form.post(`/dashboard/staff/${props.staff.id}`, {
    _method: 'put', // Crucial for spoofing PUT with file uploads
    onSuccess: () => {
      form.photo = null;
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors);
    },
  });
}

// Function to handle file input change
function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    form.photo = target.files[0];
  }
}
</script>

<template>
  <Head title="Edit Staff" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Staff</h1>
        <p class="text-sm text-muted-foreground">Update staff details as necessary.</p>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-background p-6 shadow-sm space-y-6">
        <Form :form="form" :errors="form.errors" :existingPhoto="staff.photo" @file-change="handleFileChange" />

        <div class="flex justify-end space-x-3">
          <Link
            href="/dashboard/staff"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-muted/30 transition"
          >
            Cancel
          </Link>
          <button
            @click="submit"
            :disabled="form.processing"
            class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white rounded-md text-sm font-medium transition"
          >
            {{ form.processing ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>