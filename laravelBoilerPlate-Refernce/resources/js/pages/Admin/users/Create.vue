<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Form from './Form.vue'; // We will use the reusable form

// Temporary departments list; replace with server-provided options if available
const departments = [
  'Nursing',
  'Physiotherapy',
  'Pharmacy',
  'Administration',
  'Finance',
  'IT',
  'HR',
];

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

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'User Management', href: route('admin.users.index') },
  { title: 'Create User', href: route('admin.users.create') },
];
</script>

<template>
  <Head title="Create New User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New User</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a user.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" :departments="departments" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.users.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create User' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>