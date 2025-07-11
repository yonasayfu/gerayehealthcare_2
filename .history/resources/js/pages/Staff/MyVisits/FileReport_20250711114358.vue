<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { format } from 'date-fns';

const props = defineProps<{
  visit: any;
  services: Array<{ id: number; name: string; price: string }>;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'My Visits', href: route('staff.my-visits.index') },
  { title: 'File Report' },
];

const form = useForm({
  service_id: props.visit.service_id || '',
  visit_notes: props.visit.visit_notes || '',
});

const submit = () => {
  form.post(route('staff.my-visits.report.store', { visit: props.visit.id }));
};

const formatDate = (dateString: string | null) => {
  if (!dateString) return 'N/A';
  return format(new Date(dateString), 'MMM dd, yyyy, hh:mm a');
};
</script>

<template>
  <Head title="File Visit Report" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="max-w-2xl mx-auto">
        <div class="rounded-lg bg-white dark:bg-gray-900 p-6 shadow-sm">
          <h1 class="text-xl font-semibold mb-2">File Report for Visit</h1>
          <p class="text-sm text-muted-foreground mb-6">
            For patient <span class="font-semibold">{{ visit.patient.full_name }}</span> on {{ formatDate(visit.scheduled_at) }}
          </p>

          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <label for="service_id" class="block text-sm font-medium">Service Provided</label>
              <select
                id="service_id"
                v-model="form.service_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                required
              >
                <option value="" disabled>-- Select a service --</option>
                <option v-for="service in services" :key="service.id" :value="service.id">
                  {{ service.name }}
                </option>
              </select>
              <div v-if="form.errors.service_id" class="text-sm text-red-600 mt-1">{{ form.errors.service_id }}</div>
            </div>

            <div>
              <label for="visit_notes" class="block text-sm font-medium">Visit Notes</label>
              <textarea
                id="visit_notes"
                v-model="form.visit_notes"
                rows="5"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600"
                placeholder="Add any relevant notes about the visit..."
              ></textarea>
              <div v-if="form.errors.visit_notes" class="text-sm text-red-600 mt-1">{{ form.errors.visit_notes }}</div>
            </div>

            <div class="flex justify-end gap-4 pt-4 border-t">
              <Link :href="route('staff.my-visits.index')" class="px-4 py-2 border rounded-md text-sm">Cancel</Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 bg-green-600 text-white rounded-md text-sm disabled:opacity-50"
              >
                {{ form.processing ? 'Saving...' : 'Save Report' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
