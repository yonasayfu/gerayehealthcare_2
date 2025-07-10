<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ServiceForm from './Form.vue';
import type { BreadcrumbItemType } from '@/types';

const props = defineProps<{
  service: any;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Services', href: route('admin.services.index') },
  { title: 'Edit', href: route('admin.services.edit', props.service.id) },
];

const form = useForm({
  name: props.service.name,
  description: props.service.description,
  price: props.service.price,
  is_active: props.service.is_active,
});

const submit = () => {
  form.put(route('admin.services.update', props.service.id));
};
</script>

<template>
  <Head title="Edit Service" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="max-w-2xl mx-auto">
        <div class="rounded-lg bg-white dark:bg-gray-900 p-6 shadow-sm">
          <h1 class="text-xl font-semibold mb-4">Edit Service</h1>
          <ServiceForm :form="form" />
          <div class="flex justify-end gap-4 mt-6">
            <Link :href="route('admin.services.index')" class="px-4 py-2 border rounded-md text-sm">Cancel</Link>
            <button @click="submit" :disabled="form.processing" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm disabled:opacity-50">
              {{ form.processing ? 'Saving...' : 'Update Service' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>