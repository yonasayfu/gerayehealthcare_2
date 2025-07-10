<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ServiceForm from './Form.vue';
import type { BreadcrumbItemType } from '@/types';

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Services', href: route('admin.services.index') },
  { title: 'Create', href: route('admin.services.create') },
];

const form = useForm({
  name: '',
  description: '',
  price: '',
  is_active: true,
});

const submit = () => {
  form.post(route('admin.services.store'));
};
</script>

<template>
  <Head title="Create Service" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="max-w-2xl mx-auto">
        <div class="rounded-lg bg-white dark:bg-gray-900 p-6 shadow-sm">
          <h1 class="text-xl font-semibold mb-4">Add New Service</h1>
          <ServiceForm :form="form" />
          <div class="flex justify-end gap-4 mt-6">
            <Link :href="route('admin.services.index')" class="px-4 py-2 border rounded-md text-sm">Cancel</Link>
            <button @click="submit" :disabled="form.processing" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm disabled:opacity-50">
              {{ form.processing ? 'Saving...' : 'Save Service' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>