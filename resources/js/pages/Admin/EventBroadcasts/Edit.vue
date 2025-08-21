<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    eventBroadcast: { type: Object, required: true },
    events: { type: Array, default: () => [] },
    staff: { type: Array, default: () => [] },
});

const form = useForm({
    event_id: props.eventBroadcast.event_id,
    channel: props.eventBroadcast.channel,
    message: props.eventBroadcast.message,
    sent_by_staff_id: props.eventBroadcast.sent_by_staff_id,
});

const submit = () => {
    form.put(route('admin.event-broadcasts.update', props.eventBroadcast.id));
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Event Broadcasts', href: route('admin.event-broadcasts.index') },
    { title: 'Edit', href: route('admin.event-broadcasts.edit', props.eventBroadcast.id) },
];
</script>

<template>
  <Head title="Edit Event Broadcast" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Event Broadcast</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update event broadcast information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.event-broadcasts.index')"
              class="btn-glass btn-glass-sm"
            >
              Cancel
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
