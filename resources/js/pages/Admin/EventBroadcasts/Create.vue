<script setup>
import FormActions from '@/components/FormActions.vue'
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue'

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
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Event</label>
              <select v-model="form.event_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
                <option value="">Select Event</option>
                <option v-for="ev in events" :key="ev.id" :value="ev.id">{{ ev.title ?? ev.name ?? ('Event #' + ev.id) }}</option>
              </select>
              <InputError class="mt-1" :message="form.errors?.event_id" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Channel</label>
              <select v-model="form.channel" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
                <option value="">Select Channel</option>
                <option value="Email">Email</option>
                <option value="SMS">SMS</option>
                <option value="WhatsApp">WhatsApp</option>
                <option value="Telegram">Telegram</option>
                <option value="In-App">In-App</option>
                <option value="Push">Push</option>
              </select>
              <InputError class="mt-1" :message="form.errors?.channel" />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Message</label>
              <textarea v-model="form.message" rows="4" placeholder="Broadcast message..." class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"></textarea>
              <InputError class="mt-1" :message="form.errors?.message" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Sent By (Staff)</label>
              <select v-model="form.sent_by_staff_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
                <option value="">Select Staff</option>
                <option v-for="s in staff" :key="s.id" :value="s.id">{{ s.full_name ?? s.name ?? ('Staff #' + s.id) }}</option>
              </select>
              <InputError class="mt-1" :message="form.errors?.sent_by_staff_id" />
            </div>
          </div>

          <FormActions
            :cancel-href="route('admin.event-broadcasts.index')"
            cancel-text="Cancel"
            submit-text="Create Event Broadcast"
            :processing="form.processing"
          />
        </form>
      </div>
    </div>
  </AppLayout>
</template>
