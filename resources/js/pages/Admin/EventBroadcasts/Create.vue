<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    events: { type: Array, default: () => [] }, // [{id, title}]
    staff: { type: Array, default: () => [] },  // [{id, first_name, last_name}]
});

const form = useForm({
    event_id: '',
    channel: '',
    message: '',
    sent_by_staff_id: '',
});

const submit = () => {
    form.post(route('admin.event-broadcasts.store'));
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Event Broadcasts', href: route('admin.event-broadcasts.index') },
    { title: 'Create', href: route('admin.event-broadcasts.create') },
];
</script>

<template>
  <Head title="Create New Event Broadcast" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Event Broadcast</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a event broadcast.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.event-broadcasts.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Event Broadcast' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
