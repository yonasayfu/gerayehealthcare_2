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
          <Form :form="form" v-bind="$props" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.event-recommendations.index')"
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
