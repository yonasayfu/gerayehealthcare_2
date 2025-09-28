<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue'

const props = defineProps({
    participant: Object,
    events: { type: Array, default: () => [] },
    patients: { type: Array, default: () => [] },
});

const form = useForm({
    event_id: props.participant.event_id,
    patient_id: props.participant.patient_id,
    status: props.participant.status,
});

const submit = () => {
    form.put(route('admin.event-participants.update', props.participant.id));
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Event Participants', href: route('admin.event-participants.index') },
    { title: 'Edit', href: route('admin.event-participants.edit', props.participant.id) },
];
</script>

<template>
  <Head title="Edit Event Participant" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Event Participant</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update event participant information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" :events="props.events" :patients="props.patients" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.event-participants.index')"
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
