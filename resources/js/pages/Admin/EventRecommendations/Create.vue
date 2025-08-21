<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    events: Array,
    patients: Array,
});

const form = useForm({
    event_id: '',
    source_channel: '',
    recommended_by_name: '',
    recommended_by_phone: '',
    patient_name: '',
    phone_number: '',
    notes: '',
    status: 'pending',
});

const submit = () => {
    form.post(route('admin.event-recommendations.store'));
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Event Recommendations', href: route('admin.event-recommendations.index') },
    { title: 'Create', href: route('admin.event-recommendations.create') },
];
</script>

<template>
  <Head title="Create New Event Recommendation" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Event Recommendation</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a event recommendation.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.event-recommendations.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Creating...' : 'Create Event Recommendation' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
