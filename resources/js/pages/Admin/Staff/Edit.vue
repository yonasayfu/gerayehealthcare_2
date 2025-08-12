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
    photo: string | null
    hourly_rate: string | number | null // Corrected type definition
  },
  departments: string[]
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
  hourly_rate: props.staff.hourly_rate || '', // Added hourly_rate to the form
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
  <Head title="Edit Staff" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Staff
            </h3>
            <Link :href="route('admin.staff.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <Form :form="form" :existingPhoto="staff.photo" :departments="departments" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <button @click="submit" :disabled="form.processing" class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
        </div>

    </div>
  </AppLayout>
</template>