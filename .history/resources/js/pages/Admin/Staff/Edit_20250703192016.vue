<script setup lang="ts">
import { useForm, router, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import { ref } from 'vue'
// import { toast } from 'vue-sonner'

const props = defineProps({
  staff: Object,
})

const breadcrumbs = [
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
  photo: null,
})

const onSubmit = () => {
  form.post(route('staff.update', props.staff.id), {
    preserveScroll: true,
    // onSuccess: () => toast.success('Staff updated successfully!'),
    // onError: () => toast.error('There was an error updating the staff.'),
    forceFormData: true,
  })
}

const handleFileChange = (event) => {
  form.photo = event.target.files[0]
}
</script>

<template>
  <Head title="Edit Staff" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 max-w-4xl mx-auto space-y-6">
      <div class="bg-muted/40 p-4 rounded-lg shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Staff</h1>
        <p class="text-sm text-muted-foreground">Update staff details as needed below.</p>
      </div>

      <div class="bg-white dark:bg-background shadow rounded-lg p-6">
        <Form :form="form" :errors="form.errors" :existing-photo="props.staff.photo" @file-change="handleFileChange" />

        <div class="mt-6 flex justify-end space-x-4">
          <button
            @click="router.visit(route('staff.index'))"
            type="button"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 dark:bg-gray-800 dark:text-white"
          >
            Cancel
          </button>
          <button
            @click="onSubmit"
            type="button"
            class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700"
          >
            Update
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
