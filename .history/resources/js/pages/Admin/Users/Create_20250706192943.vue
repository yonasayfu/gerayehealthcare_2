<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Form from './Form.vue'; // We will use the reusable form

const form = useForm({
  // User fields
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  // Staff fields
  first_name: '',
  last_name: '',
  position: '',
  department: '',
  phone: '',
  hire_date: '',
});

const submit = () => {
  form.post(route('admin.users.store'));
};
</script>

<template>
  <Head title="Add New Staff User" />
  <AppLayout>
    <div class="p-6 space-y-6">
       <!-- Header Card -->
       <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Staff User</h1>
        <p class="text-sm text-muted-foreground">This will create both a user account and a linked staff profile.</p>
      </div>
      
      <!-- Form Card -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <Form :form="form" @submit="submit" />
        
        <!-- Actions -->
        <div class="flex justify-end space-x-4 border-t dark:border-gray-700 pt-6 mt-6">
          <Link :href="route('admin.users.index')" class="px-4 py-2 border rounded-md text-sm font-medium">Cancel</Link>
          <button @click="submit" :disabled="form.processing" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium disabled:opacity-50">
            {{ form.processing ? 'Creating...' : 'Create Staff User' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
