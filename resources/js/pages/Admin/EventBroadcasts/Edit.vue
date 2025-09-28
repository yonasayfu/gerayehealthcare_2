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
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Event</label>
              <select v-model="form.event_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
                <option value="">Select Event</option>
                <option v-for="ev in events" :key="ev.id" :value="ev.id">{{ ev.title ?? ev.name ?? ('Event #' + ev.id) }}</option>
              </select>
              <span class="text-red-500 text-xs" v-if="form.errors.event_id">{{ form.errors.event_id }}</span>
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
              <span class="text-red-500 text-xs" v-if="form.errors.channel">{{ form.errors.channel }}</span>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Message</label>
              <textarea v-model="form.message" rows="4" placeholder="Broadcast message..." class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"></textarea>
              <span class="text-red-500 text-xs" v-if="form.errors.message">{{ form.errors.message }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Sent By (Staff)</label>
              <select v-model="form.sent_by_staff_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
                <option value="">Select Staff</option>
                <option v-for="s in staff" :key="s.id" :value="s.id">{{ s.full_name ?? s.name ?? ('Staff #' + s.id) }}</option>
              </select>
              <span class="text-red-500 text-xs" v-if="form.errors.sent_by_staff_id">{{ form.errors.sent_by_staff_id }}</span>
            </div>
          </div>

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.event-broadcasts.index')" class="btn-glass btn-glass-sm">Cancel</Link>
            <button type="submit" :disabled="form.processing" class="btn-glass btn-glass-sm">
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
