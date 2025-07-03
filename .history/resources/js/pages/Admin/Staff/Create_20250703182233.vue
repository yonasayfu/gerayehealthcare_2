<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Form from './Form.vue'
import { toast } from 'vue-sonner'

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  position: '',
  department: '',
  status: 'Active',
  hire_date: '',
  photo: null,
})

const onSubmit = () => {
  form.post(route('staff.store'), {
    preserveScroll: true,
    onSuccess: () => toast.success('Staff created successfully!'),
    onError: () => toast.error('There was an error submitting the form.'),
  })
}

const handleFileChange = (event) => {
  form.photo = event.target.files[0]
}
</script>

<template>
  <div class="p-6 max-w-4xl mx-auto">
    <div class="mb-6">
      <h1 class="text-2xl font-semibold text-gray-800">Add New Staff</h1>
      <p class="text-sm text-gray-500">Complete the form to register a new staff member.</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
      <Form :form="form" :errors="form.errors" @file-change="handleFileChange" />

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
          Save
        </button>
      </div>
    </div>
  </div>
</template>
