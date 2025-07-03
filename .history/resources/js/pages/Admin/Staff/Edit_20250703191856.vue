<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Form from './Form.vue'
//import { toast } from 'vue-sonner'

const props = defineProps({
  staff: Object,
})

const form = useForm({
  first_name: props.staff.first_name,
  last_name: props.staff.last_name,
  email: props.staff.email,
  phone: props.staff.phone,
  position: props.staff.position,
  department: props.staff.department,
  status: props.staff.status,
  hire_date: props.staff.hire_date,
  photo: null, // Allow new file upload if changed
})

const onSubmit = () => {
  form.post(route('staff.update', props.staff.id), {
    preserveScroll: true,
    onSuccess: () => toast.success('Staff updated successfully!'),
    onError: () => toast.error('There was an error updating the staff.'),
    forceFormData: true,
  })
}

const handleFileChange = (event) => {
  form.photo = event.target.files[0]
}
</script>

<template>
  <div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
      <h1 class="text-2xl font-semibold text-gray-800">Edit Staff</h1>
      <p class="text-sm text-gray-500">Update staff details as needed below.</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
      <Form :form="form" :errors="form.errors" :existing-photo="props.staff.photo" @file-change="handleFileChange" />

      <div class="mt-6 flex justify-end space-x-4">
        <button
          @click="router.visit(route('staff.index'))"
          type="button"
          class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
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
</template>
