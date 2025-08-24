<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    events: Array,
});

const operators = ['=', '!=', '>', '>=', '<', '<=', 'LIKE', 'ILIKE', 'IN', 'NOT IN'];

const form = useForm({
    event_id: '',
    criteria_title: '',
    operator: '',
    value: '',
});

const submit = () => {
    form.post(route('admin.eligibility-criteria.store'));
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Eligibility Criteria', href: route('admin.eligibility-criteria.index') },
    { title: 'Create', href: route('admin.eligibility-criteria.create') },
];
</script>

<template>
  <Head title="Create New Eligibility Criteria" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Eligibility Criteria</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a eligibility criteria.</p>
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
              <span class="text-red-500 text-xs" v-if="form.errors?.event_id">{{ form.errors.event_id }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Criteria Title</label>
              <input v-model="form.criteria_title" type="text" placeholder="e.g., Age, City" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
              <span class="text-red-500 text-xs" v-if="form.errors?.criteria_title">{{ form.errors.criteria_title }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Operator</label>
              <select v-model="form.operator" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
                <option value="">Select Operator</option>
                <option v-for="op in operators" :key="op" :value="op">{{ op }}</option>
              </select>
              <span class="text-red-500 text-xs" v-if="form.errors?.operator">{{ form.errors.operator }}</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Value</label>
              <input v-model="form.value" type="text" placeholder="e.g., 18, Addis Ababa" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800" />
              <span class="text-red-500 text-xs" v-if="form.errors?.value">{{ form.errors.value }}</span>
            </div>
          </div>

          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.eligibility-criteria.index')" class="btn-glass btn-glass-sm">Cancel</Link>
            <button type="submit" :disabled="form.processing" class="btn-glass btn-glass-sm">
              {{ form.processing ? 'Creating...' : 'Create Eligibility Criteria' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
