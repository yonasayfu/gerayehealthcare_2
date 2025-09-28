<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    eventRecommendation: Object,
    events: Array,
    patients: Array,
});

const form = useForm({
    event_id: props.eventRecommendation.event_id,
    source_channel: props.eventRecommendation.source_channel,
    recommended_by_name: props.eventRecommendation.recommended_by_name,
    recommended_by_phone: props.eventRecommendation.recommended_by_phone,
    patient_name: props.eventRecommendation.patient_name,
    phone_number: props.eventRecommendation.phone_number,
    notes: props.eventRecommendation.notes,
    status: props.eventRecommendation.status,
});

const submit = () => {
    form.put(route('admin.event-recommendations.update', props.eventRecommendation.id));
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Event Recommendations', href: route('admin.event-recommendations.index') },
    { title: 'Edit', href: route('admin.event-recommendations.edit', props.eventRecommendation.id) },
];
</script>

<template>
  <Head title="Edit Event Recommendation" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Event Recommendation</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update event recommendation information below.</p>
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
              <span class="text-red-500 text-xs" v-if="form.errors?.event_id">{{ form.errors.event_id }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Source Channel</label>
              <select v-model="form.source_channel" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
                <option value="">Select Channel</option>
                <option value="Email">Email</option>
                <option value="SMS">SMS</option>
                <option value="WhatsApp">WhatsApp</option>
                <option value="Telegram">Telegram</option>
                <option value="In-App">In-App</option>
                <option value="Push">Push</option>
                <option value="Referral">Referral</option>
                <option value="Other">Other</option>
              </select>
              <span class="text-red-500 text-xs" v-if="form.errors?.source_channel">{{ form.errors.source_channel }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Recommended By (Name)</label>
              <input v-model="form.recommended_by_name" type="text" placeholder="Recommender name" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
              <span class="text-red-500 text-xs" v-if="form.errors?.recommended_by_name">{{ form.errors.recommended_by_name }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Recommended By (Phone)</label>
              <input v-model="form.recommended_by_phone" type="text" placeholder="Recommender phone" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
              <span class="text-red-500 text-xs" v-if="form.errors?.recommended_by_phone">{{ form.errors.recommended_by_phone }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Patient Name</label>
              <input v-model="form.patient_name" type="text" placeholder="Patient full name" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
              <span class="text-red-500 text-xs" v-if="form.errors?.patient_name">{{ form.errors.patient_name }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Patient Phone</label>
              <input v-model="form.phone_number" type="text" placeholder="Patient phone" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
              <span class="text-red-500 text-xs" v-if="form.errors?.phone_number">{{ form.errors.phone_number }}</span>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Notes</label>
              <textarea v-model="form.notes" rows="3" placeholder="Additional notes..." class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"></textarea>
              <span class="text-red-500 text-xs" v-if="form.errors?.notes">{{ form.errors.notes }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Status</label>
              <select v-model="form.status" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
              <span class="text-red-500 text-xs" v-if="form.errors?.status">{{ form.errors.status }}</span>
            </div>
          </div>

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.event-recommendations.index')" class="btn-glass btn-glass-sm">Cancel</Link>
            <button type="submit" :disabled="form.processing" class="btn-glass btn-glass-sm">
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
