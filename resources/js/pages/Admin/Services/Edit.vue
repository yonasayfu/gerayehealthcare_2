<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ServiceForm from './Form.vue';
import FormActions from '@/components/FormActions.vue'
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
  category: props.service.category,
  duration: props.service.duration,
  price: props.service.price,
  is_active: Boolean(props.service.is_active),
});

const submit = () => {
  form.put(route('admin.services.update', props.service.id));
};
</script>

<template>
  <Head title="Edit Service" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Service</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update service information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <ServiceForm :form="form" v-bind="$props" />

          <FormActions :cancel-href="route('admin.services.index')" submit-text="Save Changes" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
</template>
